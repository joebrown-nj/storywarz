<x-app-layout>
    <x-slot name="header">
        <div class="flex">
            <div class="flex-1">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Warz') }}
                </h2>
            </div>

            <div class="flex-1 justify-end flex">
                <a href="{{ route('warz.create') }}" class="rounded-md bg-white/10 px-2.5 py-1.5 text-sm font-semibold text-white inset-ring inset-ring-white/5 hover:bg-white/20">Create A War</a>
            </div>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />
        <x-input-error :messages="$errors->all()" class="mb-4" />
    </x-slot>

    <div class="py-0">
        <div class="lg:flex max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mr-6 ml-6 lg:flex-1 lg:p-4 sm:p-1 lg:w-14 bg-white dark:bg-stone-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-8 flex items-center justify-between">
                        <h3 class="justify-start flex-1 font-semibold text-lg text-gray-800 dark:text-gray-200 leading-tight">
                            {{ __("Warz You Are In") }}
                        </h3>

                        <a href="{{ route('warz.create') }}" class="justify-end flex rounded-md bg-white/10 px-2.5 py-1.5 text-sm font-semibold text-white inset-ring inset-ring-white/5 hover:bg-white/20">
                            Create A War
                        </a>
                    </div>

                    <table class="mb-4 w-full text-sm text-left rtl:text-right border border-stone-300 dark:border-stone-700 rounded">
                        <thead class="text-sm text-gray-300 bg-stone-900 dark:bg-stone-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 font-medium">Topic</th>
                                <th scope="col" class="px-6 py-3 font-medium">Status</th>
                                <th scope="col" class="px-6 py-3 font-medium">Round</th>
                                <th scope="col" class="px-6 py-3 font-medium">Host</th>
                                <th scope="col" class="px-6 py-3 font-medium">Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($warz as $war)
                                <tr 
                                    class="@if ($war->status != 'created') dark:bg-red-950/40 hover:dark:bg-red-950/60 @else odd:bg-stone-800 even:bg-stone-700 @endif border-b border-stone-300 dark:border-stone-700 cursor-pointer"
                                    onClick="window.location=
                                        @if ($war->status == 'created')
                                            '{{ route('warz.edit', $war->warz_id) }}'
                                        @else
                                            '{{ route('warz.show', $war->warz_id) }}'
                                        @endif
                                ">
                                    <td class="px-6 py-4">{{ $war->topic }}</td>
                                    <td class="px-6 py-4">{{$war->status}}</td>
                                    <td class="px-6 py-4">{{ $war->round_count }}</td>
                                    <td class="px-6 py-4">{{ $war->name }}</td>
                                    <td class="px-6 py-4">{{ date('n/d', strtotime($war->created_at)) }}</td>
                                    {{-- <td class="px-6 py-4">
                                        @if ($war->status == 'active')
                                            <a href="{{ route('warz.show', $war->id) }}" 
                                                class="text-nowrap inline-flex items-center px-4 py-2 bg-white dark:bg-stone-800 border border-stone-300 dark:border-red-800 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-stone-50 dark:hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150"
                                            >
                                                View War
                                            </a>
                                        @else
                                            <x-secondary-link href="{{ route('warz.edit', $war->id) }}">
                                                Edit
                                            </x-secondary-link>
                                        @endif
                                    </td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>


                    {{ $warz->links() }}
                </div>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="lg:flex max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mr-6 ml-6 lg:flex-1 lg:p-4 sm:p-1 lg:w-14 bg-white dark:bg-stone-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-8 flex items-center justify-between">
                        <h3 class="justify-start flex-1 font-semibold text-lg text-gray-800 dark:text-gray-200 leading-tight">
                            {{ __("Warz You Created") }}
                        </h3>

                        <a href="{{ route('warz.create') }}" class="justify-end flex rounded-md bg-white/10 px-2.5 py-1.5 text-sm font-semibold text-white inset-ring inset-ring-white/5 hover:bg-white/20">
                            Create A War
                        </a>
                    </div>

                    <table class="mb-4 w-full text-sm text-left rtl:text-right border border-stone-300 dark:border-stone-700 rounded">
                        <thead class="text-sm text-gray-300 bg-stone-900 dark:bg-stone-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 font-medium">Topic</th>
                                <th scope="col" class="px-6 py-3 font-medium">Status</th>
                                <th scope="col" class="px-6 py-3 font-medium">Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($yourWarz as $war)
                                <tr 
                                    class="@if ($war->status == 'active') dark:bg-red-950/50 @else odd:bg-stone-800 even:bg-stone-700 @endif border-b border-stone-300 dark:border-stone-700 cursor-pointer"
                                    onClick="window.location='{{ route('warz.edit', $war->id) }}'">
                                    <td class="px-6 py-4">{{ $war->topic }}</td>
                                    <td class="px-6 py-4">{{ $war->status }}</td>
                                    <td class="px-6 py-4">{{ date('n/d', strtotime($war->created_at)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>


                    {{ $yourWarz->links() }}
                </div>
            </div>

            <div class="mr-6 ml-6 lg:flex-1 p-4 lg:w-64 bg-white dark:bg-stone-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="mb-2 font-semibold text-lg text-gray-800 dark:text-gray-200 leading-tight">
                        {{ __("Your Stories") }}
                    </h3>
                    <p class="mb-4 text-xs text-gray-500 dark:text-gray-300">
                        Click to add/edit your stories. All participants must have 3-6 stories per war.
                        Have to change this - can't view warz unless you created them
                         - maybe add and change "Warz you've created"
                    </p>

                    <table class="w-full text-sm text-left rtl:text-right bg-white dark:bg-stone-800 border border-stone-300 dark:border-stone-700 rounded">
                        <thead class="text-sm text-gray-300 bg-stone-900 dark:bg-stone-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 font-medium">Topic</th>
                                <th scope="col" class="px-6 py-3 font-medium">Host</th>
                                <th scope="col" class="px-6 py-3 font-medium">Stories</th>
                                {{-- <th scope="col" class="px-6 py-3 font-medium">Status</th> --}}
                                {{-- <th scope="col" class="px-6 py-3 font-medium"></th> --}}
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @foreach ($yourStories as $war)
                                <tr 
                                    class="@if ($war->story_count >= 3)dark:bg-red-950/50 @else odd:bg-stone-800 even:bg-stone-700 @endif border-b border-stone-300 dark:border-stone-700 cursor-pointer"
                                    onClick="window.location='{{ route('warz.addStoryForm', $war->id) }}'"
                                >
                                    <td class="px-6 py-4">{{ $war->topic }}</td>
                                    <td class="px-6 py-4">{{ $war->host_name }}</td>
                                    <td class="px-6 py-4">{{ $war->story_count }}</td>
                                    {{-- <td class="px-6 py-4">{{ $war->status }}</td> --}}
                                    {{-- 
                                    <td class="px-6 py-4">
                                        @if ($war->story_count >= 3)
                                            <a href="{{ route('warz.show', $war->id) }}"
                                                class="text-nowrap inline-flex items-center px-4 py-2 bg-white dark:bg-stone-800 border border-stone-300 dark:border-red-800 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-stone-50 dark:hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150"
                                            >
                                                View War
                                            </a>
                                        @else
                                            <x-secondary-link href="{{ route('warz.addStoryForm', $war->id) }}">
                                                Add Stories
                                            </x-secondary-link>
                                        @endif
                                    </td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ $yourStories->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
