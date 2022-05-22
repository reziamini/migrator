<div class="w-full" x-data="{ showFreshModal: false, showMigrateModal: false }">

    @include('migrator::message')

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
                                Connection
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Batch
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created At
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <span class="mr-5">Action</span>
                                <input type="text" class="focus:outline-none focus:border-gray-400 border border-gray-200 rounded py-2 px-4 w-9/12 @error('search') border-red-300 @enderror" wire:model="search" placeholder="Search">
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($migrations as $key => $migration)
                                @livewire('migrator::livewire.migration.single', ['migration' => $migration], key($key))
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center">
                                            <div class="text-gray-500">
                                                No migration found.
                                            </div>
                                        </td>
                                    </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if($migrations->lastPage() > 1)
                        <div class="px-6 py-3">
                            {{ $migrations->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="my-4 flex flex-col sm:flex-row">
        <div class="sm:w-1/2 w-full items-center mt-3 sm:mt-0 px-5">
            <input x-on:click.prevent="showMigrateModal = true" type="submit" class="cursor-pointer hover:bg-green-600 rounded w-full py-2 bg-green-500 text-white" value="Migrate all">
        </div>
        <div class="sm:w-1/2 w-full items-center mt-3 sm:mt-0 px-5">
            <input x-on:click.prevent="showFreshModal = true" type="submit" class="cursor-pointer hover:bg-red-600 rounded w-full py-2 bg-red-500 text-white" value="Fresh database">
        </div>
    </div>

    <div style="display:none;" x-show="showFreshModal" class="fixed z-10 inset-0 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div  class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle w-3/5 h-1/2" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-12 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-headline">
                                Fresh migrations
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Are you sure to fresh migrations?
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-between sm:flex-row-reverse">
                    <div>
                        <button wire:click="fresh" x-on:click.prevent="showFreshModal = false" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-700 sm:ml-3 sm:w-auto sm:text-sm mt-3 sm:mt-0">
                            Fresh database
                        </button>
                        <button wire:click="fresh(true)" x-on:click.prevent="showFreshModal = false" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-700 sm:ml-3 sm:w-auto sm:text-sm mt-3 sm:mt-0">
                            Fresh and seed
                        </button>
                    </div>
                    <button x-on:click.prevent="showFreshModal = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm mt-3 sm:mt-0">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div style="display:none;" x-show="showMigrateModal" class="fixed z-10 inset-0 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div  class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle w-3/5 h-1/2" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-12 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-headline">
                                Migrate the migrations
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Are you sure to migrate the migrations?
                                </p>
                                <div class="mt-2">
                                    <span class="text-xs text-red-600 mt-4">Note: Safe migrate will fresh the database then re-run migrations!</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-between sm:flex-row-reverse">
                    <div>
                        <button wire:click="migrate" x-on:click.prevent="showMigrateModal = false" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 hover:bg-blue-700 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 sm:ml-3 sm:w-auto sm:text-sm mt-3 sm:mt-0">
                            Normal Migrate
                        </button>
                        <button wire:click="migrate(true)" x-on:click.prevent="showMigrateModal = false" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 hover:bg-blue-700 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 sm:ml-3 sm:w-auto sm:text-sm mt-3 sm:mt-0">
                            Safe Re-Migrate all
                        </button>
                    </div>
                    <button x-on:click.prevent="showMigrateModal = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm mt-3 sm:mt-0">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-12 mb-4 text-center font-light text-xs text-gray-700">
        <a class="text-decoration-none text-indigo-500" target="_blank" href="https://github.com/rezaamini-ir/migrator">Migrator</a> is a GUI migration manager for Laravel.
    </div>
</div>
