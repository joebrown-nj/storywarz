<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Warz: Add Your Stories') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-stone-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />
                <x-input-error :messages="$errors->all()" class="mb-4" />

                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ __('War Topic: ' . $warz->topic) }}
                </h3>

                <p class="text-gray-700 dark:text-gray-300 mb-6">
                    {{ __('Created by ' . $warz->host_name) }}
                </p>

                <p class="text-gray-700 dark:text-gray-300 mb-4">
                    {!! __('You are a warrior in this war. Please submit 3-6 stories related to the topic <b>'.$warz->topic.'</b>.') !!}
                </p>

                <form method="post" action="{{ route('warz.addStories', $warz->id) }}" class="mt-6 space-y-6 add-stories-form">
                    @csrf
                    @method('patch')

                    @if($stories)
                        @foreach($stories as $story)
                            <div class="story-inputs mt-4">
                                <x-input-label for="topic" :value="__('Story'.($story->story_was_used ? ' (used)' : ''))" />
                                <textarea @if($story->story_was_used) disabled @else name="story[]" @endif id="story" class="@if($story->story_was_used) opacity-50 @endif mt-1 block w-full bg-stone-50 dark:bg-stone-700 border border-stone-300 dark:border-stone-600 rounded-md shadow-sm focus:ring-stone-500 focus:border-stone-500 text-stone-200" rows="4">{{ $story->story }}</textarea>
                                <input type="hidden" name="story_id[]" value="{{ $story->id }}">
                                @if($story->story_was_used) <input type="hidden" name="story[]" value="{{ $story->story }}"> @endif
                                @if(!$story->story_was_used)
                                    <a href="{{ route('warz.removeStory', $story->id) }}" class="text-sm cursor-pointer text-red-500 hover:underline mt-1 inline-block">
                                        {{ __('Remove') }}
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    @else 
                        @if(old('story'))
                            @foreach(old('story') as $index => $story)
                                <div class="story-inputs mt-4">
                                    <x-input-label for="topic" :value="__('Story')" />
                                    <textarea id="story" name="story[]" class="mt-1 block w-full bg-stone-50 dark:bg-stone-700 border border-stone-300 dark:border-stone-600 rounded-md shadow-sm focus:ring-stone-500 focus:border-stone-500 text-stone-200" rows="4">{{ $story }}</textarea>
                                    <a class="remove-story-input cursor-pointer text-sm text-red-500 hover:underline mt-1 inline-block">
                                        {{ __('Remove') }}
                                    </a>
                                </div>
                            @endforeach
                        @endif
                    @endif

                    <div class="story-inputs-hidden hidden mt-4">
                        <x-input-label for="topic" :value="__('Story')" />
                        <textarea id="story" name="story[]" class="mt-1 block w-full bg-stone-50 dark:bg-stone-700 border border-stone-300 dark:border-stone-600 rounded-md shadow-sm focus:ring-stone-500 focus:border-stone-500 text-stone-200" rows="4"></textarea>
                        <a class="remove-story-input cursor-pointer text-sm text-red-500 hover:underline mt-1 inline-block">
                            {{ __('Remove') }}
                        </a>
                    </div>

                    <div class="mt-4">
                        <x-secondary-button class="mt-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" id="addStoryButton">
                            {{ __('Add Another Story') }}
                        </x-secondary-button>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('warz') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:underline">
                            {{ __('Cancel') }}
                        </a>
                        <x-primary-button class="cursor-pointer ms-3 bg-black hover:bg-stone-900 text-red-700 font-bold py-2 px-4 rounded">
                            {{ __('Save Changes') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>