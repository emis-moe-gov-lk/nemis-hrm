<section class="w-full">
    {{-- Main Page Header --}}
    <div class="relative mb-8 w-full px-4 lg:px-0">
        <div class="flex items-center gap-2 mb-2">
            <span class="px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 text-[10px] font-bold uppercase tracking-widest">
                System Administration
            </span>
        </div>
        <flux:heading size="xl" level="1" class="!font-black tracking-tight text-slate-900 dark:text-white uppercase">
            {{ __('Main System Tables Overview') }}
        </flux:heading>
        <flux:subheading size="lg" class="mb-6 text-slate-500">
            {{ __('Manage core database registers and classification settings') }}
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <x-main-tables.layout>
        <div class="max-w-[1400px] mx-auto pb-12 px-4 lg:px-0">
            
            {{-- Section Specific Header --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
                <div class="space-y-1">
                    <div class="flex items-center gap-3 mb-1">
                        <div class="p-2 rounded-xl bg-indigo-500/10">
                            <flux:icon.book-open variant="micro" class="text-indigo-600" />
                        </div>
                        <flux:heading size="xl" level="1" class="!font-black tracking-tighter text-slate-900 dark:text-white">
                            {{ __('SLEAS Subjects') }}
                        </flux:heading>
                    </div>
                    <flux:subheading size="lg" class="text-slate-500 ml-11">
                        {{ __('Education Administrator Service Subjects and related information') }}
                    </flux:subheading>
                </div>

                <div class="flex items-center gap-3">
                    <flux:modal.trigger name="add-new-sleas-subject">
                        <flux:button icon="plus" color="primary" class="w-full md:w-auto !rounded-2xl shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all py-2.5">
                            {{ __('Add new Subject') }}
                        </flux:button>
                    </flux:modal.trigger>
                </div>
            </div>

            {{-- Flash Messages --}}
            @if (session()->has('message'))
                <div class="mb-8 animate-in fade-in slide-in-from-top-4 duration-500">
                    <div class="flex items-center gap-3 p-4 rounded-[1.5rem] bg-emerald-50 border border-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400">
                        <flux:icon.check-circle variant="micro" class="shrink-0" />
                        <span class="text-sm font-bold tracking-tight">{{ session('message') }}</span>
                    </div>
                </div>
            @endif

            {{-- SUBJECT CARDS GRID --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
                @forelse ($sleasSubjects as $key => $data)
                    <div class="relative group bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-[2.5rem] p-7 shadow-sm hover:shadow-2xl hover:border-indigo-400 dark:hover:border-indigo-800 transition-all duration-300 flex flex-col justify-between overflow-hidden">
                        
                        {{-- ID & Status Bar --}}
                        <div class="flex justify-between items-start mb-6 relative z-10">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black text-slate-500 tabular-nums tracking-[0.2em] uppercase mb-1.5">
                                    REF #{{ str_pad($sleasSubjects->firstItem() + $key, 2, '0', STR_PAD_LEFT) }}
                                </span>
                                <div class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-50 dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 font-mono text-xs font-bold ring-1 ring-slate-200 dark:ring-slate-700">
                                    {{ $data->eas_subject_id }}
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-2 bg-white dark:bg-slate-800 px-3 py-1.5 rounded-full border border-slate-200 dark:border-slate-700 shadow-sm">
                                <div class="size-2 rounded-full {{ $data->active_status ? 'bg-emerald-500 animate-pulse shadow-[0_0_8px_rgba(16,185,129,0.8)]' : 'bg-slate-300' }}"></div>
                                <span class="text-[9px] font-black uppercase tracking-widest text-slate-500">
                                    {{ $data->active_status ? 'Live' : 'Hidden' }}
                                </span>
                            </div>
                        </div>

                        {{-- Subject Body --}}
                        <div class="mb-10 relative z-10">
                            <h3 class="text-xl font-black text-slate-800 dark:text-slate-100 leading-tight group-hover:text-indigo-600 transition-colors uppercase tracking-tight">
                                {{ $data->subject }}
                            </h3>
                        </div>

                        {{-- Actions --}}
                        <div class="flex gap-2 relative z-10">
                            <flux:modal.trigger wire:click="editSleasSubject({{ $data->id }})" class="flex-1">
                                <flux:button size="sm" variant="ghost" icon="pencil-square" class="w-full !rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-indigo-700 font-bold text-xs uppercase tracking-wider">
                                    {{ __('Edit') }}
                                </flux:button>
                            </flux:modal.trigger>
                            
                            <flux:button 
                                wire:click="toggleStatus({{ $data->id }})"
                                wire:confirm="Are you sure you want to change status for: {{ $data->subject }}?"
                                size="sm" 
                                variant="filled" 
                                color="{{ $data->active_status == '1' ? 'red' : 'indigo' }}"
                                icon="{{ $data->active_status == '1' ? 'no-symbol' : 'check' }}"
                                class="!rounded-xl shadow-md transition-transform active:scale-95"
                            />
                        </div>

                        {{-- Decorative Background Icon --}}
                        <div class="absolute -bottom-4 -right-4 opacity-[0.03] dark:opacity-[0.07] group-hover:opacity-10 transition-all duration-500 rotate-12 group-hover:rotate-0">
                            <flux:icon.book-open size="xl" class="size-32 text-indigo-900 dark:text-white" />
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-32 text-center bg-slate-50/50 dark:bg-slate-900/50 rounded-[3rem] border-2 border-dashed border-slate-300 dark:border-slate-700">
                        <div class="inline-flex items-center justify-center w-24 h-24 rounded-[2.5rem] bg-white dark:bg-slate-800 shadow-xl mb-6 text-slate-300">
                            <flux:icon.magnifying-glass size="xl" />
                        </div>
                        <h3 class="text-xl font-black text-slate-500 uppercase tracking-[0.3em]">{{ __('No Subjects Registered') }}</h3>
                        <p class="text-slate-500 mt-2 italic text-sm">Click "Add new Subject" to get started</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-16 px-6">
                {{ $sleasSubjects->links() }}
            </div>
        </div>

        {{-- MODAL: ADD NEW SUBJECT --}}
        <flux:modal wire:model="showModelNewSleasSubject" name="add-new-sleas-subject" class="w-full max-w-md rounded-[3rem] p-10 overflow-hidden">
            <div class="space-y-8">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 bg-indigo-600 rounded-[1.75rem] flex items-center justify-center text-white shadow-xl shadow-indigo-200 dark:shadow-none">
                        <flux:icon.plus />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black tracking-tight uppercase">{{ __('New Registration') }}</flux:heading>
                        <flux:text class="text-xs font-bold text-indigo-500 uppercase tracking-widest">{{ __('SLEAS Subject Registry') }}</flux:text>
                    </div>
                </div>

                @if (session()->has('error'))
                    <div class="p-4 rounded-2xl bg-red-50 text-red-800 text-xs font-black uppercase tracking-tighter border border-red-100">
                        {{ session('error') }}
                    </div>
                @endif

                <form wire:submit.prevent="addNewSleasSubject" class="space-y-6">
                    @csrf
                    <flux:field>
                        <flux:input label="Subject ID" wire:model.live="sleasSubjectId" mask="EAS999" placeholder="EAS000" class="!rounded-2xl !py-3 font-mono" />
                    </flux:field>

                    <flux:field>
                        <flux:input label="Subject Title" wire:model.live="sleasSubject" placeholder="e.g. Educational Psychology" class="!rounded-2xl !py-3" />
                    </flux:field>

                    <div class="flex gap-3 pt-6">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-2xl !py-3 font-bold uppercase tracking-widest text-[10px]">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-2xl !py-3 font-bold uppercase tracking-widest text-[10px] shadow-lg shadow-indigo-500/30">
                            {{ __('Register Subject') }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- MODAL: EDIT SUBJECT --}}
        <flux:modal wire:model="showModelEditSleasSubject" name="edit-sleas-subject" class="w-full max-w-md rounded-[3rem] p-10">
            <div class="space-y-8">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 bg-slate-800 dark:bg-slate-700 rounded-[1.75rem] flex items-center justify-center text-white shadow-xl">
                        <flux:icon.pencil-square />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black tracking-tight uppercase">{{ __('Edit Subject') }}</flux:heading>
                        <flux:text class="text-xs font-bold text-slate-500 uppercase tracking-widest">{{ __('Update System Information') }}</flux:text>
                    </div>
                </div>

                <form wire:submit.prevent="updateSleasSubjectList" class="space-y-6">
                    @csrf
                    <flux:field>
                        <flux:input disabled label="Subject ID" wire:model.live="updateSleasSubjectId" mask="EAS999" class="!rounded-2xl !py-3 bg-slate-50 font-mono" />
                    </flux:field>

                    <flux:field>
                        <flux:input label="Subject Title" wire:model.live="updateSleasSubject" class="!rounded-2xl !py-3" />
                    </flux:field>

                    <div class="flex gap-3 pt-6">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-2xl !py-3 font-bold uppercase tracking-widest text-[10px]">{{ __('Discard') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-2xl !py-3 font-bold uppercase tracking-widest text-[10px] shadow-lg shadow-indigo-500/30">
                            {{ __('Save Changes') }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

    </x-main-tables.layout>
</section>