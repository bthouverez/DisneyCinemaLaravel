<div class="">
    <h1 class="text-3xl font-bold text-center text-white m-4">Collection Disney Cinema pour Alba</h1>
    @foreach($this->books->keys() as $status)
        <p class="text-2xl ml-6 my-2 font-bold text-white">
            @php
                switch($status) {
                    case 'possessed': echo "Ceux qu'elle a déjà"; break;
                    case 'unpossessed': echo "Ceux qu'elle n'a pas"; break;
                    case 'wanted': echo "Ceux qu'elle aimerait avoir"; break;
                }
            @endphp
        </p>
        <div class="grid grid-cols-2 lg:grid-cols-6 gap-4 m-4">

            @foreach($this->books->get($status)->sortBy('title') as $book)
                <div class="col-md-4 shadow-sm rounded p-4 text-center bg-white" :key="{{ $book->id }}">
                    <a href="https://www.hachetteheroes.com/{{ $book->link }}}">
                        <img src="{{ $book->image }}" class="card-img-top mx-auto" alt="{{ $book->title }}">
                        <p class="font-bold">{{ ucwords(strtolower($book->title), " \t\r\n\f\v'-.") }}</p>
                    </a>
                        @if($edit)
                            <select wire:model.live="status.{{ $book->id  }}" class="border px-4 py-2 rounded border-gray-600 hover:cursor-pointer">
                                <option value="unpossessed" {{ $status == 'unpossessed' ? 'selected' : '' }}>Non possédé</option>
                                <option value="possessed" {{ $status == 'possessed' ? 'selected' : '' }}>Possédé</option>
                                <option value="wanted" {{ $status == 'wanted' ? 'selected' : '' }}>Souhaité</option>
                            </select>
                        @endif
                </div>
            @endforeach
        </div>
    @endforeach
</div>
