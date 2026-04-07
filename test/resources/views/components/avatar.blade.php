<img 
    src="@if ($src){{ Storage::url($src) }}@else http://ui-avatars.com/api/?name={{ urlencode($name) }}&color=7f9cf5&background=ebf4ff @endif" 
    alt="{{ $name }}" 
    class="rounded-full h-16 w-16 object-cover {{ $class ?? '' }}"
    @if (isset($width))
        style="width: {{ $width }}px; height: {{ $width }}px;"
    @endif
>