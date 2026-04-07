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
            <div class="flex items-center justify-between">
                <h2 class="text-lg lg:text-2xl font-bold text-gray-900 dark:text-white">
                    Story Number {{ $round }}
                </h2>
                

                @if ($doublePoints)
                    <button class="justify-end flex rounded-md items-center shadow-md shadow-red-800/50 dark:shadow-red-800/80 bg-red-600 px-3 py-2 text-xl font-semibold text-white">
                        <img src="/images/dp-hand-left.png" class="justify-start h-5 mr-4 animate-bounce" />
                        DOUBLE POINTS
                        <img src="/images/dp-hand-right.png" class="justify-end h-5 ml-4 animate-bounce" />
                    </button>
                @endif
            </div>

            <div class="w-full mt-4 mb-6 relative inline-flex items-center justify-center p-1 overflow-hidden rounded-4xl group bg-linear-to-br from-red-800 via-red-400 to-white-200 dark:text-white shadow-lg shadow-red-800/50 dark:shadow-lg dark:shadow-red-800/80">
                <p class="w-full text-lg leading-6 text-center font-medium text-stone-200 relative px-6 py-4.5 transition-all ease-in duration-75 bg-zinc-950 rounded-4xl">
                    {{ $story->story }}
                </p>
            </div>

            <h2 class="mb-2 text-lg lg:text-2xl font-bold text-gray-900 dark:text-white">
                @if ($youVotedFor && $youVotedFor->voted_for_user_id == 1)
                    You are eliminated
                @else
                    {{ $youVotedFor ? 'You picked ' . $youVotedFor->voted_for_name : 'Pick who this story belongs to' }}
                @endif
            </h2>
            <p class="mb-4 text-gray-200">
                {{ count($users) - count($votes) }} vote{{ count($users) - count($votes) === 1 ? '' : 's' }} remaining
            </p>

            @if ($round > env('WAR_MAX_ROUNDS') && $tie)
                <h2 class="mb-2 text-lg lg:text-2xl font-bold text-gray-900 dark:text-white">
                    {{ __('How many points do you want to wager? Max: ') }} <span class="maxWage">{{ $yourScore }}</span>
                </h2>

                <p class="mb-4 text-gray-200">
                    <x-input-label for="points" :value="__('Points')" />
                    <x-text-input id="points" class="block mt-1 w-20 text-center" type="number" name="points" min="1" max="{{ $yourScore }}" value="1" />
                </p>
            @endif

            <div class="flex mb-6 gap-4">
                @foreach($users as $u)
                    @if($u->id != Auth::id())
                        @if($youVotedFor)
                            <div class="@if($youVotedFor->voted_for_user_id == $u->id) border-red-600 bg-stone-700/50 shadow-md shadow-red-800/50 dark:shadow-md dark:shadow-red-800/80 @endif w-32 max-w-sm border border-stone-600 items-center justify-center overflow-hidden rounded-xl">
                                <center class="@if($youVotedFor->voted_for_user_id == $u->id) bg-stone-600/50 @else bg-stone-600/10 @endif pt-4 h-full">
                                    <x-user-avatar :user="$u" :width=64 />
                                    <p class="my-4 text-center text-base/5 font-semibold tracking-tight text-white">
                                        {{ $u->name }}
                                    </p>
                                    <x-show-who-user-voted-for :votes=$votes :u=$u/>
                                </center>
                            </div>
                        @else
                            <div data-vote-id="{{ $u->id }}" data-vote-name="{{ $u->name }}" class="cursor-pointer vote-button w-32 max-w-sm border border-stone-600 items-center justify-center overflow-hidden rounded-xl hover:shadow-md hover:shadow-red-800/50 hover:dark:shadow-md hover:dark:shadow-red-800/80">
                                <center class="bg-stone-600/10 hover:bg-stone-600/50 p-0 pt-4 h-full">
                                    <x-user-avatar :user=$u :width=64 class="justify-center align-center"/>
                                    <p class="my-4 text-center text-base/5 font-semibold tracking-tight text-white">
                                        {{ $u->name }}
                                    </p>
                                    <x-show-who-user-voted-for :votes=$votes :u=$u/>
                                </center>

                                <form id="form-{{ $u->id }}" action="{{ route('warz.vote', ['warId' => $war->id]) }}" method="post">
                                    @csrf
                                    @method('post')
                                    <input type="hidden" name="user" value="{{ $u->id }}" />
                                    <input type="hidden" name="wager" value="" />
                                </form>
                            </div>
                        @endif
                    @endif
                @endforeach
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
                                     <x-user-avatar :user="$user" class="rounded-full" :width=40 />
                                </div>
                                <div class="flex-1 min-w-0 ms-2">
                                    <p class="font-normal text-[16px] truncate">
                                        {{ $user->name }}
                                    </p>
                                    {{-- <p class="text-sm text-body truncate">
                                        email@windster.com
                                    </p> --}}

                                    {{-- <div class="text-stone-600 p-2 bg-gray-200/90 text-xs h-full">
                                        Voted for 
                                    </div> --}}
                                </div>
                                <div class="inline-flex items-center glow-yellow font-bold text-3xl">
                                    {{ $user->score ?? 0 }}
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>



                        {{-- <div class="mb-8 text-white text-xl font-semibold items-center text-center">
                            <div class="mb-0">
                                <p class="glow-blue">
                                    {{ $user->name }}
                                </p>
                            </div>

                            <div class="">
                                <p class="text-3xl glow-yellow">
                                    0
                                </p>
                            </div>
                        </div> --}}

{{-- <div class="flow-root">
    <ul role="list" class="divide-y divide-default">
        <li class="py-4 sm:py-4">
            <div class="flex items-center gap-2">
                <div class="shrink-0">
                    <img class="w-8 h-8 rounded-full" src="/docs/images/people/profile-picture-1.jpg" alt="Neil image">
                </div>
                <div class="flex-1 min-w-0 ms-2">
                    <p class="font-medium text-heading truncate">
                        Neil Sims
                    </p>
                    <p class="text-sm text-body truncate">
                        email@windster.com
                    </p>
                </div>
                <div class="inline-flex items-center font-medium text-heading">
                    $320
                </div>
            </div>
        </li>
        <li class="py-4 sm:py-4">
            <div class="flex items-center gap-2">
                <div class="shrink-0">
                    <img class="w-8 h-8 rounded-full" src="/docs/images/people/profile-picture-3.jpg" alt="Bonnie image">
                </div>
                <div class="flex-1 min-w-0 ms-2">
                    <p class="font-medium text-heading truncate">
                        Bonnie Green
                    </p>
                    <p class="text-sm text-body truncate">
                        email@windster.com
                    </p>
                </div>
                <div class="inline-flex items-center font-medium text-heading">
                    $3467
                </div>
            </div>
        </li>
        <li class="py-4 sm:py-4">
            <div class="flex items-center gap-2">
                <div class="shrink-0">
                    <img class="w-8 h-8 rounded-full" src="/docs/images/people/profile-picture-2.jpg" alt="Michael image">
                </div>
                <div class="flex-1 min-w-0 ms-2">
                    <p class="font-medium text-heading truncate">
                        Michael Gough
                    </p>
                    <p class="text-sm text-body truncate">
                        email@windster.com
                    </p>
                </div>
                <div class="inline-flex items-center font-medium text-heading">
                    $67
                </div>
            </div>
        </li>
        <li class="py-4 sm:py-4">
            <div class="flex items-center gap-2">
                <div class="shrink-0">
                    <img class="w-8 h-8 rounded-full" src="/docs/images/people/profile-picture-4.jpg" alt="Lana image">
                </div>
                <div class="flex-1 min-w-0 ms-2">
                    <p class="font-medium text-heading truncate">
                        Lana Byrd
                    </p>
                    <p class="text-sm text-body truncate">
                        email@windster.com
                    </p>
                </div>
                <div class="inline-flex items-center font-medium text-heading">
                    $367
                </div>
            </div>
        </li>
        <li class="pt-4 pb-0">
            <div class="flex items-center gap-2">
                <div class="shrink-0">
                    <img class="w-8 h-8 rounded-full" src="/docs/images/people/profile-picture-5.jpg" alt="Thomas image">
                </div>
                <div class="flex-1 min-w-0 ms-2">
                    <p class="font-medium text-heading truncate">
                        Thomas Lean
                    </p>
                    <p class="text-sm text-body truncate">
                        email@windster.com
                    </p>
                </div>
                <div class="inline-flex items-center font-medium text-heading">
                    $2367
                </div>
            </div>
        </li>
    </ul>
</div> --}}




        </div>
    </div>

    {{-- <button data-modal-target="popup-modal" data-modal-toggle="popup-modal" class="text-white bg-brand box-border border border-transparent hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none" type="button">
        Toggle modal
    </button> --}}

    <div id="popup-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-neutral-primary-soft border border-default dark:bg-stone-800/95 dark:border-stone-600 rounded-base shadow-sm p-4 md:p-6">
                    <button type="button" class="cursor-pointer absolute top-3 inset-e-2.5 text-body bg-transparent hover:bg-neutral-tertiary hover:text-heading rounded-base text-sm w-9 h-9 ms-auto inline-flex justify-center items-center" data-modal-hide="popup-modal">
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/></svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                <div class="p-4 md:p-5 text-center">
                    <svg class="mx-auto mb-4 text-stone-400 w-12 h-12" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 13V8m0 8h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    <h3 class="mb-6 text-stone-200 text-xl">Are you sure you want to vote for <span class="font-bold vote-name"></span>?</h3>
                    <div class="flex items-center space-x-4 justify-center">
                        <a id="confirm-vote-btn" data-modal-hide="popup-modal" type="button" class="cursor-pointer text-white bg-danger box-border border border-transparent hover:bg-danger-strong focus:ring-4 focus:ring-danger-medium shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">
                            Vote
                        </a>

                        <button data-modal-hide="popup-modal" type="button" class="cursor-pointer text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
