<section class="w-full">
    {{-- Global Breadcrumb/Header --}}
    <div class="relative mb-8 w-full px-4 lg:px-0">
        <flux:heading size="xl" level="1" class="!font-black tracking-tight uppercase text-slate-900 dark:text-white">
            {{ __('Main System Tables Overview') }}
        </flux:heading>
        <flux:subheading size="lg" class="mb-6 text-slate-500">
            {{ __('Manage global system settings and administrative hierarchies') }}
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <x-main-tables.layout>
        <div class="max-w-[1440px] mx-auto pb-12">
            
            {{-- Section Header & Action Bar --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12 px-4">
                <div class="space-y-1">
                    <flux:heading size="xl" level="1" class="!font-black tracking-tight text-indigo-600 dark:text-indigo-400 uppercase leading-none">
                        {{ __('Office Levels') }}
                    </flux:heading>
                    <div class="flex items-center gap-2">
                        <div class="h-1.5 w-8 bg-indigo-500 rounded-full"></div>
                        <flux:subheading size="lg" class="!font-bold text-slate-400 uppercase tracking-widest text-[11px]">
                            {{ __('Organizational Hierarchy & Ranking') }}
                        </flux:subheading>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <flux:modal.trigger name="add-new-office-level">
                        <flux:button icon="plus" color="primary" class="w-full sm:w-auto !rounded-2xl shadow-xl shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all hover:-translate-y-1">
                            {{ __('Add Office Level') }}
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
                @forelse ($office_levels as $key => $data)
                    <div class="group relative flex flex-col bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[3rem] p-8 hover:shadow-[0_40px_80px_-15px_rgba(0,0,0,0.1)] dark:hover:shadow-[0_40px_80px_-15px_rgba(0,0,0,0.5)] hover:border-indigo-500/50 transition-all duration-500">
                        
                        {{-- Card Header: ID & Status --}}
                        <div class="flex justify-between items-start mb-8">
                            <span class="text-[10px] font-black uppercase tracking-widest px-4 py-1 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 rounded-full border border-slate-200 dark:border-slate-700">
                                {{ $data->office_level_id }}
                            </span>
                            
                            <div class="flex items-center gap-2">
                                <div class="h-2 w-2 rounded-full {{ $data->active_status ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.6)]' : 'bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.6)]' }}"></div>
                                <span class="text-[10px] font-black uppercase tracking-tighter {{ $data->active_status ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $data->active_status ? 'Active' : 'Disabled' }}
                                </span>
                            </div>
                        </div>

                        {{-- Main Info Section --}}
                        <div class="mb-8">
                            <h3 class="text-xl font-black text-slate-900 dark:text-white leading-tight uppercase tracking-tight group-hover:text-indigo-600 transition-colors">
                                {{ $data->office_level_name }}
                            </h3>
                            <div class="flex items-center gap-2 mt-2">
                                <flux:icon.adjustments-horizontal variant="micro" class="text-slate-400" />
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Hierarchy Rank: {{ $data->office_level_rank }}</span>
                            </div>
                        </div>

                        {{-- Action Area --}}
                        <div class="mt-auto pt-6 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                             <div class="flex items-center gap-2 text-slate-300 font-bold text-xs uppercase tracking-widest">
                                <flux:icon.hashtag variant="micro" />
                                <span>{{ $office_levels->firstItem() + $key }}</span>
                             </div>
                             
                             <div class="flex items-center gap-2">
                                <flux:modal.trigger wire:click="editOfficeLevel({{ $data->id }})" name="edit-office-level">
                                    <flux:button size="sm" variant="ghost" icon="pencil-square" class="!rounded-xl hover:!bg-indigo-600 hover:!text-white transition-all shadow-sm" />
                                </flux:modal.trigger>
                                
                                <flux:button 
                                    wire:click="toggleStatus({{ $data->id }})"
                                    wire:confirm="Confirm status change for: {{ $data->office_level_name }}?"
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
                            <flux:icon.home-modern size="xl" class="text-slate-300" />
                        </div>
                        <p class="text-slate-400 font-black uppercase tracking-[0.3em] text-sm">{{ __('No Office Levels Found') }}</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-16 px-4">
                {{ $office_levels->links() }}
            </div>
        </div>

        {{-- MODAL: ADD NEW --}}
        <flux:modal wire:model="showModelNewOfficeLevel" name="add-new-office-level" class="w-full max-w-lg rounded-[3rem] p-12">
            <div class="space-y-8">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 bg-indigo-600 rounded-[1.5rem] flex items-center justify-center text-white shadow-2xl shadow-indigo-500/40">
                        <flux:icon.plus size="lg" />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black uppercase tracking-tighter leading-none">{{ __('New Level') }}</flux:heading>
                        <flux:text size="sm" class="mt-1">{{ __('Add a new tier to the office hierarchy.') }}</flux:text>
                    </div>
                </div>

                <form wire:submit.prevent="addNewOfficeLevel" class="space-y-5">
                    @csrf
                    <flux:field>
                        <flux:input label="Office Level ID" wire:model.live="office_level_id" mask="OLID999" placeholder="OLID001" class="!rounded-[1.25rem] !py-3" />
                    </flux:field>

                    <flux:field>
                        <flux:input label="Office Level Name" wire:model.live="office_level_name" placeholder="e.g. Regional Office" class="!rounded-[1.25rem] !py-3" />
                    </flux:field>

                    <flux:field>
                        <flux:input label="Hierarchy Rank" type="number" wire:model.live="office_level_rank" placeholder="e.g. 1" class="!rounded-[1.25rem] !py-3" />
                    </flux:field>

                    <div class="flex gap-4 pt-6">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-[1.25rem] !py-3">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-[1.25rem] !py-3 shadow-xl shadow-indigo-500/30">{{ __('Add Level') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- MODAL: EDIT --}}
        <flux:modal wire:model="showModelEditOfficeLevel" name="edit-office-level" class="w-full max-w-lg rounded-[3rem] p-12">
            <div class="space-y-8">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 bg-slate-900 dark:bg-white rounded-[1.5rem] flex items-center justify-center text-white dark:text-slate-900 shadow-2xl">
                        <flux:icon.pencil-square size="lg" />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black uppercase tracking-tighter leading-none">{{ __('Edit Level') }}</flux:heading>
                        <flux:text size="sm" class="mt-1">{{ __('Update hierarchy name or ranking order.') }}</flux:text>
                    </div>
                </div>

                <form wire:submit.prevent="updateOfficeLevel" class="space-y-5">
                    @csrf
                    <flux:field>
                        <flux:input disabled label="Office Level ID" wire:model.live="update_office_level_id" mask="OLID999" class="!rounded-[1.25rem] !py-3 bg-slate-50 opacity-50" />
                    </flux:field>

                    <flux:field>
                        <flux:input label="Office Level Name" wire:model.live="update_office_level_name" class="!rounded-[1.25rem] !py-3" />
                    </flux:field>

                    <flux:field>
                        <flux:input label="Hierarchy Rank" type="number" wire:model.live="update_office_level_rank" class="!rounded-[1.25rem] !py-3" />
                    </flux:field>

                    <div class="flex gap-4 pt-6">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-[1.25rem] !py-3">{{ __('Discard') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-[1.25rem] !py-3 shadow-xl shadow-indigo-500/30">{{ __('Save Changes') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>
    </x-main-tables.layout>
</section>