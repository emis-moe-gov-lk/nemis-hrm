<section class="w-full">
    <x-main-tables.layout>
        <div class="max-w-[1200px] mx-auto pb-12 px-4 lg:px-0">
            
            {{-- Section Header & Action Bar --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
                <div class="space-y-1">
                    <flux:heading size="xl" level="1" class="!font-black tracking-tight text-slate-900 dark:text-white">
                        {{ __('Civil Status') }}
                    </flux:heading>
                    <flux:subheading size="lg" class="flex items-center gap-2">
                        <flux:icon.user-circle variant="micro" class="text-indigo-500" />
                        {{ __('Manage Civil Status and related information') }}
                    </flux:subheading>
                </div>

                <div class="flex items-center gap-3">
                    <flux:modal.trigger name="add-new-civil-status">
                        <flux:button icon="plus" color="primary" class="w-full md:w-auto !rounded-2xl shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all">
                            {{ __('Add new Civil Status') }}
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

            {{-- CARDS GRID --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
                @forelse ($civilStatus as $key => $data)
                    <div class="relative group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2.5rem] p-6 shadow-sm hover:shadow-xl hover:border-indigo-300 dark:hover:border-indigo-800 transition-all duration-300">
                        
                        {{-- Top Metadata Row --}}
                        <div class="flex justify-between items-center mb-5">
                            <span class="text-[10px] font-black text-slate-400 tabular-nums tracking-widest uppercase">
                                #{{ $civilStatus->firstItem() + $key }}
                            </span>
                            <flux:badge size="sm" variant="pill" color="{{ $data->active_status ? 'green' : 'red' }}" class="!font-black uppercase tracking-widest text-[9px]">
                                {{ $data->active_status ? 'Active' : 'Inactive' }}
                            </flux:badge>
                        </div>

                        {{-- Core Content --}}
                        <div class="mb-6">
                            <div class="text-[10px] font-bold text-indigo-500 uppercase tracking-tighter mb-1">
                                {{ $data->civil_status_id }}
                            </div>
                            <h3 class="text-xl font-black text-slate-900 dark:text-white leading-tight">
                                {{ $data->civil_status_name }}
                            </h3>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex gap-2 pt-4 border-t border-slate-50 dark:border-slate-800">
                            <flux:modal.trigger wire:click="editCivilStatus({{ $data->id }})" class="flex-1">
                                <flux:button size="sm" variant="ghost" icon="pencil-square" class="w-full !rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800">
                                    {{ __('Edit') }}
                                </flux:button>
                            </flux:modal.trigger>
                            
                            <flux:button 
                                wire:click="toggleStatus({{ $data->id }})"
                                wire:confirm="Are you sure you want to change status?"
                                size="sm" 
                                variant="filled" 
                                color="{{ $data->active_status == '1' ? 'red' : 'green' }}"
                                icon="{{ $data->active_status == '1' ? 'no-symbol' : 'check' }}"
                                class="!rounded-xl shadow-sm"
                            />
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-24 text-center">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-[2rem] bg-slate-50 dark:bg-slate-900 border-2 border-dashed border-slate-200 dark:border-slate-800 mb-4 text-slate-300">
                            <flux:icon.user-circle size="xl" />
                        </div>
                        <h3 class="text-lg font-black text-slate-400">{{ __('No Civil Status Found') }}</h3>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-12 px-6">
                {{ $civilStatus->links() }}
            </div>
        </div>

        {{-- MODAL: ADD NEW --}}
        <flux:modal wire:model="showModelNewCivilStatus" name="add-new-civil-status" class="w-full max-w-md rounded-[2.5rem] p-8">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-indigo-50 dark:bg-indigo-900/30 rounded-[1.5rem] flex items-center justify-center text-indigo-600">
                        <flux:icon.plus />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black tracking-tight">{{ __('Add new Civil Status') }}</flux:heading>
                        <flux:text>{{ __('Create a new classification for your system.') }}</flux:text>
                    </div>
                </div>

                @if (session()->has('error'))
                    <div class="p-4 rounded-2xl bg-red-50 border border-red-100 text-red-800 text-xs font-bold">
                        {{ session('error') }}
                    </div>
                @endif

                <form wire:submit.prevent="addNewCivilStatus" class="space-y-5">
                    @csrf
                    <flux:field>
                        <flux:input label="Civil Status ID" wire:model.live="civilStatusId" placeholder="e.g. C01" mask="C99" />
                    </flux:field>

                    <flux:field>
                        <flux:input label="Civil Status Name" wire:model.live="civilStatusName" placeholder="e.g. Married" />
                    </flux:field>

                    <div class="flex gap-3 pt-4">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-xl">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-xl shadow-lg shadow-indigo-500/20">{{ __('Add Civil Status') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- MODAL: EDIT --}}
        <flux:modal wire:model="showModelEditCivilStatus" name="edit-civil-status" class="w-full max-w-md rounded-[2.5rem] p-8">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-amber-50 dark:bg-amber-900/30 rounded-[1.5rem] flex items-center justify-center text-amber-600">
                        <flux:icon.pencil-square />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black tracking-tight">{{ __('Edit Civil Status') }}</flux:heading>
                        <flux:text>{{ __('Modify existing status information.') }}</flux:text>
                    </div>
                </div>

                <form wire:submit.prevent="updateCivilStatusList" class="space-y-5">
                    @csrf
                    <flux:field>
                        <flux:input disabled label="Civil Status ID" wire:model.live="updateCivilStatusId" mask="C99" class="bg-slate-50" />
                    </flux:field>

                    <flux:field>
                        <flux:input label="Civil Status Name" wire:model.live="updateCivilStatus" />
                    </flux:field>

                    <div class="flex gap-3 pt-4">
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