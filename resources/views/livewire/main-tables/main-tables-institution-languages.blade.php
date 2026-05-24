<section class="w-full">

    <x-main-tables.layout>
        <div class="max-w-[1400px] mx-auto pb-12 px-4 lg:px-0">
            
            {{-- Section Header & Action Bar --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
                <div class="space-y-1">
                    <flux:heading size="xl" level="1" class="!font-black tracking-tight text-indigo-600 dark:text-indigo-400 uppercase">
                        {{ __('Institution Languages') }}
                    </flux:heading>
                    <flux:subheading size="lg" class="flex items-center gap-2">
                        <flux:icon.language variant="micro" class="text-slate-500" />
                        {{ __('Configure and monitor available languages for institutional communication') }}
                    </flux:subheading>
                </div>

                <div class="flex items-center gap-3">
                    <flux:modal.trigger name="add-new-institution-language">
                        <flux:button icon="plus" color="primary" class="w-full sm:w-auto !rounded-2xl shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all">
                            {{ __('Add New Language') }}
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
                @forelse ($institutionlanguages as $key => $data)
                    <div class="group relative flex flex-col bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-[2.5rem] p-6 hover:shadow-2xl hover:shadow-indigo-500/10 hover:border-indigo-500/30 transition-all duration-500">
                        
                        {{-- Card Top Row: ID & Status --}}
                        <div class="flex justify-between items-start mb-6">
                            <span class="text-[10px] font-black uppercase tracking-widest px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-500 rounded-full border border-slate-300 dark:border-slate-700">
                                {{ $data->language_id }}
                            </span>
                            
                            <div class="flex items-center gap-1.5">
                                <span class="text-[10px] font-bold uppercase tracking-tighter {{ $data->active_status ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $data->active_status ? 'Active' : 'Inactive' }}
                                </span>
                                <div class="h-1.5 w-1.5 rounded-full {{ $data->active_status ? 'bg-emerald-500' : 'bg-rose-500' }}"></div>
                            </div>
                        </div>

                        {{-- Card Content --}}
                        <div class="mb-8 flex flex-col items-center text-center">
                            <div class="mb-4 w-16 h-16 rounded-3xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-500">
                                <span class="text-2xl font-black text-indigo-600 dark:text-indigo-400">
                                    {{ substr($data->name, 0, 2) }}
                                </span>
                            </div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-white leading-tight uppercase tracking-tight group-hover:text-indigo-600 transition-colors">
                                {{ $data->name }}
                            </h3>
                        </div>

                        {{-- Card Footer/Actions --}}
                        <div class="mt-auto pt-4 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between">
                             <div class="flex items-center gap-2">
                                <flux:icon.hashtag variant="micro" class="text-slate-300" />
                                <span class="text-xs font-bold text-slate-500">Index #{{ $institutionlanguages->firstItem() + $key }}</span>
                             </div>
                             
                             <div class="flex items-center gap-1">
                                <flux:modal.trigger wire:click="editInstitutionLanguage({{ $data->id }})" name="edit-institution-language">
                                    <flux:button size="sm" variant="ghost" icon="pencil-square" class="!rounded-xl hover:!bg-indigo-50 hover:!text-indigo-600 transition-colors" />
                                </flux:modal.trigger>
                                
                                <flux:button 
                                    wire:click="toggleStatus({{ $data->id }})"
                                    wire:confirm="Update status for {{ $data->name }}?"
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
                        <flux:icon.language size="xl" class="text-slate-300 mb-4" />
                        <p class="text-slate-500 font-black uppercase tracking-widest text-sm italic">{{ __('No Languages Configured') }}</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination Container --}}
            <div class="mt-12 px-4">
                {{ $institutionlanguages->links() }}
            </div>
        </div>

        {{-- MODAL: ADD NEW --}}
        <flux:modal wire:model="showModelNewInstitutionLanguage" name="add-new-institution-language" class="w-full max-w-lg rounded-[2.5rem] p-10">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                        <flux:icon.plus />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black uppercase tracking-tight leading-none">{{ __('New Language') }}</flux:heading>
                        <flux:text size="sm">{{ __('Register a new language for institutional records.') }}</flux:text>
                    </div>
                </div>

                @if (session()->has('error'))
                    <div class="p-3 rounded-xl bg-rose-50 border border-rose-100 text-rose-800 text-xs font-bold">
                        {{ session('error') }}
                    </div>
                @endif

                <form wire:submit.prevent="addNewInstitutionLanguage" class="space-y-4 pt-2">
                    @csrf
                    <flux:field>
                        <flux:input label="Language ID" wire:model.live="institutionLanguageId" mask="SLID99" placeholder="SLID01" class="!rounded-xl" />
                    </flux:field>

                    <flux:field>
                        <flux:input label="Language Name" wire:model.live="institutionLanguage" placeholder="e.g. English, French" class="!rounded-xl" />
                    </flux:field>

                    <div class="flex gap-3 pt-6">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-xl">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-xl shadow-lg shadow-indigo-500/20">{{ __('Create Language') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- MODAL: EDIT --}}
        <flux:modal wire:model="showModelEditInstitutionLanguage" name="edit-institution-language" class="w-full max-w-lg rounded-[2.5rem] p-10">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white">
                        <flux:icon.pencil-square />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black uppercase tracking-tight leading-none">{{ __('Edit Language') }}</flux:heading>
                        <flux:text size="sm">{{ __('Modify existing language identification and properties.') }}</flux:text>
                    </div>
                </div>

                <form wire:submit.prevent="updateInstitutionLanguageList" class="space-y-4 pt-2">
                    @csrf
                    <flux:field>
                        <flux:input disabled label="Language ID" wire:model.live="updateInstitutionLanguageId" mask="SLID99" class="!rounded-xl bg-slate-50 opacity-60" />
                    </flux:field>

                    <flux:field>
                        <flux:input label="Language Name" wire:model.live="updateInstitutionLanguage" class="!rounded-xl" />
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