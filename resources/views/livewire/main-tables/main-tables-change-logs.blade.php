<section class="w-full">
    <x-main-tables.layout>
        <div class="max-w-[1400px] mx-auto pb-12">
            
            {{-- Section Header & Action Bar --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10 px-4 lg:px-0">
                <div class="space-y-1">
                    <flux:heading size="xl" level="1" class="!font-black tracking-tight text-slate-900 dark:text-white">
                        {{ __('Change Logs') }}
                    </flux:heading>
                    <flux:subheading size="lg" class="flex items-center gap-2">
                        <flux:icon.clock variant="micro" class="text-emerald-500" />
                        {{ __('Track system versions, feature updates, and technical refinements') }}
                    </flux:subheading>
                </div>

                <div class="flex items-center gap-3">
                    <flux:modal.trigger name="add-new-change-log">
                        <flux:button icon="plus" color="primary" class="w-full md:w-auto !rounded-2xl shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40 transition-all">
                            {{ __('Add New Change') }}
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

            {{-- Change Log Bento Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 px-4 lg:px-0">
                @forelse ($changelogs as $key => $data)
                    <div class="group relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2.5rem] p-6 transition-all duration-300 hover:shadow-2xl hover:shadow-emerald-500/10 hover:-translate-y-1.5 flex flex-col">
                        
                        {{-- Top Row: Version & Type --}}
                        <div class="flex justify-between items-start mb-6">
                            <div class="px-3 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                                <span class="text-xs font-black text-slate-600 dark:text-slate-400 uppercase tracking-tighter">
                                    {{ $data->version_id }}
                                </span>
                            </div>
                            
                            @php
                                $typeColor = match(strtolower($data->type)) {
                                    'added' => 'green',
                                    'fixed' => 'blue',
                                    'modified' => 'orange',
                                    'security' => 'red',
                                    'removed' => 'zinc',
                                    default => 'indigo'
                                };
                            @endphp
                            
                            <flux:badge size="sm" variant="pill" color="{{ $typeColor }}" class="!font-black uppercase tracking-widest text-[10px]">
                                {{ $data->type }}
                            </flux:badge>
                        </div>

                        {{-- Content --}}
                        <div class="space-y-4 mb-8">
                            <div>
                                <flux:heading size="lg" class="!font-black text-slate-900 dark:text-white group-hover:text-emerald-600 transition-colors">
                                    {{ $data->title }}
                                </flux:heading>
                                <div class="mt-3 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-transparent group-hover:border-emerald-100 dark:group-hover:border-emerald-900/30 transition-colors duration-300">
                                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed line-clamp-4">
                                        {{ $data->description }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Actions Footer --}}
                        <div class="flex items-center gap-2 mt-auto">
                            <flux:button 
                                wire:click="editChangeLog({{ $data->id }})" 
                                class="flex-1 !rounded-2xl !py-3 font-bold shadow-sm" 
                                variant="filled" 
                                icon="pencil-square">
                                {{ __('Edit') }}
                            </flux:button>
                            
                            <flux:button 
                                wire:click="deleteChangeLog({{ $data->id }})" 
                                wire:confirm="Are you sure you want to delete this Change Log?"
                                class="!rounded-2xl !py-3 px-4 shadow-sm" 
                                variant="ghost" 
                                color="red"
                                icon="trash" 
                            />
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 bg-slate-50/50 dark:bg-slate-800/10 rounded-[3rem] border-2 border-dashed border-slate-200 dark:border-slate-800 flex flex-col items-center justify-center text-center">
                        <div class="w-20 h-20 bg-emerald-50 dark:bg-emerald-900/20 rounded-full flex items-center justify-center mb-6 text-emerald-500">
                            <flux:icon.document-text size="xl" />
                        </div>
                        <flux:heading size="lg" class="!font-black text-slate-900 dark:text-white">{{ __('No Logs Recorded') }}</flux:heading>
                        <flux:text class="max-w-xs mx-auto">{{ __('All system updates and version changes will appear here once registered.') }}</flux:text>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-12 px-4">
                {{ $changelogs->links() }}
            </div>
        </div>

        {{-- MODAL: ADD NEW CHANGE LOG --}}
        <flux:modal wire:model="showModelNewChangeLog" name="add-new-change-log" class="w-full max-w-lg rounded-[2.5rem] p-8">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg" class="!font-black tracking-tight">{{ __('Register New Change') }}</flux:heading>
                    <flux:text>{{ __('Document a new system update or technical adjustment.') }}</flux:text>
                </div>

                @if (session()->has('error'))
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-100 text-rose-800 text-xs font-bold">
                        {{ session('error') }}
                    </div>
                @endif

                <form wire:submit.prevent="addNewChangeLog" class="space-y-5">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <flux:field>
                            <flux:select label="Version" id="versionId" wire:model.live="versionId" class="!rounded-xl shadow-sm">
                                <option value="">{{ __ ('Select Version') }}</option>
                                @foreach ($versionOption as $version)
                                    <option value="{{ $version->version_id }}">{{ $version->version }}</option>
                                @endforeach
                            </flux:select>
                        </flux:field>

                        <flux:field>
                            <flux:select label="Change Type" id="type" wire:model.live="type" class="!rounded-xl shadow-sm">
                                <option value="">{{ __ ('Select Type') }}</option>
                                <option value="added">{{ 'Added' }}</option>
                                <option value="modified">{{ 'Modified' }}</option>
                                <option value="fixed">{{ 'Fixed' }}</option>
                                <option value="removed">{{ 'Removed' }}</option>
                                <option value="security">{{ 'Security' }}</option>
                            </flux:select>
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:input label="Short Title" wire:model.live="title" class="!rounded-xl shadow-sm" placeholder="e.g. Optimized database queries" />
                    </flux:field>

                    <flux:field>
                        <flux:textarea label="Detailed Description" wire:model.live="description" class="!rounded-xl shadow-sm" rows="4" placeholder="Describe what was changed and why..." />
                    </flux:field>

                    <div class="flex flex-col-reverse md:flex-row gap-3 pt-6 border-t border-slate-100 dark:border-slate-800">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-xl">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-xl px-6 shadow-md shadow-emerald-500/20">{{ __('Add to Log') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- MODAL: EDIT CHANGE LOG --}}
        <flux:modal wire:model="showModelEditChangeLog" name="edit-change-log" class="w-full max-w-lg rounded-[2.5rem] p-8">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg" class="!font-black tracking-tight">{{ __('Edit Log Entry') }}</flux:heading>
                    <flux:text>{{ __('Modify the details of this change log entry.') }}</flux:text>
                </div>

                @if (session()->has('error'))
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-100 text-rose-800 text-xs font-bold">
                        {{ session('error') }}
                    </div>
                @endif

                <form wire:submit.prevent="updateChangeLog" class="space-y-5">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <flux:field>
                            <flux:select label="Version" id="updateVersionId" wire:model.live="updateVersionId" class="!rounded-xl shadow-sm">
                                <option value="">{{ __ ('Select Version') }}</option>
                                @foreach ($versionOption as $version)
                                    <option value="{{ $version->version_id }}">{{ $version->version }}</option>
                                @endforeach
                            </flux:select>
                        </flux:field>

                        <flux:field>
                            <flux:select label="Change Type" id="updateType" wire:model.live="updateType" class="!rounded-xl shadow-sm">
                                <option value="">{{ __ ('Select Type') }}</option>
                                <option value="added">{{ 'Added' }}</option>
                                <option value="modified">{{ 'Modified' }}</option>
                                <option value="fixed">{{ 'Fixed' }}</option>
                                <option value="removed">{{ 'Removed' }}</option>
                                <option value="security">{{ 'Security' }}</option>
                            </flux:select>
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:input label="Short Title" wire:model.live="updateTitle" class="!rounded-xl shadow-sm" />
                    </flux:field>

                    <flux:field>
                        <flux:textarea label="Detailed Description" wire:model.live="updateDescription" class="!rounded-xl shadow-sm" rows="4" />
                    </flux:field>

                    <div class="flex flex-col-reverse md:flex-row gap-3 pt-6 border-t border-slate-100 dark:border-slate-800">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-xl">{{ __('Discard') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-xl px-6 shadow-md shadow-emerald-500/20">{{ __('Update Log') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>
    </x-main-tables.layout>
</section>