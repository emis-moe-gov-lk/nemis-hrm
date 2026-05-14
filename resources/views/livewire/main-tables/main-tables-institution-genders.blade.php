<section class="w-full">
    <x-main-tables.layout>
        <div class="max-w-[1400px] mx-auto pb-12 px-4 lg:px-0">
            
            {{-- Section Header & Action Bar --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
                <div class="space-y-1">
                    <flux:heading size="xl" level="1" class="!font-black tracking-tight text-rose-600 dark:text-rose-400 uppercase">
                        {{ __('Institution Genders') }}
                    </flux:heading>
                    <flux:subheading size="lg" class="flex items-center gap-2">
                        <flux:icon.user-plus variant="micro" class="text-slate-400" />
                        {{ __('Configure gender identifiers for system users and records') }}
                    </flux:subheading>
                </div>

                <div class="flex items-center gap-3">
                    <flux:modal.trigger name="add-new-institution-gender">
                        <flux:button icon="plus" color="primary" class="w-full sm:w-auto !rounded-2xl shadow-lg shadow-rose-500/20 hover:shadow-rose-500/40 transition-all">
                            {{ __('Add Gender') }}
                        </flux:button>
                    </flux:modal.trigger>
                </div>
            </div>

            {{-- Notifications --}}
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
                @forelse ($institutiongenders as $key => $data)
                    <div class="group relative flex flex-col bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2.5rem] p-6 hover:shadow-2xl hover:shadow-rose-500/10 hover:border-rose-500/30 transition-all duration-500">
                        
                        {{-- Card Header: ID & Status --}}
                        <div class="flex justify-between items-start mb-4">
                            <span class="text-[10px] font-black uppercase tracking-widest px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 rounded-full border border-slate-200 dark:border-slate-700">
                                {{ $data->gender_id }}
                            </span>
                            
                            <div class="flex items-center gap-1.5">
                                <span class="relative flex h-2 w-2">
                                    @if($data->active_status)
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                    @else
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                                    @endif
                                </span>
                                <span class="text-[10px] font-bold uppercase tracking-tighter {{ $data->active_status ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $data->active_status ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>

                        {{-- Card Body: Gender Name --}}
                        <div class="mb-10 flex-grow">
                            <h3 class="text-xl font-black text-slate-900 dark:text-white leading-tight uppercase tracking-tight group-hover:text-rose-600 transition-colors">
                                {{ $data->name }}
                            </h3>
                            <div class="mt-2 flex items-center gap-2">
                                <div class="h-1 w-8 bg-rose-100 dark:bg-rose-900 rounded-full group-hover:w-12 transition-all"></div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">System Identifier</span>
                            </div>
                        </div>

                        {{-- Card Footer --}}
                        <div class="mt-auto pt-4 border-t border-slate-100 dark:border-slate-800">
                            <div class="flex items-center gap-3">
                                <div class="flex shrink-0 w-10 h-10 items-center justify-center rounded-2xl bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 transition-colors group-hover:bg-rose-600 group-hover:text-white">
                                    <flux:icon.user variant="micro" />
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-tighter leading-none mb-1">Row Order</p>
                                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-tight">
                                        Sequence #{{ $institutiongenders->firstItem() + $key }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Hover Action Bar (Floating Glassmorphism) --}}
                        <div class="absolute inset-x-0 -bottom-2 flex justify-center opacity-0 group-hover:opacity-100 group-hover:bottom-4 transition-all duration-300 pointer-events-none group-hover:pointer-events-auto">
                            <div class="flex items-center gap-1 bg-white/95 dark:bg-slate-800/95 backdrop-blur-md border border-slate-200 dark:border-slate-700 p-1.5 rounded-2xl shadow-xl">
                                <flux:modal.trigger wire:click="editInstitutionGender({{ $data->id }})" name="edit-institution-gender">
                                    <flux:button size="sm" variant="ghost" icon="pencil-square" class="!rounded-xl hover:!bg-rose-50 hover:!text-rose-600 transition-colors" />
                                </flux:modal.trigger>
                                
                                <div class="w-px h-4 bg-slate-200 dark:bg-slate-700 mx-1"></div>
                                
                                <flux:button 
                                    wire:click="toggleStatus({{ $data->id }})"
                                    wire:confirm="Change status for {{ $data->name }}?"
                                    size="sm" 
                                    variant="ghost" 
                                    icon="{{ $data->active_status ? 'no-symbol' : 'check' }}"
                                    class="!rounded-xl {{ $data->active_status ? 'hover:!bg-rose-50 hover:!text-rose-600' : 'hover:!bg-emerald-50 hover:!text-emerald-600' }} transition-colors" 
                                />
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-24 flex flex-col items-center justify-center bg-slate-50 dark:bg-slate-900/50 rounded-[3rem] border-2 border-dashed border-slate-200 dark:border-slate-800">
                        <flux:icon.magnifying-glass size="xl" class="text-slate-300 mb-4" />
                        <p class="text-slate-400 font-black uppercase tracking-widest text-sm italic">{{ __('No Genders Configured') }}</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-12 px-4">
                {{ $institutiongenders->links() }}
            </div>
        </div>

        {{-- MODAL: ADD NEW --}}
        <flux:modal wire:model="showModelNewInstitutionGender" name="add-new-institution-gender" class="w-full max-w-lg rounded-[2.5rem] p-10">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-rose-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-rose-200">
                        <flux:icon.plus />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black uppercase tracking-tight leading-none">{{ __('New Gender') }}</flux:heading>
                        <flux:text size="sm">{{ __('Define a new gender classification for your institution.') }}</flux:text>
                    </div>
                </div>

                @if (session()->has('error'))
                    <div class="p-3 rounded-xl bg-rose-50 border border-rose-100 text-rose-800 text-xs font-bold">
                        {{ session('error') }}
                    </div>
                @endif

                <form wire:submit.prevent="addNewInstitutionGender" class="space-y-4 pt-2">
                    @csrf
                    <flux:field>
                        <flux:input label="Gender ID" wire:model.live="institutionGenderId" mask="IGID99" placeholder="IGID01" class="!rounded-xl" />
                    </flux:field>

                    <flux:field>
                        <flux:input label="Gender Name" wire:model.live="institutionGender" placeholder="e.g. Male, Female, Other" class="!rounded-xl" />
                    </flux:field>

                    <div class="flex gap-3 pt-6">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-xl">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-xl shadow-lg shadow-rose-500/20">{{ __('Add Gender') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- MODAL: EDIT --}}
        <flux:modal wire:model="showModelEditInstitutionGender" name="edit-institution-gender" class="w-full max-w-lg rounded-[2.5rem] p-10">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-amber-200">
                        <flux:icon.pencil-square />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black uppercase tracking-tight leading-none">{{ __('Edit Gender') }}</flux:heading>
                        <flux:text size="sm">{{ __('Modify the administrative gender properties.') }}</flux:text>
                    </div>
                </div>

                @if (session()->has('error'))
                    <div class="p-3 rounded-xl bg-rose-50 border border-rose-100 text-rose-800 text-xs font-bold">
                        {{ session('error') }}
                    </div>
                @endif

                <form wire:submit.prevent="updateInstitutionGenderList" class="space-y-4 pt-2">
                    @csrf
                    <flux:field>
                        <flux:input disabled label="Gender ID" wire:model.live="updateInstitutionGenderId" mask="IGID99" class="!rounded-xl bg-slate-50 opacity-60" />
                    </flux:field>

                    <flux:field>
                        <flux:input label="Gender Name" wire:model.live="updateInstitutionGender" class="!rounded-xl" />
                    </flux:field>

                    <div class="flex gap-3 pt-6">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-xl">{{ __('Discard') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-xl shadow-lg shadow-rose-500/20">{{ __('Save Changes') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>
    </x-main-tables.layout>
</section>