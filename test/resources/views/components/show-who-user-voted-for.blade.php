@foreach($votes as $vote)
    @if($vote->user_id == $u->id)
        <div class="p-2 text-center bg-gray-100 text-xs h-full">
            @if ($vote->voted_for_user_id == 1)
                Eliminated
            @else 
                Voted for<br>
                <strong>
                    @if ($vote->voted_for_user_id == Auth::id())
                        You
                    @else
                        {{ $vote->voted_for_name }}
                    @endif
                </strong>
            @endif
        </div>
    @endif
@endforeach