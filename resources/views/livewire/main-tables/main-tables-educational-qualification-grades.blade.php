<section class="w-full">

    <x-main-tables.layout>
        <div class="max-w-[1400px] mx-auto pb-12 px-4 lg:px-0">
            
            {{-- Section Header & Action Bar --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
                <div class="space-y-1">
                    <flux:heading size="xl" level="1" class="!font-black tracking-tight text-slate-900 dark:text-white uppercase">
                        {{ __('Qualification Grades') }}
                    </flux:heading>
                    <flux:subheading size="lg" class="flex items-center gap-2">
                        <flux:icon.bookmark-square variant="micro" class="text-indigo-500" />
                        {{ __('Define localized grade values and status categories') }}
                    </flux:subheading>
                </div>

                <div class="flex items-center gap-3">
                    <flux:modal.trigger name="add-new-edu-qualification-grade">
                        <flux:button icon="plus" color="primary" class="w-full md:w-auto !rounded-2xl shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all">
                            {{ __('Add New Grade') }}
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

            {{-- GRADE CARDS GRID --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
                @forelse ($eduQualificationGrade as $key => $data)
                    <div class="relative group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2.5rem] p-6 shadow-sm hover:shadow-xl hover:border-indigo-300 dark:hover:border-indigo-800 transition-all duration-300">
                        
                        {{-- Top Metadata --}}
                        <div class="flex justify-between items-center mb-5">
                            <span class="text-[10px] font-black text-slate-400 tabular-nums tracking-widest uppercase">
                                #{{ $eduQualificationGrade->firstItem() + $key }}
                            </span>
                            <flux:badge size="sm" variant="pill" color="{{ $data->active_status ? 'green' : 'red' }}" class="!font-black uppercase tracking-widest text-[9px]">
                                {{ $data->active_status ? 'Active' : 'Inactive' }}
                            </flux:badge>
                        </div>

                        {{-- Grade Display Section --}}
                        <div class="mb-6 flex items-baseline gap-3">
                            <h3 class="text-3xl font-black text-slate-900 dark:text-white leading-tight group-hover:text-indigo-600 transition-colors italic uppercase">
                                {{ $data->grade }}
                            </h3>
                        </div>

                        {{-- Details Chip --}}
                        <div class="flex items-center gap-3 p-4 bg-slate-50/50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 rounded-3xl mb-6">
                            <div class="w-8 h-8 rounded-xl bg-white dark:bg-slate-700 flex items-center justify-center text-indigo-500 shadow-sm">
                                <flux:icon.finger-print variant="micro" />
                            </div>
                            <div>
                                <p class="text-[9px] uppercase font-black text-slate-400 leading-none mb-1">{{ __('Reference ID') }}</p>
                                <p class="text-xs font-bold font-mono text-slate-700 dark:text-slate-300 tracking-tighter">{{ $data->grade_id }}</p>
                            </div>
                        </div>

                        {{-- Actions Footer --}}
                        <div class="flex gap-2 mt-auto">
                            <flux:modal.trigger wire:click="editEduQualificationGrade({{ $data->id }})" class="flex-1">
                                <flux:button size="sm" variant="ghost" icon="pencil-square" class="w-full !rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800">
                                    {{ __('Edit') }}
                                </flux:button>
                            </flux:modal.trigger>
                            
                            <flux:button 
                                wire:click="toggleStatus({{ $data->id }})"
                                wire:confirm="Are you sure you want to change status?"
                                size="sm" 
                                variant="filled" 
                                color="{{ $data->active_status ? 'red' : 'green' }}"
                                icon="{{ $data->active_status ? 'no-symbol' : 'check' }}"
                                class="!rounded-xl shadow-sm"
                            />
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-24 text-center">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-[2rem] bg-slate-50 dark:bg-slate-900 border-2 border-dashed border-slate-200 dark:border-slate-800 mb-4 text-slate-300">
                            <flux:icon.document-chart-bar size="xl" />
                        </div>
                        <h3 class="text-lg font-black text-slate-400 uppercase tracking-widest">{{ __('No Grades Found') }}</h3>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-12">
                {{ $eduQualificationGrade->links() }}
            </div>
        </div>

        {{-- MODAL: ADD NEW GRADE --}}
        <flux:modal wire:model="showModelNewEduQualificationGrade" name="add-new-edu-qualification-grade" class="w-full max-w-lg rounded-[2.5rem] p-8">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-indigo-50 dark:bg-indigo-900/30 rounded-[1.5rem] flex items-center justify-center text-indigo-600">
                        <flux:icon.plus />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black tracking-tight uppercase">{{ __('Create Grade') }}</flux:heading>
                        <flux:text>{{ __('Define a new qualification grade for the registry.') }}</flux:text>
                    </div>
                </div>

                @if (session()->has('error'))
                    <div class="p-4 rounded-2xl bg-red-50 border border-red-100 text-red-800 text-xs font-bold">
                        {{ session('error') }}
                    </div>
                @endif

                <form wire:submit.prevent="addNewEduQualificationGrade" class="space-y-6">
                    @csrf
                    <div class="p-6 bg-slate-50 dark:bg-slate-800/50 rounded-[2rem] border border-slate-100 dark:border-slate-800 space-y-5">
                        <flux:field>
                            <flux:input label="Grade ID" wire:model.live="gradeId" mask="GRD999" placeholder="GRD000" class="!rounded-xl" />
                        </flux:field>

                        <flux:field>
                            <flux:input label="Grade Title" wire:model.live="grade" placeholder="e.g. Distinction, Grade A, Pass" class="!rounded-xl" />
                        </flux:field>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-xl">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-xl shadow-lg shadow-indigo-500/20">{{ __('Add Grade') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- MODAL: EDIT GRADE --}}
        <flux:modal wire:model="showModelEditEduQualificationGrade" name="edit-edu-qualification-grade" class="w-full max-w-lg rounded-[2.5rem] p-8">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-indigo-50 dark:bg-indigo-900/30 rounded-[1.5rem] flex items-center justify-center text-indigo-600">
                        <flux:icon.pencil-square />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black tracking-tight uppercase">{{ __('Edit Grade') }}</flux:heading>
                        <flux:text>{{ __('Update the selected grading information.') }}</flux:text>
                    </div>
                </div>

                <form wire:submit.prevent="updateEduQualificationGrade" class="space-y-6">
                    @csrf
                    <div class="p-6 bg-slate-50 dark:bg-slate-800/50 rounded-[2rem] border border-slate-100 dark:border-slate-800 space-y-5">
                        <flux:field>
                            <flux:input disabled label="Grade ID (Static)" wire:model.live="updateGradeId" mask="GRD999" class="!rounded-xl bg-slate-100/50 dark:bg-slate-900/50" />
                        </flux:field>

                        <flux:field>
                            <flux:input label="Grade Title" wire:model.live="updateGrade" class="!rounded-xl" />
                        </flux:field>
                    </div>

                    <div class="flex gap-3 pt-4">
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