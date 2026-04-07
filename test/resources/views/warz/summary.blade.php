<x-app-layout>
    <x-slot name="header">
        <div class="items-center">
            <div class="flex items-center justify-center">
                <img src="/images/story-warz-words-logo.png" alt="Story Warz Logo" class="h-12">
            </div>

            <div class="flex items-center justify-center mt-0">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Topic: {{ $war->topic }}
                </h2>
            </div>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />
        <x-input-error :messages="$errors->all()" class="mb-4" />
    </x-slot>

    <div class="lg:flex lg:p-12 sm:p-12" style="background:url('/images/story-warz-stage-bg.jpg') no-repeat center center fixed; background-size: cover;">
        <div class="mb-6 p-6 lg:flex-1 dark:bg-stone-800/50 mr-6 ml-6 sm:rounded-lg">
            <div class="mb-2 flex items-center justify-between">
                <h2 class="mb-4 text-lg lg:text-2xl font-bold text-gray-900 dark:text-white">
                    All Votes Are In! 
                </h2>
                @if ($round < env('WAR_MAX_ROUNDS') || $tie)
                    <a href="{{ route('warz.nextStory', $war->id) }}" class="justify-end flex rounded-md bg-red-500/20 px-4 py-3 font-semibold text-white inset-ring inset-ring-red-500/10 hover:bg-red-500/40">
                        Next Story
                    </a>
                @endif
            </div>

            <h2 class="mb-4 text-lg lg:text-2xl font-bold text-gray-900 dark:text-white">
                Story number {{ $round }} belongs to: <span class="glow-blue">{{ $story->name }}</span>
            </h2>

            @if ($round >= env('WAR_MAX_ROUNDS') && !$tie)
                <h2 class="mb-2 text-lg lg:text-2xl font-bold text-gray-900 dark:text-white">
                    {{ __('Your winner: ') }}
                    <span class="mb-6 text-lg lg:text-2xl text-gray-50 glow-blue">
                        {{ $users[0]->name }} with {{ $users[0]->score }} points!
                    </span>
                </h2>
            @endif

            <a href="{{ route('warz.create') }}" class="flex rounded-md bg-white/10 px-2.5 py-1.5 text-sm font-semibold text-white inset-ring inset-ring-white/5 hover:bg-white/20">
                Create A War
            </a>

            <a href="{{ route('warz') }}" class="flex rounded-md bg-white/10 px-2.5 py-1.5 text-sm font-semibold text-white inset-ring inset-ring-white/5 hover:bg-white/20">
                Your Wars
            </a>

            <h2 class="mb-2 text-lg lg:text-2xl font-bold text-gray-900 dark:text-white">
                {{ __('Points') }}
            </h2>

            <div class="mb-6 text-gray-50">
                <ul>
                    @foreach($votes as $vote)
                        <li class="flex gap-x-2 items-center mb-4 @if($vote->voted_for_user_id == 1) line-through @endif">
                            <div class="">
                                <p class="glow-yellow">
                                    {{ $vote->score ?? 0 }}
                                </p>
                            </div>

                            <div class="">
                                <x-avatar :src="$vote->avatar" :name="$vote->name" :width="20" />
                            </div>

                            <div class="">
                                {{ $vote->name }}
                            </div>

                            <div class="">
                                @if($vote->voted_for_user_id != 1) voted for @endif
                            </div>

                            <div class="">
                                @if($vote->voted_for_user_id != 1) <x-avatar :src="$vote->voted_for_avatar" :name="$vote->voted_for_name" :width="20" /> @endif
                            </div>

                            <div class="">
                                @if($vote->voted_for_user_id != 1) {{ $vote->voted_for_name }} @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="w-full mt-4 mb-6 relative inline-flex items-center justify-center p-1 overflow-hidden rounded-4xl group bg-linear-to-br from-red-800 via-red-400 to-white-200 dark:text-white shadow-lg shadow-red-800/50 dark:shadow-lg dark:shadow-red-800/80">
                <p class="w-full text-lg leading-6 text-center font-medium text-stone-200 relative px-6 py-4.5 transition-all ease-in duration-75 bg-zinc-950 rounded-4xl">
                    {{ $story->story }}
                </p>
            </div>

            @include('warz.comments')
        </div>

        <div class="mb-6 p-6 lg:w-80 flex-none dark:bg-stone-800/50 mr-6 ml-6 sm:rounded-lg">
            <h2 class="text-center mb-6 text-lg lg:text-2xl font-bold text-gray-900 dark:text-white">
                Scoreboard
            </h2>

            <div class="flow-root">
                <ul role="list" class="border-b border-stone-600/50 divide-y divide-stone-600/50 mb-8 text-white text-xl">
                    @foreach($users as $user)
                        <li class="py-3 sm:py-3 px-2">
                            <div class="flex items-center gap-2">
                                <div class="shrink-0">
                                     <x-user-avatar :user="$user" :width=40 />
                                </div>
                                <div class="flex-1 min-w-0 ms-2">
                                    <p class="font-normal text-[16px] truncate">
                                        {{ $user->name }}
                                    </p>
                                </div>
                                <div class="inline-flex items-center glow-yellow font-bold text-3xl">
                                    {{ $user->score ?? 0 }}
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
