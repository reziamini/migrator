<div class="w-full">

    @if(session()->has('message'))
        <div class="my-4 border border-green-400 px-4 py-4 mx-4 text-green-700 text-sm font-light rounded bg-green-200 text">
            {!! session()->get('message') !!}
        </div>
    @endif

    @livewire('migrator::livewire.migration.create')

    <div class="mt-8 flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ran?
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created At
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Action
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($migrations as $key => $migration)
                                @livewire('migrator::livewire.migration.single', ['migration' => $migration], key($loop->iteration))
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="my-4 flex flex-col sm:flex-row">
        <div class="sm:w-1/2 w-full items-center mt-3 sm:mt-0 px-5">
            <input wire:click.prevent="migrate" type="submit" class="cursor-pointer hover:bg-green-600 rounded w-full py-2 bg-green-500 text-white" value="Migrate all">
        </div>
        <div class="sm:w-1/2 w-full items-center mt-3 sm:mt-0 px-5">
            <input wire:click.prevent="fresh" type="submit" class="cursor-pointer hover:bg-red-600 rounded w-full py-2 bg-red-500 text-white" value="Fresh database">
        </div>
    </div>

    <div class="mt-12 mb-4 text-center font-light text-xs text-gray-700">
        <a class="text-decoration-none text-indigo-500" target="_blank" href="https://github.com/rezaamini-ir/migrator">Migrator</a> is a GUI migration manager for Laravel.
    </div>
</div>
