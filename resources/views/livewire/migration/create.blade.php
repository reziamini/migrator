<form class="flex flex-col sm:flex-row" wire:submit.prevent="create">

    <div class="sm:w-1/4 w-full flex flex-col items-center mt-3 sm:mt-0">
        <input type="text" class="focus:outline-none focus:border-gray-400 border border-gray-200 rounded py-2 px-4 w-11/12 @error('name') border-red-300 @enderror" wire:model.lazy="name" placeholder="Migration Name">
        @error('name')
            <span class="px-2 text-sm text-red-600">{{ $message }}</span>
        @enderror
    </div>

    <div class="sm:w-1/4 w-full flex flex-col items-center mt-3 sm:mt-0 ">
        <input type="text" class="focus:outline-none focus:border-gray-400 border border-gray-200 rounded py-2 px-4 w-11/12 @error('table') border-red-300 @enderror" wire:model.lazy="table" placeholder="Table Name">
        @error('table')
            <span class="px-2 text-sm text-red-600">{{ $message }}</span>
        @enderror
    </div>

    <div class="sm:w-1/4 w-full flex flex-col items-center mt-3 sm:mt-0 ">
        <select class="focus:outline-none focus:border-gray-400 border border-gray-200 rounded py-2 px-4 w-11/12 @error('connection') border-red-300 @enderror" wire:model.lazy="connection">
            @foreach($connections as $connection)
                <option value="{{ $connection }}">{{ $connection }} {{ $connection == config('database.default') ? '(Default)' : '' }}</option>
            @endforeach
        </select>
        @error('connection')
            <span class="px-2 text-sm text-red-600">{{ $message }}</span>
        @enderror
    </div>

    <div class="sm:w-1/4 w-full flex flex-col items-center mt-3 sm:mt-0">
        <select class="focus:outline-none focus:border-gray-400 border border-gray-200 rounded py-2 px-4 w-11/12 @error('type') border-red-300 @enderror" wire:model.lazy="type">
            <option value="create">Create</option>
            <option value="edit">Edit</option>
        </select>
        @error('type')
            <span class="px-2 text-sm text-red-600">{{ $message }}</span>
        @enderror
    </div>

    <div class="sm:w-1/4 w-full items-center mt-3 sm:mt-0 px-5">
        <input type="submit" class="cursor-pointer hover:bg-indigo-600 rounded w-full py-2 bg-indigo-500 text-white" value="Add">
    </div>
</form>
