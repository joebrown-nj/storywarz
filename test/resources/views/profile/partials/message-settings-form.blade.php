<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Message Settings') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Manage your message preferences and notifications.') }}
        </p>
    </header>

    <form method="post" action="{{ route('message.settings.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="send_sms_notifications" class="inline-flex items-center">
                <input id="send_sms_notifications" type="checkbox" class="rounded dark:bg-stone-900 border-stone-300 dark:border-stone-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="send_sms_notifications" value="1" @if($settings && $settings->send_sms_notifications) checked @endif>
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Send SMS Notifications') }}</span>
            </label>
        </div>

        <div>
            <label for="send_email_notifications" class="inline-flex items-center">
                <input id="send_email_notifications" type="checkbox" class="rounded dark:bg-stone-900 border-stone-300 dark:border-stone-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="send_email_notifications" value="1" @if($settings && $settings->send_email_notifications) checked @endif>
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Send Email Notifications') }}</span>
            </label>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'message-settings-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
