<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class MessageSettingsController extends Controller
{
    /**
     * Update the user's message settings.
     */
    public function update(Request $request): RedirectResponse
    {
        if (!$request->user()->messageSettings()->first()){
            $request->user()->messageSettings()->create([
                'send_sms_notifications' => $request->has('send_sms_notifications'),
                'send_email_notifications' => $request->has('send_email_notifications'),
            ]);
        } else {
            $request->user()->messageSettings()->update([
                'send_sms_notifications' => $request->has('send_sms_notifications'),
                'send_email_notifications' => $request->has('send_email_notifications'),
            ]);
        }

        return back()->with('status', 'message-settings-updated');
    }
}
