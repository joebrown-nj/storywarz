<?php

namespace App\Http\Controllers\Warz;

use App\Http\Controllers\Controller;
use App\Models\ShowRoundSummary;
use App\Models\Story;
use App\Models\User;
use App\Models\Warz;
use App\Models\WarzRound;
use App\Models\WarzRoundsVote;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class WarController extends Controller
{
    protected function showRoundSummary($warId): bool
    {
        return ShowRoundSummary::where('warz_id', $warId)->where('user_id', Auth::id())->exists();
    }

    protected function getWarRoundVotes($warRoundId)
    {
        return WarzRoundsVote::query()
            ->withVoteDetails()
            ->forRound($warRoundId)
            ->get();
    }

    protected function isThisRoundDoublePoints($storyNumber): bool
    {
        return $storyNumber > 4;
    }

    protected function checkForTie($warId): array
    {
        $warUsers = $this->getWarUsers($warId);
        if(!$warUsers[0]->score) {
            return [];
        }

        $tiedUsers = array_filter($warUsers->toArray(), function ($user) use ($warUsers) {
            return $user['score'] == $warUsers[0]->score;
        });

        return count($tiedUsers) > 1 ? $tiedUsers : [];
    }

    protected function warRoundCreate($warId): WarzRound | RedirectResponse | bool
    {
        $war = Warz::find($warId);
        if (!$war) {
            return redirect(route('warz', absolute: false))->withErrors(['War not found.']);
        }

        $tie = $this->checkForTie($warId);

        $rounds = WarzRound::where('warz_id', $warId)->where('complete', true)->count();
        if ($rounds >= env('WAR_MAX_ROUNDS') && empty($tie)) {
            $war->status = 'completed';
            $war->save();

            return false;
        }

        $story = $war->stories()->where('story_was_used', false)->inRandomOrder()->first();

        $story->story_was_used = true;
        $story->save();

        $nextRound = WarzRound::create([
            'warz_id' => $warId,
            'stories_id' => $story->id,
        ]);

        if ($rounds >= env('WAR_MAX_ROUNDS') && $tie) {
            $users = $this->getWarUsers($warId);
            foreach ($users as $user) {
                if ($user->score < $users[0]->score) {
                    WarzRoundsVote::create([
                        'warz_rounds_id' => $nextRound->id,
                        'user_id' => $user->id,
                        'warz_id' => $warId,
                        'voted_for_user_id' => 1,
                    ]);
                }
            }
        }

        return $nextRound;
    }

    protected function checkIfWarIsReadyToStart($id): void
    {
        $war = Warz::find($id);
        $warriors = $this->getWarUsers($id);

        foreach ($warriors as $warrior) {
            if (Story::countForWarrior($id, $warrior->id) < 3) {
                return;
            }
        }

        $war->status = 'active';
        $war->save();
    }

    protected function getWarUsers($warId)
    {
        return User::query()->inWarWithStats($warId)->get();
    }

    protected function validateEmail(string $email): bool
    {
        $inputValues['email'] = $email;
        $rules = ['email' => 'unique:users,email'];
        $validator = Validator::make($inputValues, $rules);

        return !$validator->fails();
    }

    protected function sendInvitations(Warz $war, Request $request): array
    {
        $users = [];
        foreach ($request->warrior_contacts as $key => $contact) {
            if (filter_var($contact, FILTER_VALIDATE_EMAIL)) {
                if (!$this->validateEmail($contact)) {
                    $users[] = User::where('email', $contact)->first();
                    continue;
                }

                $users[] = User::create([
                    'name' => $request->warrior_names[$key],
                    'email' => $contact,
                    'password' => Hash::make($request->password),
                ]);
                event(new Registered($users[count($users) - 1]));

                Mail::to($contact)->send(new WarriorInvitation($war, $users[count($users) - 1]));
                continue;
            }

            $tmpContact = 'tmp_' . time() . '@example.com';
            while (!$this->validateEmail($tmpContact)) {
                $tmpContact = 'tmp_' . time() . '@example.com';
            }

            $checkForUser = User::where('phone', $contact)->first();
            if ($checkForUser) {
                $users[] = $checkForUser;
                continue;
            }

            $users[] = User::create([
                'name' => $request->warrior_names[$key],
                'phone' => $contact,
                'email' => $tmpContact,
                'password' => Hash::make($tmpContact),
            ]);
            event(new Registered($users[count($users) - 1]));
        }

        return $users;
    }
}
