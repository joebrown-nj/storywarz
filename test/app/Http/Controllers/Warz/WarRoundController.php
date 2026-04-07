<?php

namespace App\Http\Controllers\Warz;

use App\Models\ShowRoundSummary;
use App\Models\WarzRounds;
use App\Models\WarzRoundsScores;
use App\Models\WarzRoundsVotes;
use App\Models\WarzWagers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarRoundController extends WarController
{
    public function vote(Request $request, $warId): RedirectResponse
    {
        $warRound = WarzRounds::query()
            ->currentWithStory($warId)
            ->first();

        $hasVoted = WarzRoundsVotes::where('warz_rounds_id', $warRound->round_id)->where('user_id', Auth::id())->exists();
        if ($hasVoted) {
            return redirect(route('warz.show', ['id' => $warId]));
        }

        WarzRoundsVotes::create([
            'warz_rounds_id' => $warRound->round_id,
            'user_id' => Auth::id(),
            'warz_id' => $warId,
            'voted_for_user_id' => $request->user,
        ]);

        $users = $this->getWarUsers($warId);
        $votes = WarzRoundsVotes::where('warz_rounds_id', $warRound->round_id)->get();
        $tiedUsers = $this->checkForTie($warId);

        if ($tiedUsers && $request->wager) {
            $wager = WarzWagers::where('user_id', Auth::id())->where('warz_id', $warId)->first();
            if (!$wager) {
                WarzWagers::create([
                    'user_id' => Auth::id(),
                    'warz_id' => $warId,
                    'wager' => $request->wager,
                ]);
            } else {
                $wager->wager = $request->wager;
                $wager->save();
            }
        }

        if (count($users) == $votes->count()) {
            $round = WarzRounds::where('id', $warRound->round_id)->first();
            $round->complete = true;
            $round->save();

            ShowRoundSummary::create([
                'user_id' => Auth::id(),
                'warz_id' => $warId,
            ]);

            $roundCount = WarzRounds::where('warz_id', $warId)->count();
            $doublePoints = $this->isThisRoundDoublePoints($roundCount);
            $userStoryScore = 0;
            $storyAuthor = $warRound->user_id;

            foreach ($votes as $vote) {
                if (!$tiedUsers || ($tiedUsers && in_array($vote->user_id, array_column($tiedUsers, 'id')))) {
                    if ($vote->voted_for_user_id == $storyAuthor) {
                        if ($tiedUsers) {
                            $wager = WarzWagers::where('user_id', $vote->user_id)->where('warz_id', $warId)->first();
                            $score = $wager->wager;
                        } else {
                            $score = $doublePoints ? 4 : 2;
                        }

                        WarzRoundsScores::create([
                            'user_id' => $vote->user_id,
                            'warz_rounds_id' => $warRound->round_id,
                            'warz_id' => $warId,
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
                WarzRoundsScores::create([
                    'user_id' => $storyAuthor,
                    'warz_rounds_id' => $warRound->round_id,
                    'warz_id' => $warId,
                    'score' => $userStoryScore,
                ]);
            }

            $this->warRoundCreate($warId);
        }

        return redirect(route('warz.show', ['id' => $warId]))->with('status', 'Your vote is in!');
    }
}
