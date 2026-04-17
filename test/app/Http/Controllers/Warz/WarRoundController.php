<?php

namespace App\Http\Controllers\Warz;

use App\Models\ShowRoundSummary;
use App\Models\WarzRound;
use App\Models\WarzRoundsScore;
use App\Models\WarzRoundsVote;
use App\Models\WarzWager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarRoundController extends WarController
{
    public function vote(Request $request, Warz $war): RedirectResponse
    {
        $warRound = WarzRound::query()
            ->currentWithStory($war->id)
            ->first();

        $hasVoted = WarzRoundsVote::where('warz_rounds_id', $warRound->round_id)->where('user_id', Auth::id())->exists();
        if ($hasVoted) {
            return redirect(route('warz.show', $war))->withErrors(['You have already voted for this round.']);
        }

        WarzRoundsVote::create([
            'warz_rounds_id' => $warRound->round_id,
            'user_id' => Auth::id(),
            'warz_id' => $war->id,
            'voted_for_user_id' => $request->user,
        ]);

        // if the max number of rounds has been reached, 
        // check for tie and save wager if applicable
        $storyNumber = WarzRound::storyNumber($war->id);
        if($storyNumber >= env('WAR_MAX_ROUNDS')) {
            // If this is a tie round, save the user's wager
            $tiedUsers = $this->checkForTie($war->id);
            if ($tiedUsers && $request->wager) {
                $wager = WarzWager::where('user_id', Auth::id())->where('warz_id', $war->id)->first();
                if (!$wager) {
                    WarzWager::create([
                        'user_id' => Auth::id(),
                        'warz_id' => $war->id,
                        'wager' => $request->wager,
                    ]);
                } else {
                    $wager->wager = $request->wager;
                    $wager->save();
                }
            }
        }

        // If everyone has voted for this round, calculate scores, 
        // mark round complete, and create next round if applicable
        $users = $this->getWarUsers($war->id);
        $votes = WarzRoundsVote::where('warz_rounds_id', $warRound->round_id)->get();
        if (count($users) == $votes->count()) {
            $round = WarzRound::where('id', $warRound->round_id)->first();
            $round->complete = true;
            $round->save();

            ShowRoundSummary::create([
                'user_id' => Auth::id(),
                'warz_id' => $war->id,
            ]);

            $storyNumber = WarzRound::storyNumber($war->id);
            $doublePoints = $this->isThisRoundDoublePoints($storyNumber);
            $userStoryScore = 0;
            $storyAuthor = $warRound->user_id;
            $tiedUsers = $this->checkForTie($war->id);

            foreach ($votes as $vote) {
                if (!$tiedUsers || ($tiedUsers && in_array($vote->user_id, array_column($tiedUsers, 'id')))) {
                    if ($vote->voted_for_user_id == $storyAuthor) {
                        if ($tiedUsers) {
                            $wager = WarzWager::where('user_id', $vote->user_id)->where('warz_id', $war->id)->first();
                            $score = $wager->wager;
                        } else {
                            $score = $doublePoints ? 4 : 2;
                        }

                        WarzRoundsScore::create([
                            'user_id' => $vote->user_id,
                            'warz_rounds_id' => $warRound->round_id,
                            'warz_id' => $war->id,
                            'score' => $score,
                        ]);
                    } elseif ($vote->user_id != $storyAuthor) {
                        if (!$tiedUsers || ($tiedUsers && in_array($storyAuthor, array_column($tiedUsers, 'id')))) {
                            $userStoryScore += $doublePoints ? 2 : 1;
                        }
                    }
                }
            }

            if (!$tiedUsers || ($tiedUsers && in_array($storyAuthor, array_column($tiedUsers, 'id')))) {
                WarzRoundsScore::create([
                    'user_id' => $storyAuthor,
                    'warz_rounds_id' => $warRound->round_id,
                    'warz_id' => $war->id,
                    'score' => $userStoryScore,
                ]);
            }

            $this->warRoundCreate($war->id);
        }

        return redirect(route('warz.show', $war))->with('status', 'Your vote is in!');
    }
}
