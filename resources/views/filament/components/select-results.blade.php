<div class="flex rounded-md relative">
    <div class="flex">

        @if($record->avatar_url)
            <div class="px-2 mt-1">
                <div class="h-10 w-10">
                    <img src="{{ url('/storage/' . $record->avatar_url) }}" alt="{{ $record->name }}" role="img" class="h-full w-full rounded-full overflow-hidden shadow object-cover" />
                </div>
            </div>
        @else

        <div class="px-2 mt-1">
            <div class="h-10 w-10">
                <img src="{{ asset('images/avatar_placeholder.png') }}" alt="{{ $record->name }}" role="img" class="w-full rounded-full overflow-hidden shadow object-cover" />
            </div>
        </div>
        @endif
 
        <div class="flex flex-col justify-center pl-3 py-2">
            <p class="text-sm font-bold pb-1">{{ $record->name }}</p>
        </div>
    </div>
</div>