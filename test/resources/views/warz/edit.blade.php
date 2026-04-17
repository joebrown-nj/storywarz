<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit War') }}
        </h2>
    </x-slot>

    @if ($warz->status === 'active')
        <div class="py-12">
            <div class="lg:flex max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mr-6 ml-6 lg:flex-1 lg:p-4 sm:p-1 lg:w-14 bg-white dark:bg-stone-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        This war is in progress and cannot be edited. 
                        <x-secondary-link href="{{ route('warz.show', $warz) }}">
                            {{ __('Go to War') }}
                        </x-secondary-link>
                    </div>
                </div>
            </div>
        </div>
    @elseif ($warz->status === 'completed')
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
            This war has been completed and cannot be edited.
        </div>
    @else
        <form method="POST" action="{{ route('warz.update', $warz->id) }}">
            @csrf
            @method('patch')

            <div class="py-12">
                <div class="lg:flex max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="mr-6 ml-6 lg:flex-1 lg:p-4 sm:p-1 lg:w-14 bg-white dark:bg-stone-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <!-- Session Status -->
                            <x-auth-session-status class="mb-4" :status="session('status')" />
                            <x-input-error :messages="$errors->all()" class="mb-4" />

                            <div>
                                <x-input-label for="topic" :value="__('Topic')" />
                                <small class="text-gray-500 dark:text-gray-400">
                                    Warriorz will be required to submit stories about this topic
                                </small>
                                <x-text-input id="topic" class="block mt-1 w-full" type="text" name="topic" :value="old('topic', $warz->topic)" required />
                                <x-input-error :messages="$errors->get('topic')" class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-input-label for="prize" :value="__('Prize (optional)')" />
                                <x-text-input id="prize" class="block mt-1 w-full" type="text" name="prize" :value="old('prize', $warz->prize)" required  />
                            </div>

                            <div class="mt-4">
                                <x-input-label for="status" :value="__('Status')" />
                                <span class="inline-block px-2 py-1 text-sm font-semibold text-yellow-800 bg-yellow-200 rounded">{{ Str::title($warz->status) }}</span>
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <a href="{{ route('warz') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:underline">
                                    {{ __('Cancel') }}
                                </a>
                                <x-primary-button class="cursor-pointer ms-3 bg-black hover:bg-stone-900 text-red-700 font-bold py-2 px-4 rounded">
                                    {{ __('Save Changes') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </div>

                    <div class="mr-6 ml-6 lg:flex-1 lg:p-4 sm:p-1 lg:w-14 bg-white dark:bg-stone-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <div class="mt-4">
                                <x-input-label for="warriorz" :value="__('Warriorz (' . count($users) . ')')" />
                                <small class="text-gray-500 dark:text-gray-400">
                                    Minimum 2 required
                                </small>

                                <table class="text-gray-300 w-full mt-2 bg-white dark:bg-stone-800 border border-stone-300 dark:border-stone-700 rounded text-left">
                                    <thead>
                                        <tr>
                                            <th class="text-sm px-4 py-2 border-b border-stone-300 dark:border-stone-700">Name</th>
                                            {{-- <th class="text-sm px-4 py-2 border-b border-stone-300 dark:border-stone-700">Email</th>
                                            <th class="text-sm px-4 py-2 border-b border-stone-300 dark:border-stone-700">Phone</th>
                                            <th class="text-sm px-4 py-2 border-b border-stone-300 dark:border-stone-700">Validation Status</th> --}}
                                            <th class="text-sm px-4 py-2 border-b border-stone-300 dark:border-stone-700">Stories</th>
                                            <th class="text-sm px-4 py-2 border-b border-stone-300 dark:border-stone-700"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $warrior)
                                            @if($warrior->id != $warz->user_id)@endif
                                            <tr>
                                                <td class="px-4 py-2 border-b border-stone-300 dark:border-stone-700">{{ $warrior->name }}</td>
                                                {{-- <td class="px-4 py-2 border-b border-stone-300 dark:border-stone-700">{{ $warrior->email }}</td>
                                                <td class="px-4 py-2 border-b border-stone-300 dark:border-stone-700">{{ $warrior->phone }}</td>
                                                <td class="px-4 py-2 border-b border-stone-300 dark:border-stone-700">{{ $warrior->email_verified_at ? 'Validated' : 'Not Validated' }}</td> --}}
                                                <td class="px-4 py-2 border-b border-stone-300 dark:border-stone-700">{{ $warrior->story_count ?? 0 }}</td>
                                                <td class="px-4 py-2 border-b border-stone-300 dark:border-stone-700">
                                                    <ul class="flex space-x-4">
                                                        {{-- <li class="text-sm text-gray-500 dark:text-gray-400">
                                                            <a href="{{ route('warrior.edit', $warrior->id) }}" class="text-sm text-blue-500 hover:underline">
                                                                Edit
                                                            </a>
                                                        </li> --}}
                                                        <li class="text-sm text-gray-500 dark:text-gray-400">
                                                            @if ($warrior->id != $warz->user_id)
                                                                <a href="{{ route('warrior.delete', [$warz, 'userId' => $warrior->id]) }}" class="db-remove-warrior text-sm text-red-500 hover:underline">
                                                                    Delete
                                                                </a>
                                                            @endif
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <div class="mt-4">
                                    <div class="warrior-inputs flex gap-2">
                                        <div class="flex-1">
                                            <x-text-input class="block mt-1 w-full" placeholder="Name" type="text" name="warrior_names[]" />
                                        </div>
                                        <div class="flex-1">
                                            <x-text-input class="block mt-1 w-full" placeholder="Email or phone number" type="text" name="warrior_contacts[]" />
                                        </div>
                                        <div class="flex py-4">
                                            <a class="cursor-pointer text-sm text-red-500 hover:underline remove-warrior-btn">
                                                {{ __('Remove') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <x-secondary-button class="cursor-pointermt-2 add-warrior-btn bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Add Another Warrior
                                </x-secondary-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endif
</x-app-layout>
