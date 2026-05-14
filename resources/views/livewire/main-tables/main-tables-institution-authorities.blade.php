<section class="w-full">

    <x-main-tables.layout>
        <div class="max-w-[1400px] mx-auto pb-12 px-4 lg:px-0">
            
            {{-- Section Header & Action Bar --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
                <div class="space-y-1">
                    <flux:heading size="xl" level="1" class="!font-black tracking-tight text-blue-600 dark:text-blue-400 uppercase">
                        {{ __('Institution Authorities') }}
                    </flux:heading>
                    <flux:subheading size="lg" class="flex items-center gap-2">
                        <flux:icon.shield-check variant="micro" class="text-slate-400" />
                        {{ __('Governing bodies and official institutional records') }}
                    </flux:subheading>
                </div>

                <div class="flex items-center gap-3">
                    <flux:modal.trigger name="add-new-ins-authority">
                        <flux:button icon="plus" color="primary" class="w-full sm:w-auto !rounded-2xl shadow-lg shadow-blue-500/20 hover:shadow-blue-500/40 transition-all">
                            {{ __('Add Authority') }}
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
                @forelse ($insauthorities as $key => $data)
                    <div class="group relative flex flex-col bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2.5rem] p-6 hover:shadow-2xl hover:shadow-blue-500/10 hover:border-blue-500/30 transition-all duration-500">
                        
                        {{-- Card Header: Code & Status --}}
                        <div class="flex justify-between items-start mb-4">
                            <span class="text-[10px] font-black uppercase tracking-widest px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 rounded-full border border-slate-200 dark:border-slate-700">
                                {{ $data->authority_id }}
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

                        {{-- Card Body: Name & Description --}}
                        <div class="mb-6 flex-grow">
                            <h3 class="text-lg font-black text-slate-900 dark:text-white leading-tight uppercase tracking-tight group-hover:text-blue-600 transition-colors">
                                {{ $data->authority_name }}
                            </h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-3 line-clamp-3 group-hover:line-clamp-none transition-all duration-300">
                                {{ $data->description }}
                            </p>
                        </div>

                        {{-- Card Footer: Meta Info --}}
                        <div class="mt-auto pt-4 border-t border-slate-100 dark:border-slate-800">
                            <div class="flex items-center gap-3">
                                <div class="flex shrink-0 w-10 h-10 items-center justify-center rounded-2xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 transition-colors group-hover:bg-blue-600 group-hover:text-white">
                                    <flux:icon.shield-check variant="micro" />
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-tighter leading-none mb-1">Entity Rank</p>
                                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-tight">
                                        Official Authority
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Hover Action Bar (Glassmorphism) --}}
                        <div class="absolute inset-x-0 -bottom-2 flex justify-center opacity-0 group-hover:opacity-100 group-hover:bottom-4 transition-all duration-300 pointer-events-none group-hover:pointer-events-auto">
                            <div class="flex items-center gap-1 bg-white/90 dark:bg-slate-800/90 backdrop-blur-md border border-slate-200 dark:border-slate-700 p-1.5 rounded-2xl shadow-xl">
                                <flux:modal.trigger wire:click="editInsAuthority({{ $data->id }})" name="edit-ins-authority">
                                    <flux:button size="sm" variant="ghost" icon="pencil-square" class="!rounded-xl hover:!bg-blue-50 hover:!text-blue-600 transition-colors" />
                                </flux:modal.trigger>
                                
                                <div class="w-px h-4 bg-slate-200 dark:bg-slate-700 mx-1"></div>
                                
                                <flux:button 
                                    wire:click="toggleStatus({{ $data->id }})"
                                    wire:confirm="Change status for {{ $data->authority_name }}?"
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
                        <p class="text-slate-400 font-black uppercase tracking-widest text-sm italic">{{ __('No Authorities Registered') }}</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-12 px-4">
                {{ $insauthorities->links() }}
            </div>
        </div>

        {{-- MODAL: ADD NEW --}}
        <flux:modal wire:model="showModelNewInsAuthority" name="add-new-ins-authority" class="w-full max-w-lg rounded-[2.5rem] p-10">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                        <flux:icon.plus />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black uppercase tracking-tight leading-none">{{ __('New Authority') }}</flux:heading>
                        <flux:text size="sm">{{ __('Register a new institutional authority body.') }}</flux:text>
                    </div>
                </div>

                <form wire:submit.prevent="addNewInsAuthority" class="space-y-4">
                    @csrf
                    <flux:field>
                        <flux:input label="Authority ID" wire:model.live="authority_id" mask="AUID99" placeholder="AUID01" class="!rounded-xl" />
                    </flux:field>

                    <flux:field>
                        <flux:input label="Authority Name" wire:model.live="authority_name" placeholder="Enter Official Name" class="!rounded-xl" />
                    </flux:field>

                    <flux:field>
                        <flux:textarea label="Description" wire:model.live="description" placeholder="Enter authority description..." class="!rounded-xl" rows="4" />
                    </flux:field>

                    <div class="flex gap-3 pt-6">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-xl">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-xl shadow-lg shadow-blue-500/20">{{ __('Register Authority') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- MODAL: EDIT --}}
        <flux:modal wire:model="showModelEditInsAuthority" name="edit-ins-authority" class="w-full max-w-lg rounded-[2.5rem] p-10">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-amber-200">
                        <flux:icon.pencil-square />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black uppercase tracking-tight leading-none">{{ __('Edit Authority') }}</flux:heading>
                        <flux:text size="sm">{{ __('Update governing body details.') }}</flux:text>
                    </div>
                </div>

                <form wire:submit.prevent="updateInsAuthority" class="space-y-4">
                    @csrf
                    <flux:field>
                        <flux:input disabled label="Authority ID" wire:model.live="update_authority_id" mask="AUID99" class="!rounded-xl bg-slate-50 opacity-60" />
                    </flux:field>

                    <flux:field>
                        <flux:input label="Authority Name" wire:model.live="update_authority_name" class="!rounded-xl" />
                    </flux:field>

                    <flux:field>
                        <flux:textarea label="Description" wire:model.live="update_description" class="!rounded-xl" rows="4" />
                    </flux:field>

                    <div class="flex gap-3 pt-6">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-xl">{{ __('Discard') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-xl shadow-lg shadow-blue-500/20">{{ __('Save Changes') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

    </x-main-tables.layout>
</section>