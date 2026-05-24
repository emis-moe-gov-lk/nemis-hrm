<section class="w-full">
    
    <x-main-tables.layout>
        <div class="max-w-[1400px] mx-auto pb-12 px-4 lg:px-0">
            
            {{-- Section Header & Action Bar --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
                <div class="space-y-1">
                    <flux:heading size="xl" level="1" class="!font-black tracking-tight text-indigo-600 dark:text-indigo-400 uppercase">
                        {{ __('Institutional Facilities') }}
                    </flux:heading>
                    <flux:subheading size="lg" class="flex items-center gap-2">
                        <flux:icon.home-modern variant="micro" class="text-slate-500" />
                        {{ __('Manage and categorize available resources and facilities') }}
                    </flux:subheading>
                </div>

                <div class="flex items-center gap-3">
                    <flux:modal.trigger name="add-new-institutional-facility">
                        <flux:button icon="plus" color="primary" class="w-full sm:w-auto !rounded-2xl shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all">
                            {{ __('Add Facility') }}
                        </flux:button>
                    </flux:modal.trigger>
                </div>
            </div>

            {{-- Success Notification --}}
            @if (session()->has('message'))
                <div class="mb-8 animate-in fade-in slide-in-from-top-4 duration-500">
                    <div class="flex items-center gap-3 p-4 rounded-[1.5rem] bg-emerald-50 border border-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400">
                        <flux:icon.check-circle variant="micro" class="shrink-0" />
                        <span class="text-sm font-bold">{{ session('message') }}</span>
                    </div>
                </div>
            @endif

            {{-- CARD GRID --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
                @forelse ($institutional_facilities as $key => $data)
                    <div class="group relative flex flex-col bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-[2.5rem] p-6 hover:shadow-2xl hover:shadow-indigo-500/10 hover:border-indigo-500/30 transition-all duration-500">
                        
                        {{-- Card Top Row: ID & Status --}}
                        <div class="flex justify-between items-start mb-6">
                            <span class="text-[10px] font-black uppercase tracking-widest px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-500 rounded-full border border-slate-300 dark:border-slate-700">
                                {{ $data->facilities_id }}
                            </span>
                            
                            <div class="flex items-center gap-1.5">
                                <span class="text-[10px] font-bold uppercase tracking-tighter {{ $data->active_status ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $data->active_status ? 'Active' : 'Inactive' }}
                                </span>
                                <div class="h-1.5 w-1.5 rounded-full {{ $data->active_status ? 'bg-emerald-500' : 'bg-rose-500' }}"></div>
                            </div>
                        </div>

                        {{-- Card Content --}}
                        <div class="mb-6 flex flex-col items-center text-center">
                            <div class="mb-4 w-16 h-16 rounded-3xl bg-slate-50 dark:bg-slate-800/50 flex items-center justify-center group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/30 transition-colors duration-500">
                                <flux:icon.wrench-screwdriver class="text-slate-500 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors" />
                            </div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-white leading-tight uppercase tracking-tight group-hover:text-indigo-600 transition-colors">
                                {{ $data->name }}
                            </h3>
                            <p class="mt-3 text-xs text-slate-500 dark:text-slate-500 line-clamp-2 px-2">
                                {{ $data->description ?? 'No specific description provided for this facility.' }}
                            </p>
                        </div>

                        {{-- Card Footer/Actions --}}
                        <div class="mt-auto pt-4 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between">
                             <div class="flex items-center gap-2">
                                <flux:icon.hashtag variant="micro" class="text-slate-300" />
                                <span class="text-xs font-bold text-slate-500">#{{ $institutional_facilities->firstItem() + $key }}</span>
                             </div>
                             
                             <div class="flex items-center gap-1">
                                <flux:modal.trigger wire:click="editInstitutionalFacility({{ $data->id }})" name="edit-institutional-facility">
                                    <flux:button size="sm" variant="ghost" icon="pencil-square" class="!rounded-xl hover:!bg-indigo-50 hover:!text-indigo-600 transition-colors" />
                                </flux:modal.trigger>
                                
                                <flux:button 
                                    wire:click="toggleStatus({{ $data->id }})"
                                    wire:confirm="Are you sure you want to change the status for {{ $data->name }}?"
                                    size="sm" 
                                    variant="ghost" 
                                    icon="{{ $data->active_status ? 'no-symbol' : 'check' }}"
                                    class="!rounded-xl {{ $data->active_status ? 'hover:!bg-rose-50 hover:!text-rose-600' : 'hover:!bg-emerald-50 hover:!text-emerald-600' }} transition-colors" 
                                />
                             </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-24 flex flex-col items-center justify-center bg-slate-50 dark:bg-slate-900/50 rounded-[3rem] border-2 border-dashed border-slate-300 dark:border-slate-700">
                        <flux:icon.home-modern size="xl" class="text-slate-300 mb-4" />
                        <p class="text-slate-500 font-black uppercase tracking-widest text-sm italic">{{ __('No Facilities Found') }}</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination Container --}}
            <div class="mt-12">
                {{ $institutional_facilities->links() }}
            </div>
        </div>

        {{-- MODAL: ADD NEW --}}
        <flux:modal wire:model="showModelNewInstitutionalFacility" name="add-new-institutional-facility" class="w-full max-w-lg rounded-[2.5rem] p-10">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                        <flux:icon.plus />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black uppercase tracking-tight leading-none">{{ __('New Facility') }}</flux:heading>
                        <flux:text size="sm">{{ __('Add a new facility category to the system.') }}</flux:text>
                    </div>
                </div>

                <form wire:submit.prevent="addNewInstitutionalFacility" class="space-y-4 pt-2">
                    @csrf
                    <flux:field>
                        <flux:input label="Facility ID" wire:model.live="facilities_id" mask="FAC999" placeholder="FAC001" class="!rounded-xl" />
                    </flux:field>

                    <flux:field>
                        <flux:input label="Facility Name" wire:model.live="facilities_name" placeholder="e.g. Science Lab" class="!rounded-xl" />
                    </flux:field>

                    <flux:field>
                        <flux:textarea label="Facility Description" wire:model.live="description" class="!rounded-xl" />
                    </flux:field>

                    <div class="flex gap-3 pt-6">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-xl">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-xl shadow-lg shadow-indigo-500/20">{{ __('Add Facility') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- MODAL: EDIT --}}
        <flux:modal wire:model="showModelEditInstitutionalFacility" name="edit-institutional-facility" class="w-full max-w-lg rounded-[2.5rem] p-10">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white">
                        <flux:icon.pencil-square />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black uppercase tracking-tight leading-none">{{ __('Edit Facility') }}</flux:heading>
                        <flux:text size="sm">{{ __('Update institutional facility information.') }}</flux:text>
                    </div>
                </div>

                <form wire:submit.prevent="updateInstitutionalFacility" class="space-y-4 pt-2">
                    @csrf
                    <flux:field>
                        <flux:input disabled label="Facility ID" wire:model.live="update_facilities_id" mask="FAC999" class="!rounded-xl bg-slate-50 opacity-60" />
                    </flux:field>

                    <flux:field>
                        <flux:input label="Facility Name" wire:model.live="update_facilities_name" class="!rounded-xl" />
                    </flux:field>

                    <flux:field>
                        <flux:textarea label="Facility Description" wire:model.live="update_description" class="!rounded-xl" />
                    </flux:field>

                    <div class="flex gap-3 pt-6">
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