<section class="w-full">
    <x-main-tables.layout>
        <div class="max-w-[1400px] mx-auto pb-12 px-4 lg:px-0">
            
            {{-- Section Header & Action Bar --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
                <div class="space-y-1">
                    <flux:heading size="xl" level="1" class="!font-black tracking-tight text-slate-900 dark:text-white uppercase">
                        {{ __('Personal Titles') }}
                    </flux:heading>
                    <flux:subheading size="lg" class="flex items-center gap-2">
                        <flux:icon.identification variant="micro" class="text-indigo-500" />
                        {{ __('Manage honorifics and formal address titles') }}
                    </flux:subheading>
                </div>

                <div class="flex items-center gap-3">
                    <flux:modal.trigger name="add-new-title">
                        <flux:button icon="plus" color="primary" class="w-full md:w-auto !rounded-2xl shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all">
                            {{ __('Add new Title') }}
                        </flux:button>
                    </flux:modal.trigger>
                </div>
            </div>

            {{-- Notifications --}}
            @if (session()->has('message'))
                <div class="mb-6 animate-in fade-in slide-in-from-top-4 duration-500">
                    <div class="flex items-center gap-3 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400">
                        <flux:icon.check-circle variant="micro" class="shrink-0" />
                        <span class="text-sm font-bold">{{ session('message') }}</span>
                    </div>
                </div>
            @endif

            {{-- TITLES CARDS GRID --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-4">
                @forelse ($titles as $key => $data)
                    <div class="group relative flex items-center justify-between bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-3xl p-5 shadow-sm hover:shadow-md hover:border-indigo-500/30 transition-all duration-200">
                        
                        <div class="flex items-center gap-4">
                            {{-- Index / ID Avatar --}}
                            <div class="flex items-center justify-center w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-800 text-slate-500 font-black text-xs border border-slate-200 dark:border-slate-700 uppercase">
                                {{ $data->title_id }}
                            </div>

                            <div>
                                <h3 class="text-lg font-black text-slate-900 dark:text-white tracking-tight leading-none mb-1">
                                    {{ $data->title_name }}
                                </h3>
                                <div class="flex items-center gap-1.5">
                                    <div class="w-1.5 h-1.5 rounded-full {{ $data->active_status ? 'bg-emerald-500' : 'bg-rose-500' }}"></div>
                                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-500">
                                        {{ $data->active_status ? 'Available' : 'Disabled' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Hover Actions --}}
                        <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <flux:modal.trigger name="edit-title" wire:click="editTitle({{ $data->id }})">
                                <flux:button size="sm" variant="ghost" icon="pencil-square" class="!rounded-xl" />
                            </flux:modal.trigger>
                            
                            <flux:button 
                                wire:click="toggleStatus({{ $data->id }})"
                                wire:confirm="Are you sure you want to {{ $data->active_status ? 'deactivate' : 'activate' }} this Title?"
                                size="sm" 
                                variant="ghost" 
                                color="{{ $data->active_status ? 'red' : 'primary' }}"
                                icon="{{ $data->active_status ? 'no-symbol' : 'check' }}"
                                class="!rounded-xl"
                            />
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center">
                        <flux:icon.identification size="xl" class="mx-auto text-slate-200 mb-4" />
                        <h3 class="text-sm font-black text-slate-500 uppercase tracking-widest">{{ __('No Titles Defined') }}</h3>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $titles->links() }}
            </div>
        </div>

        {{-- MODAL: ADD NEW TITLE --}}
        <flux:modal wire:model="showModelNewTitle" name="add-new-title" class="w-full max-w-md rounded-[2.5rem] p-8">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-indigo-50 dark:bg-indigo-900/30 rounded-[1.5rem] flex items-center justify-center text-indigo-600">
                        <flux:icon.plus />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black tracking-tight uppercase">{{ __('New Title') }}</flux:heading>
                        <flux:text>{{ __('Create a new honorific for the system.') }}</flux:text>
                    </div>
                </div>

                <form wire:submit.prevent="addNewTitle" class="space-y-6">
                    @csrf
                    <div class="space-y-4">
                        <flux:field>
                            <flux:input label="Title ID" wire:model.live="titleId" placeholder="e.g. T01" mask="T99" class="!rounded-xl" />
                        </flux:field>

                        <flux:field>
                            <flux:input label="Title Name" wire:model.live="title" placeholder="e.g. Mr, Mrs, Dr." class="!rounded-xl" />
                        </flux:field>
                    </div>

                    <div class="flex gap-3">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-xl">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-xl shadow-lg shadow-indigo-500/20">{{ __('Create Title') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- MODAL: EDIT TITLE --}}
        <flux:modal wire:model="showModelEditTitle" name="edit-title" class="w-full max-w-md rounded-[2.5rem] p-8">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-indigo-50 dark:bg-indigo-900/30 rounded-[1.5rem] flex items-center justify-center text-indigo-600">
                        <flux:icon.pencil-square />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black tracking-tight uppercase">{{ __('Edit Title') }}</flux:heading>
                        <flux:text>{{ __('Update the honorific label.') }}</flux:text>
                    </div>
                </div>

                <form wire:submit.prevent="updateTitleList" class="space-y-6">
                    @csrf
                    <div class="space-y-4">
                        <flux:field>
                            <flux:input disabled label="Title ID" wire:model.live="updateTitleId" class="!rounded-xl opacity-70" />
                        </flux:field>

                        <flux:field>
                            <flux:input label="Title Name" wire:model.live="updateTitle" class="!rounded-xl" />
                        </flux:field>
                    </div>

                    <div class="flex gap-3">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-xl">{{ __('Discard') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-xl shadow-lg shadow-indigo-500/20">{{ __('Save Changes') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

    </x-main-tables.layout>
</section>