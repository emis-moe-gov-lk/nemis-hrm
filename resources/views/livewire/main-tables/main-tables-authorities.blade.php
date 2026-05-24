<section class="w-full">
    <x-main-tables.layout>
        <div class="max-w-[1400px] mx-auto pb-12">
            
            {{-- Section Header & Action Bar --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10 px-4 lg:px-0">
                <div class="space-y-1">
                    <flux:heading size="xl" level="1" class="!font-black tracking-tight text-slate-900 dark:text-white">
                        {{ __('Authorities') }}
                    </flux:heading>
                    <flux:subheading size="lg" class="flex items-center gap-2">
                        <flux:icon.shield-check variant="micro" class="text-indigo-500" />
                        {{ __('Manage permission-granting bodies and their descriptions') }}
                    </flux:subheading>
                </div>

                <div class="flex items-center gap-3">
                    <flux:modal.trigger name="add-new-authority">
                        <flux:button icon="plus" color="primary" class="w-full md:w-auto !rounded-2xl shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all">
                            {{ __('Add New Authority') }}
                        </flux:button>
                    </flux:modal.trigger>
                </div>
            </div>

            {{-- Notifications --}}
            @if (session()->has('message'))
                <div class="mx-4 lg:mx-0 mb-8 animate-in fade-in slide-in-from-top-4 duration-500">
                    <div class="flex items-center gap-3 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400 shadow-sm">
                        <flux:icon.check-circle variant="micro" class="shrink-0" />
                        <span class="text-sm font-bold">{{ session('message') }}</span>
                    </div>
                </div>
            @endif

            {{-- Responsive Card Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 px-4 lg:px-0">
                @forelse ($authorities as $key => $data)
                    <div class="group relative bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-[2.5rem] p-6 transition-all duration-300 hover:shadow-2xl hover:shadow-slate-200/60 dark:hover:shadow-indigo-500/10 hover:-translate-y-1.5 flex flex-col">
                        
                        {{-- Top Row: ID & Status --}}
                        <div class="flex justify-between items-start mb-6">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 font-black text-xs ring-1 ring-indigo-100 dark:ring-indigo-800">
                                    {{ $authorities->firstItem() + $key }}
                                </div>
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-500">{{ __('ID Code') }}</p>
                                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $data->authority_id }}</p>
                                </div>
                            </div>
                            
                            <flux:badge size="sm" variant="pill" color="{{ $data->active_status ? 'green' : 'red' }}" class="!font-black ring-1 ring-inset shadow-sm">
                                {{ $data->active_status ? __('Active') : __('Inactive') }}
                            </flux:badge>
                        </div>

                        {{-- Content Row: Name & Description --}}
                        <div class="space-y-4 mb-8">
                            <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-transparent group-hover:border-indigo-100 dark:group-hover:border-indigo-900/30 transition-colors duration-300">
                                <span class="block text-[10px] font-bold uppercase text-slate-500 mb-1 tracking-wider">{{ __('Authority Name') }}</span>
                                <span class="text-sm font-bold text-slate-800 dark:text-slate-100 truncate block">{{ $data->authority_name }}</span>
                            </div>
                            
                            <div class="p-4 border border-slate-200 dark:border-slate-700 rounded-2xl bg-white/50 dark:bg-slate-900/50 h-24 overflow-y-auto custom-scrollbar">
                                <span class="block text-[10px] font-bold uppercase text-slate-500 mb-1 tracking-wider">{{ __('Description') }}</span>
                                <p class="text-xs leading-relaxed text-slate-600 dark:text-slate-500">
                                    {{ $data->description ?: __('No description provided.') }}
                                </p>
                            </div>
                        </div>

                        {{-- Action Footer --}}
                        <div class="flex items-center gap-2 mt-auto">
                            <flux:button 
                                wire:click="editAuthority({{ $data->id }})" 
                                class="flex-1 !rounded-2xl !py-3 font-bold shadow-sm" 
                                variant="filled" 
                                icon="pencil-square">
                                {{ __('Edit') }}
                            </flux:button>
                            
                            <flux:button 
                                wire:click="toggleStatus({{ $data->id }})" 
                                wire:confirm="Are you sure you want to {{ $data->active_status ? 'deactivate' : 'activate' }} this authority?"
                                class="!rounded-2xl !py-3 px-4 shadow-sm" 
                                variant="ghost" 
                                color="{{ $data->active_status ? 'red' : 'green' }}"
                                icon="{{ $data->active_status ? 'no-symbol' : 'check' }}" 
                            />
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 bg-slate-50/50 dark:bg-slate-800/10 rounded-[3rem] border-2 border-dashed border-slate-300 dark:border-slate-700 flex flex-col items-center justify-center text-center">
                        <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-6">
                            <flux:icon.shield-exclamation class="text-slate-500" size="xl" />
                        </div>
                        <flux:heading size="lg" class="!font-black text-slate-900 dark:text-white">{{ __('No Authorities Found') }}</flux:heading>
                        <flux:text class="max-w-xs mx-auto">{{ __('Start by creating a new authority body for your system.') }}</flux:text>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-12 px-4">
                {{ $authorities->links() }}
            </div>
        </div>

        {{-- MODAL: ADD NEW --}}
        <flux:modal wire:model="showModelNewAuthority" name="add-new-authority" class="w-full max-w-lg rounded-[2.5rem] p-8">
            <div class="space-y-6">
                <div class="text-center md:text-left">
                    <flux:heading size="lg" class="!font-black tracking-tight">{{ __('Register New Authority') }}</flux:heading>
                    <flux:text>{{ __('Add a new governing or administrative body.') }}</flux:text>
                </div>

                <form wire:submit.prevent="addNewAuthority" class="space-y-5">
                    @csrf
                    <flux:field>
                        <flux:input label="Authority ID" wire:model.live="authority_id" 
                            class="!rounded-xl shadow-sm" placeholder="AUID01" mask="AUID99"/>
                    </flux:field>

                    <flux:field>
                        <flux:input label="Authority Name" wire:model.live="authority_name" class="!rounded-xl shadow-sm" placeholder="e.g. Ministry of Education" />
                    </flux:field>

                    <flux:field>
                        <flux:textarea label="Description" wire:model.live="description" class="!rounded-xl shadow-sm" placeholder="Provide details about this authority..." rows="3" />
                    </flux:field>

                    <div class="flex flex-col-reverse md:flex-row gap-3 pt-6">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-xl">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-xl px-6 shadow-md shadow-indigo-500/20">{{ __('Create Authority') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- MODAL: EDIT --}}
        <flux:modal wire:model="showModelEditAuthority" name="edit-authority" class="w-full max-w-lg rounded-[2.5rem] p-8">
            <div class="space-y-6">
                <div class="text-center md:text-left">
                    <flux:heading size="lg" class="!font-black tracking-tight">{{ __('Update Authority') }}</flux:heading>
                    <flux:text>{{ __('Modify authority details and descriptions.') }}</flux:text>
                </div>

                <form wire:submit.prevent="updateAuthority" class="space-y-5">
                    @csrf
                    <flux:field>
                        <flux:input disabled label="Authority ID" wire:model.live="update_authority_id" 
                            class="!rounded-xl bg-slate-50 dark:bg-slate-800 border-dashed shadow-none" mask="AUID99"/>
                    </flux:field>

                    <flux:field>
                        <flux:input label="Authority Name" wire:model.live="update_authority_name" class="!rounded-xl shadow-sm" />
                    </flux:field>

                    <flux:field>
                        <flux:textarea label="Description" wire:model.live="update_description" class="!rounded-xl shadow-sm" rows="3" />
                    </flux:field>

                    <div class="flex flex-col-reverse md:flex-row gap-3 pt-6">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-xl">{{ __('Discard') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-xl px-6 shadow-md shadow-indigo-500/20">{{ __('Save Changes') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

    </x-main-tables.layout>
</section>