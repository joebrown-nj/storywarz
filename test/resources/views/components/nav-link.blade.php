@props(['active'])

@php
$classes = ($active ?? false)
            ? 'cursor-pointer inline-flex items-center px-1 pt-1 border-b-2 border-stone-400 dark:border-stone-600 text-sm font-medium leading-5 text-gray-900 dark:text-gray-100 focus:outline-none focus:border-stone-700 transition duration-150 ease-in-out'
            : 'cursor-pointer inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-stone-300 dark:hover:border-stone-700 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-stone-300 dark:focus:border-stone-700 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
