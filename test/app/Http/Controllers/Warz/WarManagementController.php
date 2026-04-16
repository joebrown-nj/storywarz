<?php

namespace App\Http\Controllers\Warz;

use App\Models\Warz;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class WarManagementController extends WarController
{
    public function create(): View
    {
        return view('warz.create');
    }

    public function edit(Warz $war): View | RedirectResponse
    {
        Gate::allowIf(fn (User $user) => $user->id === $war->user_id);
        return view('warz.edit', [
            'warz' => $war,
            'users' => $this->getWarUsers($war->id),
        ]);
    }

    public function update(Request $request, Warz $war): RedirectResponse
    {
        Gate::allowIf(fn (User $user) => $user->id === $war->user_id);
        $userExistsMessage = [];

        $war->update($request->all());

        $users = $this->sendInvitations($war, $request);

        foreach ($users as $user) {
            if (!$war->users()->where('user_id', $user->id)->exists()) {
                $war->users()->attach($user->id);
                continue;
            }

            $userExistsMessage[] = "{$user->name} has already been invited to this war.";
        }

        return redirect(route('warz.edit', $war->id, absolute: false))
            ->with('status', 'War updated successfully!')
            ->withErrors($userExistsMessage);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'topic' => ['required', 'string', 'max:255'],
            'warrior_names' => ['required', 'array', 'min:2'],
            'warrior_names.*' => ['required', 'string', 'max:255'],
            'warrior_contacts' => ['required', 'array', 'min:2'],
            'warrior_contacts.*' => ['required', 'string', 'max:255'],
        ]);

        $war = Warz::create([
            'topic' => $request->topic,
            'warrior_names' => json_encode($request->warrior_names),
            'warrior_contacts' => json_encode($request->warrior_contacts),
            'prize' => $request->prize,
            'user_id' => Auth::id(),
        ]);

        $users = $this->sendInvitations($war, $request);

        $war->users()->attach(Auth::id());
        foreach ($users as $user) {
            $war->users()->attach($user->id);
        }

        return redirect(route('warz', ['warz' => $war->id], absolute: false));
    }

    public function deleteWarrior(Warz $war, $userId): RedirectResponse
    {
        Gate::allowIf(fn (User $user) => $user->id === $war->user_id);
        $war->users()->detach($userId);

        return redirect(route('warz.edit', $war->id, absolute: false))->with('status', 'Warrior removed successfully!');
    }
}
