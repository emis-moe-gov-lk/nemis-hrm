

    <x-main-tables.layout>
        <div class="max-w-[1440px] mx-auto pb-12">
            
            {{-- Section Header & Action Bar --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12 px-4">
                <div class="space-y-1">
                    <flux:heading size="xl" level="1" class="!font-black tracking-tight text-indigo-600 dark:text-indigo-400 uppercase leading-none">
                        {{ __('Occupations') }}
                    </flux:heading>
                    <div class="flex items-center gap-2">
                        <div class="h-1.5 w-8 bg-indigo-500 rounded-full"></div>
                        <flux:subheading size="lg" class="!font-bold text-slate-500 uppercase tracking-widest text-[11px]">
                            {{ __('Career Classification & Localization') }}
                        </flux:subheading>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <flux:modal.trigger name="add-new-occupation">
                        <flux:button icon="plus" color="primary" class="w-full sm:w-auto !rounded-2xl shadow-xl shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all hover:-translate-y-1">
                            {{ __('Add New Occupation') }}
                        </flux:button>
                    </flux:modal.trigger>
                </div>
            </div>

            {{-- Flash Messages --}}
            @if (session()->has('message'))
                <div class="mb-10 px-4 animate-in fade-in slide-in-from-top-4 duration-500">
                    <div class="flex items-center gap-3 p-4 rounded-[1.5rem] bg-emerald-50 border border-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400">
                        <flux:icon.check-circle variant="micro" class="shrink-0" />
                        <span class="text-sm font-black uppercase tracking-tight">{{ session('message') }}</span>
                    </div>
                </div>
            @endif

            {{-- CARD GRID - NO TABLES HERE --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-8 px-4">
                @forelse ($occupations as $key => $data)
                    <div class="group relative flex flex-col bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-[3rem] p-8 hover:shadow-[0_40px_80px_-15px_rgba(0,0,0,0.1)] dark:hover:shadow-[0_40px_80px_-15px_rgba(0,0,0,0.5)] hover:border-indigo-500/50 transition-all duration-500">
                        
                        {{-- Card Header: ID & Toggle --}}
                        <div class="flex justify-between items-start mb-8">
                            <span class="text-[10px] font-black uppercase tracking-widest px-4 py-1 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-500 rounded-full border border-slate-300 dark:border-slate-700">
                                {{ $data->occ_id }}
                            </span>
                            
                            <div class="flex items-center gap-2">
                                <div class="h-2 w-2 rounded-full {{ $data->active_status ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.6)]' : 'bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.6)]' }}"></div>
                                <span class="text-[10px] font-black uppercase tracking-tighter {{ $data->active_status ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $data->active_status ? 'Active' : 'Disabled' }}
                                </span>
                            </div>
                        </div>

                        {{-- Identity Section --}}
                        <div class="mb-8">
                            <h3 class="text-xl font-black text-slate-900 dark:text-white leading-tight uppercase tracking-tight group-hover:text-indigo-600 transition-colors">
                                {{ $data->occ_name_en }}
                            </h3>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mt-1">Classification Unit</p>
                        </div>

                        {{-- Translations Stack --}}
                        <div class="space-y-3 mb-10">
                            <div class="p-4 rounded-[1.5rem] bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 group-hover:bg-indigo-50/30 transition-colors">
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-black text-indigo-500 uppercase tracking-widest mb-1 italic">Sinhala Translation</span>
                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ $data->occ_name_si }}</span>
                                </div>
                            </div>
                            <div class="p-4 rounded-[1.5rem] bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 group-hover:bg-indigo-50/30 transition-colors">
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-black text-indigo-500 uppercase tracking-widest mb-1 italic">Tamil Translation</span>
                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ $data->occ_name_ta }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Card Actions --}}
                        <div class="mt-auto pt-6 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between">
                             <div class="flex items-center gap-2 text-slate-300 font-bold text-xs uppercase tracking-widest">
                                <flux:icon.hashtag variant="micro" />
                                <span>{{ $occupations->firstItem() + $key }}</span>
                             </div>
                             
                             <div class="flex items-center gap-2">
                                <flux:modal.trigger wire:click="editOccupation({{ $data->id }})" name="edit-occupation">
                                    <flux:button size="sm" variant="ghost" icon="pencil-square" class="!rounded-xl hover:!bg-indigo-600 hover:!text-white transition-all shadow-sm" />
                                </flux:modal.trigger>
                                
                                <flux:button 
                                    wire:click="toggleStatus({{ $data->id }})"
                                    wire:confirm="Confirm status change for: {{ $data->occ_name_en }}?"
                                    size="sm" 
                                    variant="ghost" 
                                    icon="{{ $data->active_status ? 'no-symbol' : 'check' }}"
                                    class="!rounded-xl {{ $data->active_status ? 'hover:!bg-rose-500 hover:!text-white' : 'hover:!bg-emerald-500 hover:!text-white' }} transition-all shadow-sm" 
                                />
                             </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-32 flex flex-col items-center justify-center bg-slate-50 dark:bg-slate-900/40 rounded-[4rem] border-2 border-dashed border-slate-300 dark:border-slate-700">
                        <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-6">
                            <flux:icon.briefcase size="xl" class="text-slate-300" />
                        </div>
                        <p class="text-slate-500 font-black uppercase tracking-[0.3em] text-sm">{{ __('No Occupations Found') }}</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-16 px-4">
                {{ $occupations->links() }}
            </div>
        </div>

        {{-- MODAL: ADD --}}
        <flux:modal wire:model="showModelNewOccupation" name="add-new-occupation" class="w-full max-w-lg rounded-[3rem] p-12">
            <div class="space-y-8">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 bg-indigo-600 rounded-[1.5rem] flex items-center justify-center text-white shadow-2xl shadow-indigo-500/40">
                        <flux:icon.plus size="lg" />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black uppercase tracking-tighter leading-none">{{ __('New Occupation') }}</flux:heading>
                        <flux:text size="sm" class="mt-1">{{ __('Define a new career path in the system.') }}</flux:text>
                    </div>
                </div>

                <form wire:submit.prevent="addNewOccupation" class="space-y-5">
                    @csrf
                    <flux:field>
                        <flux:input label="Occupation ID" wire:model.live="occupations_id" mask="OCID999" placeholder="OCID001" class="!rounded-[1.25rem] !py-3" />
                    </flux:field>

                    <div class="grid grid-cols-1 gap-4">
                        <flux:field>
                            <flux:input label="Title (English)" wire:model.live="occupations_name_en" placeholder="e.g. Software Engineer" class="!rounded-[1.25rem] !py-3" />
                        </flux:field>
                        <flux:field>
                            <flux:input label="Title (Sinhala)" wire:model.live="occupations_name_si" placeholder="e.g. මෘදුකාංග ඉංජිනේරු" class="!rounded-[1.25rem] !py-3" />
                        </flux:field>
                        <flux:field>
                            <flux:input label="Title (Tamil)" wire:model.live="occupations_name_ta" placeholder="e.g. மென்பொருள் பொறியாளர்" class="!rounded-[1.25rem] !py-3" />
                        </flux:field>
                    </div>

                    <div class="flex gap-4 pt-6">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-[1.25rem] !py-3">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-[1.25rem] !py-3 shadow-xl shadow-indigo-500/30">{{ __('Save Occupation') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- MODAL: EDIT --}}
        <flux:modal wire:model="showModelEditOccupation" name="edit-occupation" class="w-full max-w-lg rounded-[3rem] p-12">
            <div class="space-y-8">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 bg-indigo-600 dark:bg-white rounded-[1.5rem] flex items-center justify-center text-white dark:text-slate-900 shadow-2xl">
                        <flux:icon.pencil-square size="lg" />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black uppercase tracking-tighter leading-none">{{ __('Edit Profile') }}</flux:heading>
                        <flux:text size="sm" class="mt-1">{{ __('Update details and translations.') }}</flux:text>
                    </div>
                </div>

                <form wire:submit.prevent="updateOccupation" class="space-y-5">
                    @csrf
                    <flux:field>
                        <flux:input disabled label="Occupation ID" wire:model.live="update_occupations_id" mask="OCID999" class="!rounded-[1.25rem] !py-3 bg-slate-50 opacity-50" />
                    </flux:field>

                    <div class="grid grid-cols-1 gap-4">
                        <flux:field>
                            <flux:input label="Title (English)" wire:model.live="update_occupations_name_en" class="!rounded-[1.25rem] !py-3" />
                        </flux:field>
                        <flux:field>
                            <flux:input label="Title (Sinhala)" wire:model.live="update_occupations_name_si" class="!rounded-[1.25rem] !py-3" />
                        </flux:field>
                        <flux:field>
                            <flux:input label="Title (Tamil)" wire:model.live="update_occupations_name_ta" class="!rounded-[1.25rem] !py-3" />
                        </flux:field>
                    </div>

                    <div class="flex gap-4 pt-6">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-[1.25rem] !py-3">{{ __('Discard') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-[1.25rem] !py-3 shadow-xl shadow-indigo-500/30">{{ __('Update Occupation') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

    </x-main-tables.layout>