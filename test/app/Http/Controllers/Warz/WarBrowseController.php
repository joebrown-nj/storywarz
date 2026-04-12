<?php

namespace App\Http\Controllers\Warz;

use App\Models\ShowRoundSummary;
use App\Models\Story;
use App\Models\UserWarz;
use App\Models\Warz;
use App\Models\WarzRound;
use App\Models\WarzRoundsScore;
use App\Models\WarzRoundsVote;
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

    public function show(Warz $war): View | RedirectResponse
    {
        if ($war->status == 'created') {
            return redirect(route('warz', absolute: false))->withErrors(['War is not ready to begin.']);
        }

        if ($this->showRoundSummary($war->id)) {
            return redirect(route('warz.showSummary', $war->id));
        }

        $story = WarzRound::query()
            ->currentWithStory($war->id)
            ->first();

        if (!$story) {
            $story = $this->warRoundCreate($war->id);
        }

        $storyNumber = WarzRound::storyNumber($war->id);

        return view('warz.show', [
            'war' => $war,
            'users' => $this->getWarUsers($war->id),
            'round' => $storyNumber,
            'doublePoints' => $this->isThisRoundDoublePoints($storyNumber),
            'comments' => $war->comments()->orderBy('created_at', 'desc')->get(),
            'story' => $story,
            'youVotedFor' => WarzRoundsVote::findUserVoteForRound(Auth::id(), $story->warz_rounds_id),
            'votes' => $this->getWarRoundVotes($story->warz_rounds_id),
            'tie' => $storyNumber >= env('WAR_MAX_ROUNDS') ? $this->checkForTie($war->id) : false,
            'yourScore' => WarzRoundsScore::where('user_id', Auth::id())->where('warz_id', $war->id)->sum('score'),
        ]);
    }

    public function summary(Warz $war): View | RedirectResponse
    {
        if (!$this->showRoundSummary($war->id)) {
            return redirect(route('warz.show', $war->id));
        }

        $lastRound = WarzRound::query()->latestCompleted($war->id)->first();
        $lastStory = Story::findWithAuthor($lastRound->stories_id);
        $storyNumber = WarzRound::storyNumber($war->id);
        $tie = $storyNumber >= env('WAR_MAX_ROUNDS') ? $this->checkForTie($war->id) : false;

        return view('warz.summary', [
            'war' => $war,
            'users' => $this->getWarUsers($war->id),
            'round' => $storyNumber >= env('WAR_MAX_ROUNDS') && !$tie ? $storyNumber : $storyNumber - 1,
            'comments' => $war->comments()->orderBy('created_at', 'desc')->get(),
            'story' => $lastStory,
            'votes' => $this->getWarRoundVotes($lastRound->id),
            'tie' => $tie,
        ]);
    }

    public function nextStory(Warz $war): RedirectResponse
    {
        $remove = ShowRoundSummary::where('user_id', Auth::id())->where('warz_id', $war->id)->first();
        if ($remove) {
            $remove->delete();
        }
        return redirect(route('warz.show', $war->id));
    }

    public function comment(Request $request, Warz $war): RedirectResponse
    {
        $request->validate([
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        $war->comments()->create([
            'user_id' => Auth::id(),
            'warz_id' => $war->id,
            'comment' => $request->comment,
        ]);

        return redirect(route('warz.show', $war->id))->with('status', 'Comment added successfully!');
    }
}
