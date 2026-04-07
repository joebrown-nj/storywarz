<img 
    src="@if ($user->avatar){{ Storage::url($user->avatar) }}@else http://ui-avatars.com/api/?name={{ urlencode($user->name) }}&color=7f9cf5&background=ebf4ff @endif" 
    alt="{{ $user->name }}" 
    class="rounded-full h-16 w-16 object-cover {{ $class ?? '' }}"
    @if (isset($width))
        style="width: {{ $width }}px; height: {{ $width }}px;"
    @endif
>