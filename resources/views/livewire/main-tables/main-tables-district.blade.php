<section class="w-full">
    <x-main-tables.layout>
        <div class="max-w-[1400px] mx-auto pb-12 px-4 lg:px-0">
            
            {{-- Section Specific Header --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
                <div class="space-y-1">
                    <flux:heading size="xl" level="1" class="!font-black tracking-tight text-slate-900 dark:text-white">
                        {{ __('Districts') }}
                    </flux:heading>
                    <flux:subheading size="lg" class="flex items-center gap-2">
                        <flux:icon.map variant="micro" class="text-indigo-500" />
                        {{ __('Manage regional districts, display order, and provincial hierarchy') }}
                    </flux:subheading>
                </div>

                <div class="flex items-center gap-3">
                    <flux:modal.trigger name="add-new-district">
                        <flux:button icon="plus" color="primary" class="w-full md:w-auto !rounded-2xl shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all">
                            {{ __('Add new District') }}
                        </flux:button>
                    </flux:modal.trigger>
                </div>
            </div>

            {{-- Flash Messages --}}
            @if (session()->has('message'))
                <div class="mb-6 animate-in fade-in slide-in-from-top-4 duration-500">
                    <div class="flex items-center gap-3 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400">
                        <flux:icon.check-circle variant="micro" class="shrink-0" />
                        <span class="text-sm font-bold">{{ session('message') }}</span>
                    </div>
                </div>
            @endif

            {{-- DISTRICT CARDS GRID --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
                @forelse ($districtList as $key => $data)
                    <div class="relative group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2.5rem] p-6 shadow-sm hover:shadow-xl hover:border-indigo-300 dark:hover:border-indigo-800 transition-all duration-300">
                        
                        {{-- Top Metadata Row --}}
                        <div class="flex justify-between items-center mb-5">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-black text-slate-400 tabular-nums tracking-widest uppercase">
                                    #{{ $districtList->firstItem() + $key }}
                                </span>
                                <div class="px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-[10px] font-bold text-slate-500">
                                    Order: {{ $data->district_code }}
                                </div>
                            </div>
                            <flux:badge size="sm" variant="pill" color="{{ $data->active_status ? 'green' : 'red' }}" class="!font-black uppercase tracking-widest text-[9px]">
                                {{ $data->active_status ? 'Active' : 'Inactive' }}
                            </flux:badge>
                        </div>

                        {{-- District Main Info --}}
                        <div class="mb-5">
                            <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-900/30 text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-tight mb-2">
                                <flux:icon.finger-print variant="micro" class="size-3" />
                                {{ $data->district_id }}
                            </div>
                            <h3 class="text-xl font-black text-slate-900 dark:text-white leading-tight min-h-[3rem] group-hover:text-indigo-600 transition-colors">
                                {{ $data->district_name }}
                            </h3>
                        </div>

                        {{-- Province Sub-Card --}}
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-[1.5rem] border border-slate-100 dark:border-slate-800 mb-6">
                            <p class="text-[9px] uppercase font-black text-slate-400 leading-none mb-2 tracking-widest">{{ __('Parent Province') }}</p>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-white dark:bg-slate-800 shadow-sm flex items-center justify-center text-indigo-500">
                                    <flux:icon.globe-asia-australia variant="micro" />
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ $data->province->province_name }}</p>
                                    <p class="text-[10px] font-medium text-slate-500 font-mono">ID: {{ $data->province_id }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex gap-2">
                            <flux:modal.trigger wire:click="editDistrict({{ $data->id }})" class="flex-1">
                                <flux:button size="sm" variant="ghost" icon="pencil-square" class="w-full !rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800">
                                    {{ __('Edit') }}
                                </flux:button>
                            </flux:modal.trigger>
                            
                            <flux:button 
                                wire:click="toggleStatus({{ $data->id }})"
                                wire:confirm="Are you sure you want to change this district status?"
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
                            <flux:icon.map size="xl" />
                        </div>
                        <h3 class="text-lg font-black text-slate-400 uppercase tracking-widest">{{ __('No Districts Registered') }}</h3>
                    </div>
                @endforelse
            </div>

            {{-- Pagination Footer --}}
            <div class="mt-12 px-4">
                {{ $districtList->links() }}
            </div>
        </div>

        {{-- MODAL: ADD NEW DISTRICT --}}
        <flux:modal wire:model="showModelNewDistrict" name="add-new-district" class="w-full max-w-md rounded-[2.5rem] p-8">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-indigo-50 dark:bg-indigo-900/30 rounded-[1.5rem] flex items-center justify-center text-indigo-600">
                        <flux:icon.plus />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black tracking-tight">{{ __('Add new District') }}</flux:heading>
                        <flux:text>{{ __('Link a new district to a parent province.') }}</flux:text>
                    </div>
                </div>

                @if (session()->has('error'))
                    <div class="p-4 rounded-2xl bg-red-50 border border-red-100 text-red-800 text-xs font-bold">
                        {{ session('error') }}
                    </div>
                @endif

                <form wire:submit.prevent="addNewDistrict" class="space-y-5">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:input label="District ID" wire:model.live="districtId" mask="DIS999" placeholder="DIS000" />
                        </flux:field>
                        <flux:field>
                            <flux:input label="Display Order" wire:model.live="districtOrder" placeholder="0" />
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:select label="Province" wire:model.live="provinceId">
                            <option value="">{{ __ ('Select Province') }}</option>
                            @foreach ($provinceOption as $province)
                                <option value="{{ $province->province_id }}">{{ $province->province_name }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>

                    <flux:field>
                        <flux:input label="District Name" wire:model.live="districtName" placeholder="Enter District Name" />
                    </flux:field>

                    <div class="flex gap-3 pt-4">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-xl">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-xl shadow-lg shadow-indigo-500/20">{{ __('Create District') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- MODAL: EDIT DISTRICT --}}
        <flux:modal wire:model="showModelEditDistrict" name="edit-district" class="w-full max-w-md rounded-[2.5rem] p-8">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-amber-50 dark:bg-amber-900/30 rounded-[1.5rem] flex items-center justify-center text-amber-600">
                        <flux:icon.pencil-square />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black tracking-tight">{{ __('Edit District') }}</flux:heading>
                        <flux:text>{{ __('Modify district metadata or display sorting.') }}</flux:text>
                    </div>
                </div>

                <form wire:submit.prevent="updateDistrict" class="space-y-5">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:input disabled label="District ID" wire:model.live="updateDistrictId" mask="DIS999" class="bg-slate-50" />
                        </flux:field>
                        <flux:field>
                            <flux:input label="Display Order" wire:model.live="updateDistrictOrder" />
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:select label="Province" wire:model.live="updateProvinceId">
                            <option value="">{{ __ ('Select Province') }}</option>
                            @foreach ($provinceOption as $province)
                                <option value="{{ $province->province_id }}">{{ $province->province_name }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>

                    <flux:field>
                        <flux:input label="District Name" wire:model.live="updateDistrictName" />
                    </flux:field>

                    <div class="flex gap-3 pt-4">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-xl">{{ __('Discard') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-xl shadow-lg shadow-indigo-500/20">{{ __('Update District') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

    </x-main-tables.layout>
</section>