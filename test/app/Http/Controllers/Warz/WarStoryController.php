<?php

namespace App\Http\Controllers\Warz;

use App\Models\Story;
use App\Models\UserWarz;
use App\Models\Warz;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WarStoryController extends WarController
{
    public function removeStory($storyId): RedirectResponse
    {
        $story = Story::find($storyId);
        if ($story) {
            $story->delete();
            return redirect()->back()->with('status', 'Story removed successfully!');
        }

        return redirect()->back()->withErrors(['Story not found.']);
    }

    public function addStoryForm($id): View | RedirectResponse
    {
        $war = UserWarz::findAccessibleWar(Auth::id(), $id);
        if (!$war) {
            return redirect(route('warz', absolute: false))->withErrors(['War not found or you do not have permission to edit it.']);
        }

        $stories = Story::query()->forWarrior($id, Auth::id())->get();

        return view('warz.addStory', ['warz' => $war, 'stories' => $stories]);
    }

    public function addStories(Request $request, Warz $war): RedirectResponse
    {
        if (!UserWarz::hasWarAccess(Auth::id(), $war->id)) {
            return redirect(route('warz', absolute: false))->withErrors(['War not found or you do not have permission to edit it.']);
        }

        $request->validate([
            'story' => ['required', 'array', 'min:3', 'max:7'],
        ]);

        foreach ($request->story as $key => $story) {
            if ($story) {
                if (isset($request->story_id[$key])) {
                    $oldStory = Story::find($request->story_id[$key]);
                    if (!$oldStory->story_was_used) {
                        $oldStory->fill(['story' => $story]);
                        $oldStory->save();
                    }
                } else {
                    Story::create([
                        'story' => $story,
                        'user_id' => Auth::id(),
                        'warz_id' => $war->id,
                    ]);
                }
            }
        }

        $this->checkIfWarIsReadyToStart($war->id);

        return redirect(route('warz'))->with('status', 'Stories added/updated successfully!');
    }
}
