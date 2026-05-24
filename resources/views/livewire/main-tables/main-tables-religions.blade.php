<section class="w-full">
    <x-main-tables.layout>
        <div class="max-w-[1400px] mx-auto pb-12 px-4 lg:px-0">
            
            {{-- Section Header & Action Bar --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
                <div class="space-y-1">
                    <flux:heading size="xl" level="1" class="!font-black tracking-tight text-indigo-600 dark:text-indigo-400 uppercase">
                        {{ __('Religion Settings') }}
                    </flux:heading>
                    <flux:subheading size="lg" class="flex items-center gap-2">
                        <flux:icon.heart variant="micro" class="text-slate-500" />
                        {{ __('Define religious affiliations used across the platform') }}
                    </flux:subheading>
                </div>

                <div class="flex items-center gap-3">
                    <flux:modal.trigger name="add-new-religion">
                        <flux:button icon="plus" color="primary" class="w-full md:w-auto !rounded-2xl shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all">
                            {{ __('Add New Religion') }}
                        </flux:button>
                    </flux:modal.trigger>
                </div>
            </div>

            {{-- Success Notification --}}
            @if (session()->has('message'))
                <div class="mb-6 animate-in fade-in slide-in-from-top-4 duration-500">
                    <div class="flex items-center gap-3 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400">
                        <flux:icon.check-circle variant="micro" class="shrink-0" />
                        <span class="text-sm font-bold">{{ session('message') }}</span>
                    </div>
                </div>
            @endif

            {{-- COMPACT CARD GRID --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @forelse ($religion as $key => $data)
                    <div class="group relative bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-3xl p-5 shadow-sm hover:shadow-xl hover:border-indigo-500/30 transition-all duration-300">
                        
                        <div class="flex justify-between items-start">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 leading-none mb-1">
                                    {{ $data->religion_id }}
                                </span>
                                <h3 class="text-base font-black text-slate-900 dark:text-white uppercase tracking-tight group-hover:text-indigo-600 transition-colors">
                                    {{ $data->religion_name }}
                                </h3>
                            </div>
                            
                            <div class="flex flex-col items-end gap-3">
                                <span class="inline-flex h-2 w-2 rounded-full {{ $data->active_status ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-rose-500' }}"></span>
                                
                                <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <flux:modal.trigger wire:click="editReligion({{ $data->id }})" name="edit-religion">
                                        <flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-lg text-slate-500 hover:text-indigo-600" />
                                    </flux:modal.trigger>
                                    
                                    <flux:button 
                                        wire:click="toggleStatus({{ $data->id }})"
                                        wire:confirm="Change status for {{ $data->religion_name }}?"
                                        size="xs" 
                                        variant="ghost" 
                                        icon="{{ $data->active_status ? 'no-symbol' : 'check' }}" 
                                        class="!rounded-lg {{ $data->active_status ? 'text-rose-400 hover:text-rose-600' : 'text-emerald-400 hover:text-emerald-600' }}" 
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center bg-slate-50/50 dark:bg-slate-900/50 rounded-[2.5rem] border-2 border-dashed border-slate-300 dark:border-slate-700">
                        <flux:icon.heart size="xl" class="mx-auto text-slate-200 mb-4" />
                        <h3 class="text-sm font-black text-slate-500 uppercase tracking-widest">{{ __('No Records Found') }}</h3>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-12">
                {{ $religion->links() }}
            </div>
        </div>

        {{-- MODAL: ADD RELIGION --}}
        <flux:modal wire:model="showModelNewReligion" name="add-new-religion" class="w-full max-w-sm rounded-[2.5rem] p-8">
            <div class="space-y-6">
                <div class="text-center">
                    <flux:heading size="lg" class="!font-black tracking-tight uppercase">{{ __('New Religion') }}</flux:heading>
                    <flux:text class="text-xs">{{ __('Add a new classification to the system.') }}</flux:text>
                </div>

                @if (session()->has('error'))
                    <div class="p-3 rounded-xl bg-rose-50 border border-rose-100 text-rose-800 text-xs font-bold text-center">
                        {{ session('error') }}
                    </div>
                @endif

                <form wire:submit.prevent="addNewReligion" class="space-y-4">
                    @csrf
                    <flux:field>
                        <flux:input label="Code" wire:model.live="religionId" placeholder="R01" mask="R99" class="!rounded-xl" />
                    </flux:field>

                    <flux:field>
                        <flux:input label="Religion Name" wire:model.live="religionName" placeholder="e.g. Christianity" class="!rounded-xl" />
                    </flux:field>

                    <div class="flex gap-2 pt-2">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-xl">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-xl">{{ __('Add Entry') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- MODAL: EDIT RELIGION --}}
        <flux:modal wire:model="showModelEditReligion" name="edit-religion" class="w-full max-w-sm rounded-[2.5rem] p-8">
            <div class="space-y-6">
                <div class="text-center">
                    <flux:heading size="lg" class="!font-black tracking-tight uppercase">{{ __('Modify Religion') }}</flux:heading>
                    <flux:text class="text-xs">{{ __('Update existing demographic data.') }}</flux:text>
                </div>

                @if (session()->has('error'))
                    <div class="p-3 rounded-xl bg-rose-50 border border-rose-100 text-rose-800 text-xs font-bold text-center">
                        {{ session('error') }}
                    </div>
                @endif

                <form wire:submit.prevent="updateReligionList" class="space-y-4">
                    @csrf
                    <flux:field>
                        <flux:input disabled label="Code" wire:model.live="updateReligionId" class="!rounded-xl bg-slate-50 opacity-60" />
                    </flux:field>

                    <flux:field>
                        <flux:input label="Religion Name" wire:model.live="updateReligionName" class="!rounded-xl" />
                    </flux:field>

                    <div class="flex gap-2 pt-2">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-xl">{{ __('Discard') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-xl">{{ __('Save Changes') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>
    </x-main-tables.layout>
</section>