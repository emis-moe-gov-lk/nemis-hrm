<section class="w-full">
    <x-main-tables.layout>
        <div class="max-w-[1400px] mx-auto pb-12 px-4 lg:px-0">
            
            {{-- Section Header & Action Bar --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
                <div class="space-y-1">
                    <flux:heading size="xl" level="1" class="!font-black tracking-tight text-slate-900 dark:text-white uppercase">
                        {{ __('System Versions') }}
                    </flux:heading>
                    <flux:subheading size="lg" class="flex items-center gap-2">
                         <flux:icon.map variant="micro" class="text-indigo-500" />
                        {{ __('Track deployment history and current production builds') }}
                    </flux:subheading>
                </div>

                <div class="flex items-center gap-3">
                    <flux:modal.trigger name="add-new-version">
                        <flux:button icon="plus" color="primary" class="w-full md:w-auto !rounded-2xl shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all">
                            {{ __('Release New Version') }}
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

            {{-- VERSIONS CARDS GRID --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse ($versions as $key => $data)
                    <div class="relative flex flex-col bg-white dark:bg-slate-900 border {{ $data->is_latest ? 'border-indigo-500 ring-1 ring-indigo-500/20' : 'border-slate-200 dark:border-slate-800' }} rounded-[2.5rem] p-7 shadow-sm hover:shadow-xl transition-all duration-300">
                        
                        {{-- Top Metadata & "Latest" Badge --}}
                        <div class="flex justify-between items-start mb-6">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black text-slate-400 tabular-nums tracking-widest uppercase">
                                    #{{ $versions->firstItem() + $key }} • {{ $data->version_id }}
                                </span>
                                <div class="flex items-center gap-2 mt-1 text-slate-500 dark:text-slate-400">
                                    <flux:icon.calendar variant="micro" />
                                    <span class="text-xs font-bold">{{ $data->release_date->format('M d, Y') }}</span>
                                </div>
                            </div>
                            
                            @if($data->is_latest)
                                <flux:badge size="sm" variant="pill" color="green" class="!font-black uppercase tracking-widest text-[9px] animate-pulse">
                                    Latest Release
                                </flux:badge>
                            @else
                                <flux:badge size="sm" variant="pill" color="slate" class="!font-black uppercase tracking-widest text-[9px]">
                                    Archived
                                </flux:badge>
                            @endif
                        </div>

                        {{-- Version Info --}}
                        <div class="mb-6 flex-grow">
                            <h3 class="text-3xl font-black text-slate-900 dark:text-white leading-tight mb-2 italic tracking-tighter">
                                {{ $data->version }}
                            </h3>
                            <h4 class="text-sm font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wide mb-3">
                                {{ $data->title }}
                            </h4>
                            <p class="text-sm text-slate-600 dark:text-slate-400 line-clamp-3 leading-relaxed">
                                {{ $data->description }}
                            </p>
                        </div>

                        {{-- Actions Footer --}}
                        <div class="flex gap-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <flux:modal.trigger name="edit-version" wire:click="editVersion({{ $data->id }})" class="flex-1">
                                <flux:button size="sm" variant="ghost" icon="pencil-square" class="w-full !rounded-xl border border-slate-200 dark:border-slate-700">
                                    {{ __('Edit Details') }}
                                </flux:button>
                            </flux:modal.trigger>
                            
                            <flux:button 
                                wire:click="toggleStatus({{ $data->id }})"
                                wire:confirm="Are you sure you want to change the 'Latest' status for this version?"
                                size="sm" 
                                variant="filled" 
                                color="{{ $data->is_latest ? 'red' : 'primary' }}"
                                icon="{{ $data->is_latest ? 'no-symbol' : 'check' }}"
                                class="!rounded-xl shadow-sm"
                            />
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-24 text-center">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-[2rem] bg-slate-50 dark:bg-slate-900 border-2 border-dashed border-slate-200 dark:border-slate-800 mb-4 text-slate-300">
                            <flux:icon.cpu-chip size="xl" />
                        </div>
                        <h3 class="text-lg font-black text-slate-400 uppercase tracking-widest">{{ __('No Deployment History Found') }}</h3>
                    </div>
                @endforelse
            </div>

            <div class="mt-12">
                {{ $versions->links() }}
            </div>
        </div>

        {{-- MODAL: ADD NEW VERSION --}}
        <flux:modal wire:model="showModelNewVersion" name="add-new-version" class="w-full max-w-xl rounded-[2.5rem] p-8">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-indigo-50 dark:bg-indigo-900/30 rounded-[1.5rem] flex items-center justify-center text-indigo-600">
                        <flux:icon.plus />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black tracking-tight uppercase">{{ __('New System Build') }}</flux:heading>
                        <flux:text>{{ __('Register a new software version in the system repository.') }}</flux:text>
                    </div>
                </div>

                <form wire:submit.prevent="addNewVersion" class="space-y-6">
                    @csrf
                    <div class="p-6 bg-slate-50 dark:bg-slate-800/50 rounded-[2rem] border border-slate-100 dark:border-slate-800 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <flux:field>
                                <flux:input label="Version ID" wire:model.live="versionId" mask="VER999" placeholder="VER001" class="!rounded-xl" />
                            </flux:field>
                            <flux:field>
                                <flux:input label="Version Number" wire:model.live="version" placeholder="1.0.0" class="!rounded-xl" />
                            </flux:field>
                        </div>

                        <flux:field>
                            <flux:input label="Release Date" type="date" wire:model.live="releaseDate" class="!rounded-xl" />
                        </flux:field>

                        <flux:field>
                            <flux:input label="Title" wire:model.live="title" placeholder="Major UI Overhaul" class="!rounded-xl" />
                        </flux:field>

                        <flux:field>
                            <flux:textarea label="Description" wire:model.live="description" rows="3" placeholder="Summary of changes..." class="!rounded-xl" />
                        </flux:field>

                        <flux:field class="flex items-center gap-3">
                            <flux:checkbox wire:model.live="isLatest" />
                            <flux:label class="!mb-0">{{ __('Set as latest production version') }}</flux:label>
                        </flux:field>
                    </div>

                    <div class="flex gap-3">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-xl">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-xl shadow-lg shadow-indigo-500/20">{{ __('Publish Version') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- MODAL: EDIT VERSION --}}
        <flux:modal wire:model="showModelEditVersion" name="edit-version" class="w-full max-w-xl rounded-[2.5rem] p-8">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-indigo-50 dark:bg-indigo-900/30 rounded-[1.5rem] flex items-center justify-center text-indigo-600">
                        <flux:icon.pencil-square />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black tracking-tight uppercase">{{ __('Update Build Info') }}</flux:heading>
                        <flux:text>{{ __('Modify historical release data or change production status.') }}</flux:text>
                    </div>
                </div>

                <form wire:submit.prevent="updateVersionList" class="space-y-6">
                    @csrf
                    <div class="p-6 bg-slate-50 dark:bg-slate-800/50 rounded-[2rem] border border-slate-100 dark:border-slate-800 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <flux:field>
                                <flux:input disabled label="Version ID" wire:model.live="updateVersionId" class="!rounded-xl opacity-70" />
                            </flux:field>
                            <flux:field>
                                <flux:input label="Version Number" wire:model.live="updateVersion" class="!rounded-xl" />
                            </flux:field>
                        </div>

                        <flux:field>
                            <flux:input label="Release Date" type="date" wire:model.live="updateReleaseDate" class="!rounded-xl" />
                        </flux:field>

                        <flux:field>
                            <flux:input label="Title" wire:model.live="updateTitle" class="!rounded-xl" />
                        </flux:field>

                        <flux:field>
                            <flux:textarea label="Description" wire:model.live="updateDescription" rows="3" class="!rounded-xl" />
                        </flux:field>

                        <flux:field class="flex items-center gap-3">
                            <flux:checkbox wire:model.live="updateIsLatest" />
                            <flux:label class="!mb-0">{{ __('Set as latest production version') }}</flux:label>
                        </flux:field>
                    </div>

                    <div class="flex gap-3">
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