<section class="bg-white dark:bg-stone-900 py-8 lg:py-16 antialiased">
    <div class="max-w-2xl mx-auto px-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg lg:text-2xl font-bold text-gray-900 dark:text-white">Discussion ({{ count($comments) }})</h2>
        </div>
        <form class="mb-6" method="POST" action="{{ route('warz.comment', $war->id) }}">
            @csrf
            <div class="py-2 px-4 mb-4 bg-white rounded-lg rounded-t-lg border border-stone-200 dark:bg-stone-800 dark:border-stone-700">
                <label for="comment" class="sr-only">Your comment</label>
                <textarea id="comment" name="comment" rows="6"
                    class="px-0 w-full text-sm text-gray-900 border-0 focus:ring-0 focus:outline-none dark:text-white dark:placeholder-gray-400 dark:bg-stone-800"
                    placeholder="Write a comment..." required></textarea>
            </div>
            <x-primary-button class="cursor-pointer ms-3 bg-black hover:bg-stone-900 text-red-700 font-bold py-2 px-4 rounded">
                {{ __('Post Comment') }}
            </x-primary-button>
        </form>

        @if($comments)
            @foreach($comments as $key => $comment)
                <article class="p-6 text-base bg-white rounded-lg dark:bg-stone-900">
                    <footer class="flex justify-between items-center mb-2">
                        <div class="flex items-center">
                            <p class="inline-flex items-center mr-3 text-sm text-gray-900 dark:text-white font-semibold">
                                <x-avatar :src="$comment->user->avatar" :name="$comment->user->name" :width="20" />
                                <span class="ml-2">{{ $comment->user->name }}</span>
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <time pubdate datetime="{{ date_format($comment->created_at, 'F j, Y, g:i a') }}" title="{{ date_format($comment->created_at, 'F j, Y, g:i a') }}">{{ date_format($comment->created_at, 'F j, Y, g:i a') }}</time>
                            </p>
                        </div>
                        {{-- <button id="dropdownComment1Button" data-dropdown-toggle="dropdownComment1"
                            class="inline-flex items-center p-2 text-sm font-medium text-center text-gray-500 dark:text-gray-400 bg-white rounded-lg hover:bg-stone-100 focus:ring-4 focus:outline-none focus:ring-gray-50 dark:bg-stone-900 dark:hover:bg-stone-700 dark:focus:ring-gray-600"
                            type="button">
                            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 3">
                                <path d="M2 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm6.041 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM14 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Z"/>
                            </svg>
                            <span class="sr-only">Comment settings</span>
                        </button>
                        <!-- Dropdown menu -->
                        <div id="dropdownComment1"
                            class="hidden z-10 w-36 bg-white rounded divide-y divide-gray-100 shadow dark:bg-stone-700 dark:divide-gray-600">
                            <ul class="py-1 text-sm text-gray-700 dark:text-gray-200"
                                aria-labelledby="dropdownMenuIconHorizontalButton">
                                <li>
                                    <a href="#"
                                        class="block py-2 px-4 hover:bg-stone-100 dark:hover:bg-stone-600 dark:hover:text-white">Edit</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="block py-2 px-4 hover:bg-stone-100 dark:hover:bg-stone-600 dark:hover:text-white">Remove</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="block py-2 px-4 hover:bg-stone-100 dark:hover:bg-stone-600 dark:hover:text-white">Report</a>
                                </li>
                            </ul>
                        </div> --}}
                    </footer>
                    <p class="text-gray-500 dark:text-gray-400">{{ $comment->comment }}</p>
                    {{-- <div class="flex items-center mt-4 space-x-4">
                        <button type="button"
                            class="flex items-center text-sm text-gray-500 hover:underline dark:text-gray-400 font-medium">
                            <svg class="mr-1.5 w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 18">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5h5M5 8h2m6-3h2m-5 3h6m2-7H2a1 1 0 0 0-1 1v9a1 1 0 0 0 1 1h3v5l5-5h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1Z"/>
                            </svg>
                            Reply
                        </button>
                    </div> --}}
                </article>
            @endforeach
        @endif
    </div>
</section>