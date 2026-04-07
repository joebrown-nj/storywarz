<?php

namespace App\Http\Controllers\Warz;

use App\Models\ShowRoundSummary;
use App\Models\Stories;
use App\Models\UserWarz;
use App\Models\Warz;
use App\Models\WarzRounds;
use App\Models\WarzRoundsScores;
use App\Models\WarzRoundsVotes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WarBrowseController extends WarController
{
    public function index(): View
    {
        $wars = UserWarz::query()
            ->forDashboard(Auth::id())
            ->orderBy('warzs.status', 'asc')
            ->orderBy('warzs.created_at', 'desc')
            ->paginate(10, ['*'], 'wars');

        $yourStories = UserWarz::query()
            ->storyDashboard(Auth::id())
            ->paginate(10, ['*'], 'stories');

        $yourWars = Warz::where('user_id', Auth::id())
            ->orderBy('warzs.status', 'asc')
            ->orderBy('warzs.created_at', 'desc')
            ->paginate(10, ['*'], 'wars');

        return view('warz', [
            'warz' => $wars,
            'yourStories' => $yourStories,
            'yourWarz' => $yourWars,
        ]);
    }

    public function show($warId): View | RedirectResponse
    {
        $war = Warz::where('id', $warId)->first();
        if (!$war) {
            return redirect(route('warz', absolute: false))->withErrors(['War not found.']);
        }

        if ($war->status == 'created') {
            return redirect(route('warz', absolute: false))->withErrors(['War is not ready to begin.']);
        }

        if ($this->showRoundSummary($warId)) {
            return redirect(route('warz.showSummary', $warId));
        }

        $story = WarzRounds::query()
            ->currentWithStory($warId)
            ->first();

        if (!$story) {
            $story = $this->warRoundCreate($warId);
        }

        $roundCount = WarzRounds::where('warz_id', $warId)->count();

        return view('warz.show', [
            'war' => $war,
            'users' => $this->getWarUsers($warId),
            'round' => $roundCount,
            'doublePoints' => $this->isThisRoundDoublePoints($roundCount),
            'comments' => $war->comments()->orderBy('created_at', 'desc')->get(),
            'story' => $story,
            'youVotedFor' => WarzRoundsVotes::findUserVoteForRound(Auth::id(), $story->warz_rounds_id),
            'votes' => $this->getWarRoundVotes($story->warz_rounds_id),
            'tie' => $roundCount >= env('WAR_MAX_ROUNDS') ? $this->checkForTie($warId) : false,
            'yourScore' => WarzRoundsScores::where('user_id', Auth::id())->where('warz_id', $warId)->sum('score'),
        ]);
    }

    public function summary($warId): View | RedirectResponse
    {
        if (!$this->showRoundSummary($warId)) {
            return redirect(route('warz.show', $warId));
        }

        $war = Warz::where('id', $warId)->first();
        if (!$war) {
            return redirect(route('warz', absolute: false))->withErrors(['War not found.']);
        }

        $lastRound = WarzRounds::query()->latestCompleted($warId)->first();
        $lastStory = Stories::findWithAuthor($lastRound->stories_id);
        $roundCount = WarzRounds::where('warz_id', $warId)->count();
        $tie = $roundCount >= env('WAR_MAX_ROUNDS') ? $this->checkForTie($warId) : false;

        return view('warz.summary', [
            'war' => $war,
            'users' => $this->getWarUsers($warId),
            'round' => $roundCount >= env('WAR_MAX_ROUNDS') && !$tie ? $roundCount : $roundCount - 1,
            'comments' => $war->comments()->orderBy('created_at', 'desc')->get(),
            'story' => $lastStory,
            'votes' => $this->getWarRoundVotes($lastRound->id),
            'tie' => $tie,
        ]);
    }

    public function nextStory($warId): RedirectResponse
    {
        $remove = ShowRoundSummary::where('user_id', Auth::id());
        $remove->delete();

        return redirect(route('warz.show', $warId));
    }

    public function comment(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        $war = Warz::find($id);
        if (!$war) {
            return redirect(route('warz', absolute: false))->withErrors(['War not found.']);
        }

        $war->comments()->create([
            'user_id' => Auth::id(),
            'warz_id' => $id,
            'comment' => $request->comment,
        ]);

        return redirect(route('warz.show', ['id' => $id], absolute: false))->with('status', 'Comment added successfully!');
    }
}
