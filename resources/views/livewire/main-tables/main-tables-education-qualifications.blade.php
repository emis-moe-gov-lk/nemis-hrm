<section class="w-full">
    
    <x-main-tables.layout>
        <div class="max-w-[1400px] mx-auto pb-12 px-4 lg:px-0">
            
            {{-- Section Header & Action Bar --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
                <div class="space-y-1">
                    <flux:heading size="xl" level="1" class="!font-black tracking-tight text-slate-900 dark:text-white uppercase">
                        {{ __('Education Qualifications') }}
                    </flux:heading>
                    <flux:subheading size="lg" class="flex items-center gap-2">
                        <flux:icon.academic-cap variant="micro" class="text-indigo-500" />
                        {{ __('Configure SLQF levels and NVQ vocational standards') }}
                    </flux:subheading>
                </div>

                <div class="flex items-center gap-3">
                    <flux:modal.trigger name="add-new-qualification">
                        <flux:button icon="plus" color="primary" class="w-full md:w-auto !rounded-2xl shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all">
                            {{ __('Add Qualification') }}
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

            {{-- QUALIFICATION CARDS GRID --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($eqList as $key => $data)
                    <div class="relative group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2.5rem] p-6 shadow-sm hover:shadow-xl hover:border-indigo-300 dark:hover:border-indigo-800 transition-all duration-300">
                        
                        {{-- Badge & Index --}}
                        <div class="flex justify-between items-center mb-5">
                            <span class="text-[10px] font-black text-slate-400 tabular-nums tracking-widest uppercase">
                                #{{ $eqList->firstItem() + $key }}
                            </span>
                            <flux:badge size="sm" variant="pill" color="{{ $data->active_status ? 'green' : 'red' }}" class="!font-black uppercase tracking-widest text-[9px]">
                                {{ $data->active_status ? 'Active' : 'Inactive' }}
                            </flux:badge>
                        </div>

                        {{-- Identity Section --}}
                        <div class="mb-5 min-h-[64px]">
                            <h3 class="text-lg font-black text-slate-900 dark:text-white leading-tight group-hover:text-indigo-600 transition-colors uppercase italic">
                                {{ $data->qualification }}
                            </h3>
                            <div class="inline-block mt-1 px-2 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-900/30 text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-tighter">
                                ID: {{ $data->qualifications_id }}
                            </div>
                        </div>

                        {{-- Framework Section (Replaces Metadata) --}}
                        <div class="space-y-4 mb-6">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-2xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 group-hover:text-indigo-500 transition-colors">
                                    <flux:icon.queue-list variant="micro" />
                                </div>
                                <div>
                                    <p class="text-[9px] uppercase font-black text-slate-400 leading-none mb-1">{{ __('Display Order') }}</p>
                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ str_pad($data->qualification_order, 2, '0', STR_PAD_LEFT) }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Levels Chip (Matching City GPS Chip) --}}
                        <div class="flex items-center gap-2 p-3 bg-slate-50/50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 rounded-2xl mb-6">
                            <div class="flex-1 text-center">
                                <p class="text-[8px] uppercase font-black text-slate-400">SLQF</p>
                                <p class="text-[11px] font-bold tabular-nums text-slate-600 dark:text-slate-400">{{ $data->slql ?? '—' }}</p>
                            </div>
                            <div class="w-px h-4 bg-slate-200 dark:bg-slate-700"></div>
                            <div class="flex-1 text-center">
                                <p class="text-[8px] uppercase font-black text-slate-400">NVQ</p>
                                <p class="text-[11px] font-bold tabular-nums text-slate-600 dark:text-slate-400">{{ $data->nvql ?? '—' }}</p>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex gap-2">
                            <flux:modal.trigger wire:click="editEQLevel({{ $data->id }})" class="flex-1">
                                <flux:button size="sm" variant="ghost" icon="pencil-square" class="w-full !rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800">
                                    {{ __('Edit') }}
                                </flux:button>
                            </flux:modal.trigger>
                            
                            <flux:button 
                                wire:click="toggleStatus({{ $data->id }})"
                                wire:confirm="Are you sure you want to change status?"
                                size="sm" 
                                variant="filled" 
                                color="{{ $data->active_status ? 'red' : 'green' }}"
                                icon="{{ $data->active_status ? 'no-symbol' : 'check' }}"
                                class="!rounded-xl shadow-sm"
                            />
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-24 text-center">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-[2rem] bg-slate-50 dark:bg-slate-900 border-2 border-dashed border-slate-200 dark:border-slate-800 mb-4 text-slate-300">
                            <flux:icon.academic-cap size="xl" />
                        </div>
                        <h3 class="text-lg font-black text-slate-400 uppercase tracking-widest">{{ __('No Records Found') }}</h3>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-12">
                {{ $eqList->links() }}
            </div>
        </div>

        {{-- MODAL: ADD QUALIFICATION --}}
        <flux:modal wire:model="showModelNewEducationQualification" name="add-new-qualification" class="w-full max-w-xl rounded-[2.5rem] p-8">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-indigo-50 dark:bg-indigo-900/30 rounded-[1.5rem] flex items-center justify-center text-indigo-600">
                        <flux:icon.plus />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black tracking-tight uppercase">{{ __('Add Qualification') }}</flux:heading>
                        <flux:text>{{ __('Register a new educational certification level.') }}</flux:text>
                    </div>
                </div>

                <form wire:submit.prevent="addNewEducationQualification" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:input label="Qualification ID" wire:model.live="educationQualificationId" mask="EQ999" placeholder="EQ000" />
                        </flux:field>
                        <flux:field>
                            <flux:input label="Display Order" wire:model.live="qualificationOrder" mask="99" />
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:input label="Qualification Name" wire:model.live="educationQualification" placeholder="e.g. Bachelor of Science" />
                    </flux:field>

                    <div class="p-6 bg-slate-50 dark:bg-slate-800/50 rounded-[2rem] border border-slate-100 dark:border-slate-800 space-y-4">
                        <flux:heading size="sm" class="!font-black uppercase tracking-widest text-slate-400 text-[10px]">{{ __('Framework Mapping') }}</flux:heading>
                        <div class="grid grid-cols-2 gap-4">
                            <flux:field>
                                <flux:select label="SLQF Level" wire:model.live="slqfl">
                                    <option value="">Select</option>
                                    @for ($i = 1; $i <= 12; $i++) <option value="SLQF{{ $i }}">Level {{ $i }}</option> @endfor
                                </flux:select>
                            </flux:field>
                            <flux:field>
                                <flux:select label="NVQ Level" wire:model.live="nvql">
                                    <option value="">Select</option>
                                    @for ($i = 1; $i <= 7; $i++) <option value="NVQ{{ $i }}">Level {{ $i }}</option> @endfor
                                </flux:select>
                            </flux:field>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-xl">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-xl shadow-lg shadow-indigo-500/20">{{ __('Add Qualification') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- MODAL: EDIT QUALIFICATION --}}
        <flux:modal wire:model="showModelEditEducationQualification" name="edit-qualification" class="w-full max-w-xl rounded-[2.5rem] p-8">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-indigo-50 dark:bg-indigo-900/30 rounded-[1.5rem] flex items-center justify-center text-indigo-600">
                        <flux:icon.pencil-square />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black tracking-tight uppercase">{{ __('Edit Record') }}</flux:heading>
                        <flux:text>{{ __('Modify qualification name and framework levels.') }}</flux:text>
                    </div>
                </div>

                <form wire:submit.prevent="updateEducationQualificationlist" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:input disabled label="Qualification ID" wire:model.live="updateEducationQualificationId" />
                        </flux:field>
                        <flux:field>
                            <flux:input label="Display Order" wire:model.live="updateQualificationOrder" />
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:input label="Qualification Name" wire:model.live="updateEducationQualification" />
                    </flux:field>

                    <div class="grid grid-cols-2 gap-4 p-5 bg-slate-50 dark:bg-slate-800/50 rounded-3xl border border-slate-100 dark:border-slate-800">
                        <flux:field>
                            <flux:select label="SLQF Level" wire:model.live="updateSlqfl">
                                <option value="">Select</option>
                                @for ($i = 1; $i <= 12; $i++) <option value="SLQF{{ $i }}">Level {{ $i }}</option> @endfor
                            </flux:select>
                        </flux:field>
                        <flux:field>
                            <flux:select label="NVQ Level" wire:model.live="updateNvql">
                                <option value="">Select</option>
                                @for ($i = 1; $i <= 7; $i++) <option value="NVQ{{ $i }}">Level {{ $i }}</option> @endfor
                            </flux:select>
                        </flux:field>
                    </div>

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