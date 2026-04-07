<?php

namespace App\Http\Controllers\Warz;

class WarriorInvitationController extends Controller
{
    private User $user;
    private Warz $war;

    public function __construct($war, $user)
    {
        $this->war = $war;
        $this->user = $user;
        $this->middleware('auth');

        if ($this->war->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return $this->generateInvitation(request()->route('id'));
    }

    public function generateInvitation()
    {
        $invitationLink = route('warz.show', ['id' => $this->war->id], absolute: true);
        die($invitationLink);
    }
}
