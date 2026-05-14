<section class="w-full">
    {{-- Global Breadcrumb/Header --}}
    <div class="relative mb-8 w-full px-4 lg:px-0">
        <flux:heading size="xl" level="1" class="!font-black tracking-tight uppercase text-slate-900 dark:text-white">
            {{ __('Main System Tables Overview') }}
        </flux:heading>
        <flux:subheading size="lg" class="mb-6 text-slate-500">
            {{ __('Manage organizational hierarchy and rank structures') }}
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <x-main-tables.layout>
        <div class="max-w-[1440px] mx-auto pb-12">
            
            {{-- Section Header & Action Bar --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12 px-4">
                <div class="space-y-1">
                    <flux:heading size="xl" level="1" class="!font-black tracking-tight text-indigo-600 dark:text-indigo-400 uppercase leading-none">
                        {{ __('Positions') }}
                    </flux:heading>
                    <div class="flex items-center gap-2">
                        <div class="h-1.5 w-8 bg-indigo-500 rounded-full"></div>
                        <flux:subheading size="lg" class="!font-bold text-slate-400 uppercase tracking-widest text-[11px]">
                            {{ __('Designations & Roles Management') }}
                        </flux:subheading>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <flux:modal.trigger name="add-new-position">
                        <flux:button icon="plus" color="primary" class="w-full sm:w-auto !rounded-2xl shadow-xl shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all hover:-translate-y-1">
                            {{ __('Add Position') }}
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
                @forelse ($positionList as $key => $data)
                    <div class="group relative flex flex-col bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[3rem] p-8 hover:shadow-[0_40px_80px_-15px_rgba(0,0,0,0.1)] dark:hover:shadow-[0_40px_80px_-15px_rgba(0,0,0,0.5)] hover:border-indigo-500/50 transition-all duration-500">
                        
                        {{-- Card Header: Position ID & Status --}}
                        <div class="flex justify-between items-start mb-6">
                            <span class="text-[10px] font-black uppercase tracking-widest px-4 py-1 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 rounded-full border border-slate-200 dark:border-slate-700">
                                {{ $data->position_id }}
                            </span>
                            
                            <div class="flex items-center gap-2">
                                <div class="h-2 w-2 rounded-full {{ $data->active_status ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.6)]' : 'bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.6)]' }}"></div>
                                <span class="text-[10px] font-black uppercase tracking-tighter {{ $data->active_status ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $data->active_status ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>

                        {{-- Position Name --}}
                        <div class="mb-4">
                            <h3 class="text-xl font-black text-slate-900 dark:text-white leading-tight uppercase tracking-tight group-hover:text-indigo-600 transition-colors">
                                {{ $data->position_name }}
                            </h3>
                        </div>

                        {{-- Service Parent Info --}}
                        <div class="mb-6 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700/50">
                            <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">{{ __('Linked Service') }}</div>
                            <div class="flex items-center gap-2">
                                <flux:icon.briefcase variant="micro" class="text-indigo-500" />
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                    {{ $data->service->service_name }} 
                                    <span class="text-[10px] text-slate-400 font-medium">({{ $data->service_id }})</span>
                                </span>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="mb-8">
                            <div class="flex items-start gap-2 text-slate-500 dark:text-slate-400">
                                <flux:icon.information-circle variant="micro" class="shrink-0 mt-0.5 opacity-50" />
                                <p class="text-xs font-medium leading-relaxed italic">
                                    {{ $data->description ?? 'No description provided for this role.' }}
                                </p>
                            </div>
                        </div>

                        {{-- Card Actions --}}
                        <div class="mt-auto pt-6 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                             <div class="flex items-center gap-2 text-slate-300 font-bold text-xs uppercase tracking-widest">
                                <flux:icon.hashtag variant="micro" />
                                <span>{{ $positionList->firstItem() + $key }}</span>
                             </div>
                             
                             <div class="flex items-center gap-2">
                                <flux:modal.trigger wire:click="editPosition({{ $data->id }})" name="edit-position">
                                    <flux:button size="sm" variant="ghost" icon="pencil-square" class="!rounded-xl hover:!bg-indigo-600 hover:!text-white transition-all shadow-sm" />
                                </flux:modal.trigger>
                                
                                <flux:button 
                                    wire:click="toggleStatus({{ $data->id }})"
                                    wire:confirm="Confirm status change for: {{ $data->position_name }}?"
                                    size="sm" 
                                    variant="ghost" 
                                    icon="{{ $data->active_status ? 'no-symbol' : 'check' }}"
                                    class="!rounded-xl {{ $data->active_status ? 'hover:!bg-rose-500 hover:!text-white' : 'hover:!bg-emerald-500 hover:!text-white' }} transition-all shadow-sm" 
                                />
                             </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-32 flex flex-col items-center justify-center bg-slate-50 dark:bg-slate-900/40 rounded-[4rem] border-2 border-dashed border-slate-200 dark:border-slate-800">
                        <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-6">
                            <flux:icon.identification size="xl" class="text-slate-300" />
                        </div>
                        <p class="text-slate-400 font-black uppercase tracking-[0.3em] text-sm">{{ __('No Positions Defined') }}</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-16 px-4">
                {{ $positionList->links() }}
            </div>
        </div>

        {{-- MODAL: ADD NEW POSITION --}}
        <flux:modal wire:model="showModelNewPosition" name="add-new-position" class="w-full max-w-lg rounded-[3rem] p-12">
            <div class="space-y-8">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 bg-indigo-600 rounded-[1.5rem] flex items-center justify-center text-white shadow-2xl shadow-indigo-500/40">
                        <flux:icon.plus size="lg" />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black uppercase tracking-tighter leading-none">{{ __('New Position') }}</flux:heading>
                        <flux:text size="sm" class="mt-1">{{ __('Define a new role within the department.') }}</flux:text>
                    </div>
                </div>

                @if (session()->has('error'))
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-100 text-rose-800 text-xs font-bold uppercase tracking-tight">
                        {{ session('error') }}
                    </div>
                @endif

                <form wire:submit.prevent="addNewPosition" class="space-y-5">
                    @csrf
                    <flux:field>
                        <flux:input label="Position ID" wire:model.live="positionId" mask="POS999" placeholder="POS001" class="!rounded-[1.25rem] !py-3" />
                    </flux:field>

                    <flux:field>
                        <flux:select label="Service Category" id="service" wire:model.live="serviceId" class="!rounded-[1.25rem]">
                            <option value="" disabled selected>{{ __ ('Select Parent Service') }}</option>
                            @foreach ($serviceOption as $service)
                                <option value="{{ $service->service_id }}">{{ $service->service_name }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>

                    <flux:field>
                        <flux:input label="Position Name" wire:model.live="positionName" placeholder="e.g. Chief Inspector" class="!rounded-[1.25rem] !py-3" />
                    </flux:field>

                    <flux:field>
                        <flux:textarea label="Role Description" wire:model.live="updatePositionDescription" placeholder="Outline the primary duties of this rank..." class="!rounded-[1.25rem]" rows="3" />
                    </flux:field>

                    <div class="flex gap-4 pt-6">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-[1.25rem] !py-3">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-[1.25rem] !py-3 shadow-xl shadow-indigo-500/30">{{ __('Save Position') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- MODAL: EDIT POSITION --}}
        <flux:modal wire:model="showModelEditPosition" name="edit-position" class="w-full max-w-lg rounded-[3rem] p-12">
            <div class="space-y-8">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 bg-slate-900 dark:bg-white rounded-[1.5rem] flex items-center justify-center text-white dark:text-slate-900 shadow-2xl">
                        <flux:icon.pencil-square size="lg" />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black uppercase tracking-tighter leading-none">{{ __('Edit Position') }}</flux:heading>
                        <flux:text size="sm" class="mt-1">{{ __('Modify rank details or linked service.') }}</flux:text>
                    </div>
                </div>

                <form wire:submit.prevent="updatePosition" class="space-y-5">
                    @csrf
                    <flux:field>
                        <flux:input disabled label="Position ID" wire:model.live="updatePositionId" mask="POS999" class="!rounded-[1.25rem] !py-3 bg-slate-50 opacity-50" />
                    </flux:field>

                    <flux:field>
                        <flux:select label="Service Category" id="service" wire:model.live="updateServiceId" class="!rounded-[1.25rem]">
                            @foreach ($serviceOption as $service)
                                <option value="{{ $service->service_id }}">{{ $service->service_name }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>

                    <flux:field>
                        <flux:input label="Position Name" wire:model.live="updatePositionName" class="!rounded-[1.25rem] !py-3" />
                    </flux:field>

                    <flux:field>
                        <flux:textarea label="Role Description" wire:model.live="updatePositionDescription" class="!rounded-[1.25rem]" rows="3" />
                    </flux:field>

                    <div class="flex gap-4 pt-6">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-[1.25rem] !py-3">{{ __('Discard') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-[1.25rem] !py-3 shadow-xl shadow-indigo-500/30">{{ __('Update Position') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>
    </x-main-tables.layout>
</section>