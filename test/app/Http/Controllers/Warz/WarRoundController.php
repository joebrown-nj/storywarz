<?php

namespace App\Http\Controllers\Warz;

use App\Models\ShowRoundSummary;
use App\Models\Warz;
use App\Models\WarzRound;
use App\Models\WarzRoundScore;
use App\Models\WarzRoundVote;
use App\Models\WarzWager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WarRoundController extends WarController
{
    public function vote(Request $request, Warz $war): RedirectResponse
    {
        $validated = $request->validate([
            'user' => ['required', 'integer'],
            'wager' => ['nullable', 'integer', 'min:0'],
        ]);

        $userId = Auth::id();
        $currentRound = WarzRound::query()
            ->currentWithStory($war->id)
            ->first();

        if (!$currentRound) {
            return redirect(route('warz.show', $war))
                ->withErrors(['There is no active round to vote on right now.']);
        }

        $alreadyVoted = DB::transaction(function () use ($war, $currentRound, $validated, $userId) {
            $hasVoted = WarzRoundVote::query()
                ->where('warz_rounds_id', $currentRound->round_id)
                ->where('user_id', $userId)
                ->exists();

            if ($hasVoted) {
                return true;
            }

            WarzRoundVote::create([
                'warz_rounds_id' => $currentRound->round_id,
                'user_id' => $userId,
                'warz_id' => $war->id,
                'voted_for_user_id' => $validated['user'],
            ]);

            $storyNumber = WarzRound::storyNumber($war->id);
            $tiedUsers = $this->checkForTie($war->id);

            if ($storyNumber >= $this->maxRounds() && !empty($tiedUsers) && array_key_exists('wager', $validated)) {
                $this->saveWager($war->id, $userId, $validated['wager']);
            }

            $this->finalizeRoundIfComplete(
                $war,
                (int) $currentRound->round_id,
                (int) $currentRound->user_id,
                $userId,
                $storyNumber,
                $tiedUsers
            );

            return false;
        });

        if ($alreadyVoted) {
            return redirect(route('warz.show', $war))
                ->withErrors(['You have already voted for this round.']);
        }

        return redirect(route('warz.show', $war))->with('status', 'Your vote is in!');
    }

    protected function finalizeRoundIfComplete(
        Warz $war,
        int $roundId,
        int $storyAuthor,
        int $actingUserId,
        int $storyNumber,
        array $tiedUsers
    ): void {
        $userCount = $this->getWarUsers($war->id)->count();
        $voteCount = WarzRoundVote::query()
            ->where('warz_rounds_id', $roundId)
            ->count();

        if ($userCount !== $voteCount) {
            return;
        }

        WarzRound::query()
            ->where('id', $roundId)
            ->update(['complete' => true]);

        ShowRoundSummary::create([
            'user_id' => $actingUserId,
            'warz_id' => $war->id,
        ]);

        $votes = WarzRoundVote::query()
            ->where('warz_rounds_id', $roundId)
            ->get();

        $this->createRoundScores($war->id, $roundId, $storyAuthor, $storyNumber, $tiedUsers, $votes);
        $this->warRoundCreate($war->id);
    }

    protected function createRoundScores(
        int $warId,
        int $roundId,
        int $storyAuthor,
        int $storyNumber,
        array $tiedUsers,
        Collection $votes
    ): void {
        $doublePoints = $this->isThisRoundDoublePoints($storyNumber);
        $eligibleTieUserIds = collect($tiedUsers)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
        $isTieRound = !empty($eligibleTieUserIds);
        $wagersByUserId = $isTieRound
            ? WarzWager::query()
                ->where('warz_id', $warId)
                ->whereIn('user_id', $eligibleTieUserIds)
                ->get()
                ->keyBy('user_id')
            : collect();
        $storyScore = 0;

        foreach ($votes as $vote) {
            if (!$this->isEligibleTieUser((int) $vote->user_id, $eligibleTieUserIds)) {
                continue;
            }

            if ((int) $vote->voted_for_user_id === $storyAuthor) {
                $score = $isTieRound
                    ? (int) optional($wagersByUserId->get($vote->user_id))->wager
                    : ($doublePoints ? 4 : 2);

                WarzRoundScore::create([
                    'user_id' => $vote->user_id,
                    'warz_rounds_id' => $roundId,
                    'warz_id' => $warId,
                    'score' => $score,
                ]);

                continue;
            }

            if ((int) $vote->user_id !== $storyAuthor && $this->isEligibleTieUser($storyAuthor, $eligibleTieUserIds)) {
                $storyScore += $doublePoints ? 2 : 1;
            }
        }

        if (!$this->isEligibleTieUser($storyAuthor, $eligibleTieUserIds)) {
            return;
        }

        WarzRoundScore::create([
            'user_id' => $storyAuthor,
            'warz_rounds_id' => $roundId,
            'warz_id' => $warId,
            'score' => $storyScore,
        ]);
    }

    protected function isEligibleTieUser(int $userId, array $eligibleTieUserIds): bool
    {
        if (empty($eligibleTieUserIds)) {
            return true;
        }

        return in_array($userId, $eligibleTieUserIds, true);
    }

    protected function saveWager(int $warId, int $userId, ?int $wager): void
    {
        if ($wager === null) {
            return;
        }

        WarzWager::updateOrCreate(
            [
                'user_id' => $userId,
                'warz_id' => $warId,
            ],
            [
                'wager' => $wager,
            ]
        );
    }

    protected function maxRounds(): int
    {
        return (int) config('app.war_max_rounds', env('WAR_MAX_ROUNDS'));
    }
}
