<section class="w-full">
    {{-- Global Breadcrumb/Header --}}
    <div class="relative mb-8 w-full px-4 lg:px-0">
        <flux:heading size="xl" level="1" class="!font-black tracking-tight uppercase text-slate-900 dark:text-white">
            {{ __('Main System Tables Overview') }}
        </flux:heading>
        <flux:subheading size="lg" class="mb-6 text-slate-500">
            {{ __('Manage global system settings and service locations') }}
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <x-main-tables.layout>
        <div class="max-w-[1440px] mx-auto pb-12">
            
            {{-- Section Header & Action Bar --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12 px-4">
                <div class="space-y-1">
                    <flux:heading size="xl" level="1" class="!font-black tracking-tight text-indigo-600 dark:text-indigo-400 uppercase leading-none">
                        {{ __('Police Stations') }}
                    </flux:heading>
                    <div class="flex items-center gap-2">
                        <div class="h-1.5 w-8 bg-indigo-500 rounded-full"></div>
                        <flux:subheading size="lg" class="!font-bold text-slate-500 uppercase tracking-widest text-[11px]">
                            {{ __('Law Enforcement Units & Contact Directory') }}
                        </flux:subheading>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <flux:modal.trigger name="add-new-police-station">
                        <flux:button icon="plus" color="primary" class="w-full sm:w-auto !rounded-2xl shadow-xl shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all hover:-translate-y-1">
                            {{ __('Add Police Station') }}
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

            {{-- CARD GRID --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-8 px-4">
                @forelse ($police_stations as $key => $data)
                    <div class="group relative flex flex-col bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-[3rem] p-8 hover:shadow-[0_40px_80px_-15px_rgba(0,0,0,0.1)] dark:hover:shadow-[0_40px_80px_-15px_rgba(0,0,0,0.5)] hover:border-indigo-500/50 transition-all duration-500">
                        
                        {{-- Card Header --}}
                        <div class="flex justify-between items-start mb-6">
                            <span class="text-[10px] font-black uppercase tracking-widest px-4 py-1 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-500 rounded-full border border-slate-300 dark:border-slate-700">
                                {{ $data->police_station_id }}
                            </span>
                            
                            <div class="flex items-center gap-2">
                                <div class="h-2 w-2 rounded-full {{ $data->active_status ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.6)]' : 'bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.6)]' }}"></div>
                                <span class="text-[10px] font-black uppercase tracking-tighter {{ $data->active_status ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $data->active_status ? 'Active' : 'Disabled' }}
                                </span>
                            </div>
                        </div>

                        {{-- Main Name & Location --}}
                        <div class="mb-6">
                            <h3 class="text-xl font-black text-slate-900 dark:text-white leading-tight uppercase tracking-tight group-hover:text-indigo-600 transition-colors">
                                {{ $data->police_station_name }}
                            </h3>
                            <div class="flex items-start gap-2 mt-3 text-slate-500 dark:text-slate-500">
                                <flux:icon.map-pin variant="micro" class="shrink-0 mt-0.5" />
                                <p class="text-xs font-medium leading-relaxed">
                                    {{ $data->address }}<br>
                                    <span class="font-black text-indigo-500/70">{{ $data->postal_code }}</span>
                                </p>
                            </div>
                        </div>

                        {{-- Contact Stack --}}
                        <div class="space-y-2 mb-8">
                            <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50">
                                <flux:icon.phone variant="micro" class="text-indigo-500" />
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $data->phone }}</span>
                            </div>
                            <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50">
                                <flux:icon.envelope variant="micro" class="text-indigo-500" />
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300 truncate">{{ $data->email }}</span>
                            </div>
                        </div>

                        {{-- Card Actions --}}
                        <div class="mt-auto pt-6 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between">
                             <div class="flex items-center gap-2 text-slate-300 font-bold text-xs uppercase tracking-widest">
                                <flux:icon.hashtag variant="micro" />
                                <span>{{ $police_stations->firstItem() + $key }}</span>
                             </div>
                             
                             <div class="flex items-center gap-2">
                                <flux:modal.trigger wire:click="editPoliceStation({{ $data->id }})" name="edit-police-station">
                                    <flux:button size="sm" variant="ghost" icon="pencil-square" class="!rounded-xl hover:!bg-indigo-600 hover:!text-white transition-all shadow-sm" />
                                </flux:modal.trigger>
                                
                                <flux:button 
                                    wire:click="toggleStatus({{ $data->id }})"
                                    wire:confirm="Confirm status change for: {{ $data->police_station_name }}?"
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
                            <flux:icon.shield-check size="xl" class="text-slate-300" />
                        </div>
                        <p class="text-slate-500 font-black uppercase tracking-[0.3em] text-sm">{{ __('No Stations Found') }}</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-16 px-4">
                {{ $police_stations->links() }}
            </div>
        </div>

        {{-- MODAL: ADD --}}
        <flux:modal wire:model="showModelNewPoliceStation" name="add-new-police-station" class="w-full max-w-lg rounded-[3rem] p-12">
            <div class="space-y-8">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 bg-indigo-600 rounded-[1.5rem] flex items-center justify-center text-white shadow-2xl shadow-indigo-500/40">
                        <flux:icon.plus size="lg" />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black uppercase tracking-tighter leading-none">{{ __('New Station') }}</flux:heading>
                        <flux:text size="sm" class="mt-1">{{ __('Register a new law enforcement location.') }}</flux:text>
                    </div>
                </div>

                <form wire:submit.prevent="addNewPoliceStation" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <flux:field class="col-span-1">
                            <flux:input label="Station ID" wire:model.live="police_station_id" mask="PSID999" placeholder="PSID001" class="!rounded-[1.25rem] !py-3" />
                        </flux:field>
                        <flux:field class="col-span-1">
                            <flux:input label="Postal Code" wire:model.live="postal_code" placeholder="e.g. 10100" class="!rounded-[1.25rem] !py-3" />
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:input label="Station Name" wire:model.live="police_station_name" placeholder="e.g. Colombo Central" class="!rounded-[1.25rem] !py-3" />
                    </flux:field>

                    <flux:field>
                        <flux:textarea label="Full Address" wire:model.live="address" placeholder="Enter street and city details..." class="!rounded-[1.25rem]" rows="3" />
                    </flux:field>

                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:input label="Phone Number" wire:model.live="phone" placeholder="+94..." class="!rounded-[1.25rem] !py-3" />
                        </flux:field>
                        <flux:field>
                            <flux:input label="Email Address" wire:model.live="email" placeholder="station@police.lk" class="!rounded-[1.25rem] !py-3" />
                        </flux:field>
                    </div>

                    <div class="flex gap-4 pt-6">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-[1.25rem] !py-3">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-[1.25rem] !py-3 shadow-xl shadow-indigo-500/30">{{ __('Save Station') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- MODAL: EDIT --}}
        <flux:modal wire:model="showModelEditPoliceStation" name="edit-police-station" class="w-full max-w-lg rounded-[3rem] p-12">
            <div class="space-y-8">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 bg-indigo-600 dark:bg-white rounded-[1.5rem] flex items-center justify-center text-white dark:text-slate-900 shadow-2xl">
                        <flux:icon.pencil-square size="lg" />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black uppercase tracking-tighter leading-none">{{ __('Edit Station') }}</flux:heading>
                        <flux:text size="sm" class="mt-1">{{ __('Update contact or location information.') }}</flux:text>
                    </div>
                </div>

                <form wire:submit.prevent="updatePoliceStation" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <flux:field class="col-span-1">
                            <flux:input disabled label="Station ID" wire:model.live="update_police_station_id" mask="PSID999" class="!rounded-[1.25rem] !py-3 bg-slate-50 opacity-50" />
                        </flux:field>
                        <flux:field class="col-span-1">
                            <flux:input label="Postal Code" wire:model.live="update_postal_code" class="!rounded-[1.25rem] !py-3" />
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:input label="Station Name" wire:model.live="update_police_station_name" class="!rounded-[1.25rem] !py-3" />
                    </flux:field>

                    <flux:field>
                        <flux:textarea label="Full Address" wire:model.live="update_address" class="!rounded-[1.25rem]" rows="3" />
                    </flux:field>

                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:input label="Phone Number" wire:model.live="update_phone" class="!rounded-[1.25rem] !py-3" />
                        </flux:field>
                        <flux:field>
                            <flux:input label="Email Address" wire:model.live="update_email" class="!rounded-[1.25rem] !py-3" />
                        </flux:field>
                    </div>

                    <div class="flex gap-4 pt-6">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-[1.25rem] !py-3">{{ __('Discard') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-[1.25rem] !py-3 shadow-xl shadow-indigo-500/30">{{ __('Update Station') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>
    </x-main-tables.layout>
</section>