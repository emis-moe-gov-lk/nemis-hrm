<section class="w-full">
    <x-main-tables.layout>
        <div class="max-w-[1400px] mx-auto pb-12">
            
            {{-- 1. Header & Action Bar --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10 px-4 lg:px-0">
                <div class="space-y-1">
                    <flux:heading size="xl" level="1" class="!font-black tracking-tight text-slate-900 dark:text-white">
                        {{ __('Appointed Subjects') }}
                    </flux:heading>
                    <flux:subheading size="lg" class="flex items-center gap-2">
                        <flux:icon.book-open variant="micro" class="text-indigo-500" />
                        {{ __('Manage subject registry and translations') }}
                    </flux:subheading>
                </div>

                <div class="flex items-center gap-3">
                    <flux:modal.trigger name="add-new-apointed-subject">
                        <flux:button icon="plus" color="primary" class="w-full md:w-auto !rounded-2xl shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all">
                            {{ __('Add New Subject') }}
                        </flux:button>
                    </flux:modal.trigger>
                </div>
            </div>

            {{-- 2. Notifications --}}
            @if (session()->has('message'))
                <div class="mx-4 lg:mx-0 mb-8 animate-in fade-in slide-in-from-top-4 duration-500">
                    <div class="flex items-center gap-3 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400 shadow-sm">
                        <flux:icon.check-circle variant="micro" class="shrink-0" />
                        <span class="text-sm font-bold">{{ session('message') }}</span>
                    </div>
                </div>
            @endif

            {{-- 3. Responsive Card Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 px-4 lg:px-0">
                @forelse ($apointed_subjects as $key => $data)
                    <div class="group relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2.5rem] p-6 transition-all duration-300 hover:shadow-2xl hover:shadow-slate-200/60 dark:hover:shadow-indigo-500/10 hover:-translate-y-1.5 flex flex-col">
                        
                        {{-- ID & Badge Row --}}
                        <div class="flex justify-between items-start mb-6">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 font-black text-xs ring-1 ring-indigo-100 dark:ring-indigo-800">
                                    {{ $apointed_subjects->firstItem() + $key }}
                                </div>
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('Code') }}</p>
                                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $data->a_subject_id }}</p>
                                </div>
                            </div>
                            
                            <flux:badge size="sm" variant="pill" color="{{ $data->active_status ? 'green' : 'red' }}" class="!font-black ring-1 ring-inset shadow-sm">
                                {{ $data->active_status ? __('Active') : __('Inactive') }}
                            </flux:badge>
                        </div>

                        {{-- Language Content --}}
                        <div class="space-y-4 mb-8">
                            <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-transparent group-hover:border-indigo-100 dark:group-hover:border-indigo-900/30 transition-colors duration-300">
                                <span class="block text-[10px] font-bold uppercase text-slate-400 mb-1 tracking-wider">{{ __('English Name') }}</span>
                                <span class="text-sm font-bold text-slate-800 dark:text-slate-100">{{ $data->name_en }}</span>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-3">
                                <div class="p-3 border border-slate-100 dark:border-slate-800 rounded-2xl bg-white/50 dark:bg-slate-900/50">
                                    <span class="block text-[10px] font-bold uppercase text-slate-400 mb-1 tracking-wider">{{ __('Sinhala') }}</span>
                                    <span class="text-xs font-medium text-slate-600 dark:text-slate-300 line-clamp-1">{{ $data->name_si }}</span>
                                </div>
                                <div class="p-3 border border-slate-100 dark:border-slate-800 rounded-2xl bg-white/50 dark:bg-slate-900/50">
                                    <span class="block text-[10px] font-bold uppercase text-slate-400 mb-1 tracking-wider">{{ __('Tamil') }}</span>
                                    <span class="text-xs font-medium text-slate-600 dark:text-slate-300 line-clamp-1">{{ $data->name_ta }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Action Footer --}}
                        <div class="flex items-center gap-2 mt-auto">
                            <flux:button 
                                wire:click="editApointedSubject({{ $data->id }})" 
                                class="flex-1 !rounded-2xl !py-3 font-bold shadow-sm" 
                                variant="filled" 
                                icon="pencil-square">
                                {{ __('Edit') }}
                            </flux:button>
                            
                            <flux:button 
                                wire:click="toggleStatus({{ $data->id }})" 
                                wire:confirm="{{ __('Are you sure you want to change the status of this subject?') }}"
                                class="!rounded-2xl !py-3 px-4 shadow-sm" 
                                variant="ghost" 
                                color="{{ $data->active_status ? 'red' : 'green' }}"
                                icon="{{ $data->active_status ? 'no-symbol' : 'check' }}" 
                            />
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 bg-slate-50/50 dark:bg-slate-800/10 rounded-[3rem] border-2 border-dashed border-slate-200 dark:border-slate-800 flex flex-col items-center justify-center text-center">
                        <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-6">
                            <flux:icon.document-magnifying-glass class="text-slate-400" size="xl" />
                        </div>
                        <flux:heading size="lg" class="!font-black text-slate-900 dark:text-white">{{ __('No Subjects Found') }}</flux:heading>
                        <flux:text class="max-w-xs mx-auto">{{ __('Your subject registry is currently empty. Get started by adding your first subject.') }}</flux:text>
                    </div>
                @endforelse
            </div>

            {{-- 4. Pagination --}}
            <div class="mt-12 px-4">
                {{ $apointed_subjects->links() }}
            </div>
        </div>

        {{-- MODAL: ADD NEW --}}
        <flux:modal wire:model="showModelNewApointedSubject" name="add-new-apointed-subject" class="w-full max-w-lg rounded-[2.5rem] p-8">
            <div class="space-y-6">
                <div class="text-center md:text-left">
                    <flux:heading size="lg" class="!font-black tracking-tight">{{ __('Add New Subject') }}</flux:heading>
                    <flux:text>{{ __('Populate the fields below to register a new subject.') }}</flux:text>
                </div>

                <form wire:submit.prevent="addNewApointedSubject" class="space-y-5">
                    <flux:field>
                        <flux:input label="Apointed Subject ID" wire:model.live="apointed_subjects_id" 
                            class="!rounded-xl shadow-sm" placeholder="ASUB0001" mask="ASUB9999"/>
                    </flux:field>

                    <flux:field>
                        <flux:input label="Name (English)" wire:model.live="apointed_subjects_name_en" class="!rounded-xl shadow-sm" />
                    </flux:field>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:input label="Name (Sinhala)" wire:model.live="apointed_subjects_name_si" class="!rounded-xl shadow-sm" />
                        </flux:field>
                        <flux:field>
                            <flux:input label="Name (Tamil)" wire:model.live="apointed_subjects_name_ta" class="!rounded-xl shadow-sm" />
                        </flux:field>
                    </div>

                    <div class="flex flex-col-reverse md:flex-row gap-3 pt-6">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-xl">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-xl px-6 shadow-md shadow-indigo-500/20">{{ __('Register Subject') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- MODAL: EDIT --}}
        <flux:modal wire:model="showModelEditApointedSubject" name="edit-apointed-subject" class="w-full max-w-lg rounded-[2.5rem] p-8">
            <div class="space-y-6">
                <div class="text-center md:text-left">
                    <flux:heading size="lg" class="!font-black tracking-tight">{{ __('Edit Subject Details') }}</flux:heading>
                    <flux:text>{{ __('Modify the translations or internal code for this entry.') }}</flux:text>
                </div>

                <form wire:submit.prevent="updateApointedSubject" class="space-y-5">
                    <flux:field>
                        <flux:input disabled label="Apointed Subject ID" wire:model.live="update_apointed_subjects_id" 
                            class="!rounded-xl bg-slate-50 dark:bg-slate-800 border-dashed shadow-none" mask="ASUB9999"/>
                    </flux:field>

                    <flux:field>
                        <flux:input label="Name (English)" wire:model.live="update_apointed_subjects_name_en" class="!rounded-xl shadow-sm" />
                    </flux:field>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:input label="Name (Sinhala)" wire:model.live="update_apointed_subjects_name_si" class="!rounded-xl shadow-sm" />
                        </flux:field>
                        <flux:field>
                            <flux:input label="Name (Tamil)" wire:model.live="update_apointed_subjects_name_ta" class="!rounded-xl shadow-sm" />
                        </flux:field>
                    </div>

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