<section class="w-full">
    <x-main-tables.layout>
        <div class="max-w-[1400px] mx-auto pb-12 px-4 lg:px-0">
            
            {{-- Section Header & Action Bar --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
                <div class="space-y-1">
                    <flux:heading size="xl" level="1" class="!font-black tracking-tight text-slate-900 dark:text-white">
                        {{ __('Divisional Secretariat Offices') }}
                    </flux:heading>
                    <flux:subheading size="lg" class="flex items-center gap-2">
                        <flux:icon.building-office-2 variant="micro" class="text-indigo-500" />
                        {{ __('Manage regional administration units and district assignments') }}
                    </flux:subheading>
                </div>

                <div class="flex items-center gap-3">
                    <flux:modal.trigger name="add-new-ds-office">
                        <flux:button icon="plus" color="primary" class="w-full md:w-auto !rounded-2xl shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all">
                            {{ __('Add new DS Office') }}
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
                @forelse ($dsOfficeList as $key => $data)
                    <div class="relative group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2.5rem] p-6 shadow-sm hover:shadow-xl hover:border-indigo-300 dark:hover:border-indigo-800 transition-all duration-300">
                        
                        {{-- Top Badge Row --}}
                        <div class="flex justify-between items-center mb-5">
                            <span class="text-[10px] font-black text-slate-400 tabular-nums tracking-widest uppercase">
                                #{{ $dsOfficeList->firstItem() + $key }}
                            </span>
                            <flux:badge size="sm" variant="pill" color="{{ $data->active_status ? 'green' : 'red' }}" class="!font-black uppercase tracking-widest text-[9px]">
                                {{ $data->active_status ? 'Active' : 'Inactive' }}
                            </flux:badge>
                        </div>

                        {{-- Office Information --}}
                        <div class="mb-5">
                            <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-900/30 text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-tight mb-2">
                                <flux:icon.finger-print variant="micro" class="size-3" />
                                {{ $data->dso_id }}
                            </div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-white leading-tight min-h-[3rem] group-hover:text-indigo-600 transition-colors">
                                {{ $data->dso_name }}
                            </h3>
                        </div>

                        {{-- District Assignment --}}
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-[1.5rem] border border-slate-100 dark:border-slate-800 mb-6">
                            <p class="text-[9px] uppercase font-black text-slate-400 leading-none mb-2 tracking-widest">{{ __('Assigned District') }}</p>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-white dark:bg-slate-800 shadow-sm flex items-center justify-center text-indigo-500">
                                    <flux:icon.map-pin variant="micro" />
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ $data->district->district_name }}</p>
                                    <p class="text-[10px] font-medium text-slate-500 font-mono">{{ $data->district_id }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex gap-2">
                            <flux:modal.trigger wire:click="editDSOffice({{ $data->id }})" class="flex-1">
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
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-[2.5rem] bg-slate-50 dark:bg-slate-900 border-2 border-dashed border-slate-200 dark:border-slate-800 mb-4 text-slate-300">
                            <flux:icon.building-office-2 size="xl" />
                        </div>
                        <h3 class="text-lg font-black text-slate-400 uppercase tracking-widest">{{ __('No DS Offices Found') }}</h3>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-12 px-4">
                {{ $dsOfficeList->links() }}
            </div>
        </div>

        {{-- MODAL: ADD NEW DS OFFICE --}}
        <flux:modal wire:model="showModelNewDSOffice" name="add-new-ds-office" class="w-full max-w-md rounded-[2.5rem] p-8">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-indigo-50 dark:bg-indigo-900/30 rounded-[1.5rem] flex items-center justify-center text-indigo-600">
                        <flux:icon.plus />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black tracking-tight">{{ __('Add new DS Office') }}</flux:heading>
                        <flux:text>{{ __('Register a new administrative office unit.') }}</flux:text>
                    </div>
                </div>

                @if (session()->has('error'))
                    <div class="p-4 rounded-2xl bg-red-50 border border-red-100 text-red-800 text-xs font-bold">
                        {{ session('error') }}
                    </div>
                @endif

                <form wire:submit.prevent="addNewDSOffice" class="space-y-5">
                    @csrf
                    <flux:field>
                        <flux:input label="DS Office ID" wire:model.live="dsOfficeId" placeholder="DSO0000" mask="DSO9999" />
                    </flux:field>

                    <flux:field>
                        <flux:select label="District" wire:model.live="districtId">
                            <option value="">{{ __ ('Select district') }}</option>
                            @foreach ($districtOption as $district)
                                <option value="{{ $district->district_id }}">{{ $district->district_name }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>

                    <flux:field>
                        <flux:input label="DS Office Name" wire:model.live="dsOfficeName" placeholder="Enter full office name" />
                    </flux:field>

                    <div class="flex gap-3 pt-4">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-xl">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-xl shadow-lg shadow-indigo-500/20">{{ __('Register Office') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- MODAL: EDIT DS OFFICE --}}
        <flux:modal wire:model="showModelEditDSOffice" name="edit-d-s-Office" class="w-full max-w-md rounded-[2.5rem] p-8">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-amber-50 dark:bg-amber-900/30 rounded-[1.5rem] flex items-center justify-center text-amber-600">
                        <flux:icon.pencil-square />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black tracking-tight">{{ __('Edit DS Office') }}</flux:heading>
                        <flux:text>{{ __('Update administrative details and district links.') }}</flux:text>
                    </div>
                </div>

                <form wire:submit.prevent="updateDSOffice" class="space-y-5">
                    @csrf
                    <flux:field>
                        <flux:input disabled label="DS Office ID" wire:model.live="updateDSOfficeId" mask="DSO9999" class="bg-slate-50" />
                    </flux:field>

                    <flux:field>
                        <flux:select label="District" wire:model.live="UpdateDistrictId">
                            <option value="">{{ __ ('Select district') }}</option>
                            @foreach ($districtOption as $district)
                                <option value="{{ $district->district_id }}">{{ $district->district_name }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>

                    <flux:field>
                        <flux:input label="DS Office Name" wire:model.live="updateDSOfficeName" />
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