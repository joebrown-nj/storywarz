<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create War') }}
        </h2>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />
        <x-input-error :messages="$errors->all()" class="mb-4" />
    </x-slot>

    <div class="py-12">
        <div class="flex max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mr-6 ml-6 flex-1 p-4 w-14 bg-white dark:bg-stone-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="mb-6 font-semibold text-lg text-gray-800 dark:text-gray-200 leading-tight">
                        {{ __("Are you ready for war!?") }}
                    </h3>
                    <p class="mb-4 text-gray-600 dark:text-gray-400">
                        {{ __("Create a war and invite your friends to join you!") }}
                    </p>

                    <form method="POST" action="{{ route('warz.store') }}">
                        @csrf

                        <div>
                            <x-input-label for="topic" :value="__('Topic')" />
                            <small class="text-gray-500 dark:text-gray-400">
                                Warriorz will be required to submit stories about this topic
                            </small>
                            <x-text-input id="topic" class="block mt-1 w-full" type="text" name="topic" :value="old('topic')" required  />
                            <x-input-error :messages="$errors->get('topic')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="warriorz" :value="__('Warriorz')" />
                            <small class="text-gray-500 dark:text-gray-400">
                                Minimum 2 required
                            </small>

                            <div class="warrior-inputs flex gap-2">
                                <div class="flex-1">
                                    <x-text-input class="block mt-1 w-full" placeholder="Name" type="text" name="warrior_names[]" required  />
                                </div>
                                <div class="flex-1">
                                    <x-text-input class="block mt-1 w-full" placeholder="Email or phone number" type="text" name="warrior_contacts[]" required  />
                                </div>
                            </div>

                            <x-secondary-button class="mt-2 add-warrior-btn bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Add Another Warrior
                            </x-secondary-button>
                        </div>

                        <div class="mt-4">
                            <x-input-label for="prize" :value="__('Prize (optional)')" />
                            <x-text-input id="prize" class="block mt-1 w-full" type="text" name="prize" :value="old('prize')" required  />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-3 bg-black hover:bg-stone-900 text-red-700 font-bold py-2 px-4 rounded">
                                {{ __('Go to War!') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>                
            </div>
        </div>
    </div>
</x-app-layout>
