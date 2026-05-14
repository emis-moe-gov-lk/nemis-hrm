<section class="w-full">

    <x-main-tables.layout>
        <div class="max-w-[1400px] mx-auto pb-12 px-4 lg:px-0">
            
            {{-- Section Header & Action Bar --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
                <div class="space-y-1">
                    <flux:heading size="xl" level="1" class="!font-black tracking-tight text-indigo-600 dark:text-indigo-400 uppercase">
                        {{ __('Grade Spans') }}
                    </flux:heading>
                    <flux:subheading size="lg" class="flex items-center gap-2">
                        <flux:icon.academic-cap variant="micro" class="text-slate-400" />
                        {{ __('Define and monitor educational level ranges within the institution') }}
                    </flux:subheading>
                </div>

                <div class="flex items-center gap-3">
                    <flux:modal.trigger name="add-new-ins-grade-spans">
                        <flux:button icon="plus" color="primary" class="w-full sm:w-auto !rounded-2xl shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all">
                            {{ __('Add Grade Span') }}
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
                @forelse ($insgradespans as $key => $data)
                    <div class="group relative flex flex-col bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2.5rem] p-6 hover:shadow-2xl hover:shadow-indigo-500/10 hover:border-indigo-500/30 transition-all duration-500">
                        
                        {{-- Card Header: ID & Status --}}
                        <div class="flex justify-between items-start mb-6">
                            <span class="text-[10px] font-black uppercase tracking-widest px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 rounded-full border border-slate-200 dark:border-slate-700">
                                {{ $data->grade_span_id }}
                            </span>
                            
                            <div class="flex items-center gap-1.5">
                                <span class="text-[10px] font-bold uppercase tracking-tighter {{ $data->active_status ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $data->active_status ? 'Active' : 'Inactive' }}
                                </span>
                                <div class="h-1.5 w-1.5 rounded-full {{ $data->active_status ? 'bg-emerald-500' : 'bg-rose-500' }}"></div>
                            </div>
                        </div>

                        {{-- Card Body: Grade Span Info --}}
                        <div class="mb-8 flex-grow">
                            <h3 class="text-lg font-black text-slate-900 dark:text-white leading-tight uppercase tracking-tight group-hover:text-indigo-600 transition-colors mb-4">
                                {{ $data->grade_span_name }}
                            </h3>
                            
                            {{-- Visual Range Indicator --}}
                            <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-2xl border border-slate-100 dark:border-slate-800">
                                <div class="flex justify-between text-[10px] font-black uppercase text-slate-400 mb-2">
                                    <span>Start: {{ $data->start_grade }}</span>
                                    <span>End: {{ $data->end_grade }}</span>
                                </div>
                                <div class="relative w-full h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                    <div class="absolute left-0 top-0 h-full bg-indigo-500 rounded-full transition-all duration-700 w-full opacity-60 group-hover:opacity-100"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Card Footer --}}
                        <div class="mt-auto pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                             <div class="flex items-center gap-2">
                                <flux:icon.hashtag variant="micro" class="text-slate-300" />
                                <span class="text-xs font-bold text-slate-400">Order #{{ $insgradespans->firstItem() + $key }}</span>
                             </div>
                             <flux:icon.arrow-right variant="micro" class="text-slate-200 group-hover:text-indigo-400 group-hover:translate-x-1 transition-all" />
                        </div>

                        {{-- Hover Action Bar --}}
                        <div class="absolute inset-x-0 -bottom-2 flex justify-center opacity-0 group-hover:opacity-100 group-hover:bottom-4 transition-all duration-300 pointer-events-none group-hover:pointer-events-auto">
                            <div class="flex items-center gap-1 bg-white/95 dark:bg-slate-800/95 backdrop-blur-md border border-slate-200 dark:border-slate-700 p-1.5 rounded-2xl shadow-xl">
                                <flux:modal.trigger wire:click="editInsGradeSpans({{ $data->id }})" name="edit-ins-grade-spans">
                                    <flux:button size="sm" variant="ghost" icon="pencil-square" class="!rounded-xl hover:!bg-indigo-50 hover:!text-indigo-600 transition-colors" />
                                </flux:modal.trigger>
                                
                                <div class="w-px h-4 bg-slate-200 dark:bg-slate-700 mx-1"></div>
                                
                                <flux:button 
                                    wire:click="toggleStatus({{ $data->id }})"
                                    wire:confirm="Change status for {{ $data->grade_span_name }}?"
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
                        <flux:icon.document-magnifying-glass size="xl" class="text-slate-300 mb-4" />
                        <p class="text-slate-400 font-black uppercase tracking-widest text-sm italic">{{ __('No Grade Spans Found') }}</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-12 px-4">
                {{ $insgradespans->links() }}
            </div>
        </div>

        {{-- MODAL: ADD NEW --}}
        <flux:modal wire:model="showModelNewInsGradeSpans" name="add-new-ins-grade-spans" class="w-full max-w-lg rounded-[2.5rem] p-10">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                        <flux:icon.plus />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black uppercase tracking-tight leading-none">{{ __('New Grade Span') }}</flux:heading>
                        <flux:text size="sm">{{ __('Define a new range of grades for the institution.') }}</flux:text>
                    </div>
                </div>

                @if (session()->has('error'))
                    <div class="p-3 rounded-xl bg-rose-50 border border-rose-100 text-rose-800 text-xs font-bold">
                        {{ session('error') }}
                    </div>
                @endif

                <form wire:submit.prevent="addNewInsGradeSpans" class="space-y-4 pt-2">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <flux:field class="col-span-2">
                            <flux:input label="Grade Span ID" wire:model.live="grade_span_id" mask="GSID99" placeholder="GSID01" class="!rounded-xl" />
                        </flux:field>
                        <flux:field class="col-span-2">
                            <flux:input label="Span Name" wire:model.live="grade_span" placeholder="e.g. Primary, Secondary" class="!rounded-xl" />
                        </flux:field>
                        <flux:field>
                            <flux:input label="Start Grade" wire:model.live="start_grade" placeholder="0" class="!rounded-xl" />
                        </flux:field>
                        <flux:field>
                            <flux:input label="End Grade" wire:model.live="end_grade" placeholder="5" class="!rounded-xl" />
                        </flux:field>
                    </div>

                    <div class="flex gap-3 pt-6">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-xl">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-[2] !rounded-xl shadow-lg shadow-indigo-500/20">{{ __('Create Span') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- MODAL: EDIT --}}
        <flux:modal wire:model="showModelEditInsGradeSpans" name="edit-ins-grade-spans" class="w-full max-w-lg rounded-[2.5rem] p-10">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-amber-200">
                        <flux:icon.pencil-square />
                    </div>
                    <div>
                        <flux:heading size="lg" class="!font-black uppercase tracking-tight leading-none">{{ __('Edit Span') }}</flux:heading>
                        <flux:text size="sm">{{ __('Update existing grade range parameters.') }}</flux:text>
                    </div>
                </div>

                <form wire:submit.prevent="updateInsGradeSpansList" class="space-y-4 pt-2">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <flux:field class="col-span-2">
                            <flux:input disabled label="Grade Span ID" wire:model.live="updateGradeSpanId" mask="GSID99" class="!rounded-xl bg-slate-50 opacity-60" />
                        </flux:field>
                        <flux:field class="col-span-2">
                            <flux:input label="Span Name" wire:model.live="updateGradeSpan" class="!rounded-xl" />
                        </flux:field>
                        <flux:field>
                            <flux:input label="Start Grade" wire:model.live="updateStartGrade" class="!rounded-xl" />
                        </flux:field>
                        <flux:field>
                            <flux:input label="End Grade" wire:model.live="updateEndGrade" class="!rounded-xl" />
                        </flux:field>
                    </div>

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