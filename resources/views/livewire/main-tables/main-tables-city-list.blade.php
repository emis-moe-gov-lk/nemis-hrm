<section class="w-full">
    
    <x-main-tables.layout>
        <div class="max-w-[1400px] mx-auto pb-12 px-4 lg:px-0">
            
            {{-- Section Header & Action Bar --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
                <div class="space-y-1">
                    <flux:heading size="xl" level="1" class="!font-black tracking-tight text-slate-900 dark:text-white">
                        {{ __('City List') }}
                    </flux:heading>
                    <flux:subheading size="lg" class="flex items-center gap-2">
                        <flux:icon.map-pin variant="micro" class="text-indigo-500" />
                        {{ __('Configure regional boundaries and GPS coordinates') }}
                    </flux:subheading>
                </div>

                <div class="flex items-center gap-3">
                    <flux:modal.trigger name="add-new-city">
                        <flux:button icon="plus" color="primary" class="w-full md:w-auto !rounded-2xl shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all">
                            {{ __('Add New City') }}
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
                @forelse ($cities as $key => $data)
                    <div class="relative group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2.5rem] p-6 shadow-sm hover:shadow-xl hover:border-indigo-300 dark:hover:border-indigo-800 transition-all duration-300">
                        
                        {{-- Badge & Index --}}
                        <div class="flex justify-between items-center mb-5">
                            <span class="text-[10px] font-black text-slate-400 tabular-nums tracking-widest uppercase">
                                #{{ $cities->firstItem() + $key }}
                            </span>
                            <flux:badge size="sm" variant="pill" color="{{ $data->active_status ? 'green' : 'red' }}" class="!font-black uppercase tracking-widest text-[9px]">
                                {{ $data->active_status ? 'Active' : 'Inactive' }}
                            </flux:badge>
                        </div>

                        {{-- Identity Section --}}
                        <div class="mb-5">
                            <h3 class="text-lg font-black text-slate-900 dark:text-white leading-tight group-hover:text-indigo-600 transition-colors">
                                {{ $data->city_name_en }}
                            </h3>
                            <div class="inline-block mt-1 px-2 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-900/30 text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-tighter">
                                {{ $data->city_id }}
                            </div>
                        </div>

                        {{-- Metadata Section --}}
                        <div class="space-y-4 mb-6">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-2xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 group-hover:text-indigo-500 transition-colors">
                                    <flux:icon.map variant="micro" />
                                </div>
                                <div>
                                    <p class="text-[9px] uppercase font-black text-slate-400 leading-none mb-1">{{ __('District') }}</p>
                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ $data->district->district_name ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-2xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 group-hover:text-indigo-500 transition-colors">
                                    <flux:icon.envelope variant="micro" />
                                </div>
                                <div>
                                    <p class="text-[9px] uppercase font-black text-slate-400 leading-none mb-1">{{ __('Postal Code') }}</p>
                                    <p class="text-sm font-bold font-mono text-slate-700 dark:text-slate-300">{{ $data->postcode }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- GPS Chip --}}
                        <div class="flex items-center gap-2 p-3 bg-slate-50/50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 rounded-2xl mb-6">
                            <div class="flex-1 text-center">
                                <p class="text-[8px] uppercase font-black text-slate-400">Lat</p>
                                <p class="text-[11px] font-bold tabular-nums text-slate-600 dark:text-slate-400">{{ $data->latitude ?? '—' }}</p>
                            </div>
                            <div class="w-px h-4 bg-slate-200 dark:bg-slate-700"></div>
                            <div class="flex-1 text-center">
                                <p class="text-[8px] uppercase font-black text-slate-400">Lon</p>
                                <p class="text-[11px] font-bold tabular-nums text-slate-600 dark:text-slate-400">{{ $data->longitude ?? '—' }}</p>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex gap-2">
                            <flux:modal.trigger wire:click="editCityList({{ $data->id }})" class="flex-1">
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
                            <flux:icon.map-pin size="xl" />
                        </div>
                        <h3 class="text-lg font-black text-slate-400">{{ __('No Cities Found') }}</h3>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-12">
                {{ $cities->links() }}
            </div>
        </div>

        {{-- MODAL: ADD NEW CITY --}}
        <flux:modal wire:model="showModelNewCity" name="add-new-city" class="w-full max-w-xl rounded-[2.5rem] p-8">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-indigo-50 dark:bg-indigo-900/30 rounded-[1.5rem] flex items-center justify-center text-indigo-600">
                        <flux:icon.plus />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black tracking-tight">{{ __('Add New City') }}</flux:heading>
                        <flux:text>{{ __('Register a new geographic location in the system.') }}</flux:text>
                    </div>
                </div>

                @if (session()->has('error'))
                    <div class="p-4 rounded-2xl bg-red-50 border border-red-100 text-red-800 text-xs font-bold">
                        {{ session('error') }}
                    </div>
                @endif

                <form wire:submit.prevent="addNewCity" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:input label="City ID" wire:model.live="cityId" mask="CTY999999" placeholder="CTY000000" />
                        </flux:field>
                        <flux:field>
                            <flux:select label="District" wire:model.live="district">
                                <option value="">{{ __ ('Select District') }}</option>
                                @foreach ($districtOption as $district)
                                    <option value="{{ $district->district_id }}">{{ $district->district_name }}</option>
                                @endforeach
                            </flux:select>
                        </flux:field>
                    </div>

                    <div class="p-6 bg-slate-50 dark:bg-slate-800/50 rounded-[2rem] border border-slate-100 dark:border-slate-800 space-y-4">
                        <flux:heading size="sm" class="!font-black uppercase tracking-widest text-slate-400 text-[10px]">{{ __('Localized Names') }}</flux:heading>
                        <flux:field><flux:input label="English" wire:model.live="cityNameEn" /></flux:field>
                        <div class="grid grid-cols-2 gap-4">
                            <flux:field><flux:input label="Sinhala" wire:model.live="cityNameSi" /></flux:field>
                            <flux:field><flux:input label="Tamil" wire:model.live="cityNameTa" /></flux:field>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <flux:field><flux:input label="Postal Code" wire:model.live="postalCode" mask="99999" /></flux:field>
                        <flux:field><flux:input label="Latitude" wire:model.live="latitude" step="0.000001" /></flux:field>
                        <flux:field><flux:input label="Longitude" wire:model.live="longitude" step="0.000001" /></flux:field>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-xl">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-xl shadow-lg shadow-indigo-500/20">{{ __('Add New City') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- MODAL: EDIT CITY --}}
        <flux:modal wire:model="showModelEditCityList" name="edit-blood-group" class="w-full max-w-xl rounded-[2.5rem] p-8">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-indigo-50 dark:bg-indigo-900/30 rounded-[1.5rem] flex items-center justify-center text-indigo-600">
                        <flux:icon.pencil-square />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black tracking-tight">{{ __('Edit City') }}</flux:heading>
                        <flux:text>{{ __('Modify city details and GPS coordinates.') }}</flux:text>
                    </div>
                </div>

                <form wire:submit.prevent="updateCity" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:input disabled label="City ID" wire:model.live="updateCityId" mask="CTY999999" />
                        </flux:field>
                        <flux:field>
                            <flux:select label="District" wire:model.live="updateDistrict">
                                <option value="">{{ __ ('Select District') }}</option>
                                @foreach ($districtOption as $district)
                                    <option value="{{ $district->district_id }}">{{ $district->district_name }}</option>
                                @endforeach
                            </flux:select>
                        </flux:field>
                    </div>

                    <div class="space-y-4">
                        <flux:field><flux:input label="City Name [English]" wire:model.live="updateCityNameEn" /></flux:field>
                        <div class="grid grid-cols-2 gap-4">
                            <flux:field><flux:input label="City Name [Sinhala]" wire:model.live="updateCityNameSi" /></flux:field>
                            <flux:field><flux:input label="City Name [Tamil]" wire:model.live="updateCityNameTa" /></flux:field>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4 p-5 bg-slate-50 dark:bg-slate-800/50 rounded-3xl border border-slate-100 dark:border-slate-800">
                        <flux:field><flux:input label="Postal Code" wire:model.live="updatePostalCode" mask="99999" /></flux:field>
                        <flux:field><flux:input label="Lat" wire:model.live="updateLatitude" /></flux:field>
                        <flux:field><flux:input label="Lon" wire:model.live="updateLongitude" /></flux:field>
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