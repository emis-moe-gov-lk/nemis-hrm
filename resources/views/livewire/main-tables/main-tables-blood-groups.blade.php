<section class="w-full">
    <x-main-tables.layout>
        <div class="max-w-[1400px] mx-auto pb-12">
            
            {{-- Section Header & Action Bar --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10 px-4 lg:px-0">
                <div class="space-y-1">
                    <flux:heading size="xl" level="1" class="!font-black tracking-tight text-slate-900 dark:text-white">
                        {{ __('Blood Groups') }}
                    </flux:heading>
                    <flux:subheading size="lg" class="flex items-center gap-2">
                        <flux:icon.variable variant="micro" class="text-rose-500" />
                        {{ __('Manage medical blood types and system identifiers') }}
                    </flux:subheading>
                </div>

                <div class="flex items-center gap-3">
                    <flux:modal.trigger name="add-new-blood-group">
                        <flux:button icon="plus" color="primary" class="w-full md:w-auto !rounded-2xl shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all">
                            {{ __('Add New Blood Group') }}
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

            {{-- Card Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 px-4 lg:px-0">
                @forelse ($bloodgroup as $key => $data)
                    <div class="group relative bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-[2.5rem] p-6 transition-all duration-300 hover:shadow-2xl hover:shadow-slate-200/60 dark:hover:shadow-rose-500/10 hover:-translate-y-1.5 flex flex-col">
                        
                        {{-- Top Row: Index & Status --}}
                        <div class="flex justify-between items-start mb-6">
                            <div class="flex items-center justify-center w-10 h-10 rounded-2xl bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 font-black text-xs ring-1 ring-rose-100 dark:ring-rose-800">
                                {{ $bloodgroup->firstItem() + $key }}
                            </div>
                            
                            <flux:badge size="sm" variant="pill" color="{{ $data->active_status ? 'green' : 'red' }}" class="!font-black ring-1 ring-inset shadow-sm">
                                {{ $data->active_status ? __('Active') : __('Inactive') }}
                            </flux:badge>
                        </div>

                        {{-- Core Content: Large Blood Group Display --}}
                        <div class="space-y-4 mb-8 text-center">
                            <div class="py-6 bg-slate-50 dark:bg-slate-800/50 rounded-3xl border border-transparent group-hover:border-rose-100 dark:group-hover:border-rose-900/30 transition-colors duration-300">
                                <span class="block text-[10px] font-bold uppercase text-slate-500 mb-1 tracking-widest">{{ __('Blood Type') }}</span>
                                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $data->blood_group }}</span>
                            </div>
                            
                            <div class="flex items-center justify-center gap-2">
                                <span class="text-[10px] font-bold uppercase text-slate-500 tracking-wider">{{ __('System ID:') }}</span>
                                <span class="text-xs font-bold text-slate-600 dark:text-slate-300">{{ $data->blood_group_id }}</span>
                            </div>
                        </div>

                        {{-- Action Footer --}}
                        <div class="flex items-center gap-2 mt-auto">
                            <flux:button 
                                wire:click="editBloodGroup({{ $data->id }})" 
                                class="flex-1 !rounded-2xl !py-3 font-bold shadow-sm" 
                                variant="filled" 
                                icon="pencil-square">
                                {{ __('Edit') }}
                            </flux:button>
                            
                            <flux:button 
                                wire:click="toggleStatus({{ $data->id }})" 
                                wire:confirm="Are you sure you want to {{ $data->active_status ? 'deactivate' : 'activate' }} this blood group?"
                                class="!rounded-2xl !py-3 px-4 shadow-sm" 
                                variant="ghost" 
                                color="{{ $data->active_status ? 'red' : 'green' }}"
                                icon="{{ $data->active_status ? 'no-symbol' : 'check' }}" 
                            />
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 bg-slate-50/50 dark:bg-slate-800/10 rounded-[3rem] border-2 border-dashed border-slate-300 dark:border-slate-700 flex flex-col items-center justify-center text-center">
                        <div class="w-20 h-20 bg-rose-50 dark:bg-rose-900/20 rounded-full flex items-center justify-center mb-6 text-rose-500">
                            <flux:icon.variable size="xl" />
                        </div>
                        <flux:heading size="lg" class="!font-black text-slate-900 dark:text-white">{{ __('No Blood Groups Found') }}</flux:heading>
                        <flux:text class="max-w-xs mx-auto">{{ __('Populate your system by adding standard blood types like A+, B-, etc.') }}</flux:text>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-12 px-4">
                {{ $bloodgroup->links() }}
            </div>
        </div>

        {{-- MODAL: ADD NEW --}}
        <flux:modal wire:model="showModelNewBloodGroup" name="add-new-blood-group" class="w-full max-w-md rounded-[2.5rem] p-8">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg" class="!font-black tracking-tight">{{ __('Add Blood Group') }}</flux:heading>
                    <flux:text>{{ __('Create a new entry for the system blood registry.') }}</flux:text>
                </div>

                @if (session()->has('error'))
                    <div class="p-3 rounded-xl bg-rose-50 border border-rose-100 text-rose-800 text-xs font-bold animate-pulse">
                        {{ session('error') }}
                    </div>
                @endif

                <form wire:submit.prevent="addNewBloodGroup" class="space-y-5">
                    @csrf
                    <flux:field>
                        <flux:input label="Blood Group ID" wire:model.live="bloodGroupId" 
                            class="!rounded-xl shadow-sm" placeholder="B01" mask="B99"/>
                    </flux:field>

                    <flux:field>
                        <flux:input label="Blood Group Name" wire:model.live="bloodGroup" class="!rounded-xl shadow-sm" placeholder="e.g. O Positive" />
                    </flux:field>

                    <div class="flex flex-col-reverse md:flex-row gap-3 pt-6">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-xl">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-xl px-6 shadow-md shadow-indigo-500/20">{{ __('Add Group') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- MODAL: EDIT --}}
        <flux:modal wire:model="showModelEditBloodGroup" name="edit-blood-group" class="w-full max-w-md rounded-[2.5rem] p-8">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg" class="!font-black tracking-tight">{{ __('Edit Blood Group') }}</flux:heading>
                    <flux:text>{{ __('Update the identifier or name for this entry.') }}</flux:text>
                </div>

                @if (session()->has('error'))
                    <div class="p-3 rounded-xl bg-rose-50 border border-rose-100 text-rose-800 text-xs font-bold">
                        {{ session('error') }}
                    </div>
                @endif

                <form wire:submit.prevent="updateBloodGroupList" class="space-y-5">
                    @csrf
                    <flux:field>
                        <flux:input disabled label="Blood Group ID" wire:model.live="updateBloodGroupId" 
                            class="!rounded-xl bg-slate-50 dark:bg-slate-800 border-dashed shadow-none" mask="B99"/>
                    </flux:field>

                    <flux:field>
                        <flux:input label="Blood Group Name" wire:model.live="updateBloodGroup" class="!rounded-xl shadow-sm" />
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