<div class="space-y-8">
    <section>
        {{-- Section Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
                <h2 class="text-base font-black tracking-widest text-slate-700 dark:text-zinc-200 uppercase">Educational Qualifications</h2>
                <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-[0.2em] mt-0.5">Academic Background & Certifications</p>
            </div>
            @if($canCreate)
            <flux:modal.trigger name="add-qualification">
                <flux:button variant="ghost" size="sm" icon="plus" class="rounded-xl border border-slate-300 dark:border-zinc-700 font-bold text-xs px-5 text-slate-600 dark:text-zinc-300 hover:border-indigo-400 hover:text-indigo-600 transition-all w-fit">
                    Add New
                </flux:button>
            </flux:modal.trigger>
            @endif
        </div>

        {{-- Data Table --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-300 dark:border-zinc-700 overflow-hidden">

            {{-- Table Header — desktop only --}}
            <div class="hidden sm:flex items-center bg-slate-50 dark:bg-zinc-800/50 border-b border-slate-300 dark:border-zinc-700 px-6 py-3">
                <span class="flex-1 text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Qualification</span>
                <span class="w-28 shrink-0 text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Completed</span>
                <span class="w-20 shrink-0 text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Grade</span>
                @if($canDelete)
                <span class="w-10 shrink-0"></span>
                @endif
            </div>

            {{-- Rows --}}
            @forelse ($qualificationList as $data)
            <div class="flex flex-col sm:flex-row items-start sm:items-center border-b border-dashed border-slate-300 dark:border-zinc-700 last:border-b-0 px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors group gap-3 sm:gap-0">
                <div class="flex-1 min-w-0 pr-4">
                    <span class="text-sm font-semibold text-slate-800 dark:text-zinc-100 leading-snug">{{ $data->qualification->qualification }}</span>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <flux:icon.building-library variant="micro" class="size-3 text-slate-300 shrink-0" />
                        <span class="text-[11px] font-semibold text-slate-500 dark:text-zinc-400 truncate">{{ $data->institution }}</span>
                    </div>
                    @if($data->description)
                    <p class="text-[10px] text-slate-500 mt-0.5 line-clamp-1">{{ $data->description }}</p>
                    @endif
                </div>

                {{-- Date + Grade inline on mobile, fixed columns on desktop --}}
                <div class="flex items-center gap-4 sm:contents">
                    <div class="sm:w-28 sm:shrink-0">
                        <span class="sm:hidden text-[9px] font-black text-slate-500 uppercase tracking-widest">Completed: </span>
                        <span class="text-sm font-semibold text-slate-800 dark:text-zinc-100">{{ \Carbon\Carbon::parse($data->effective_date)->format('M Y') }}</span>
                    </div>
                    <div class="sm:w-20 sm:shrink-0">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[10px] font-black bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 uppercase tracking-widest">
                            {{ $data->qualificationGrade->grade ?? 'N/A' }}
                        </span>
                    </div>
                </div>

                @if($canDelete)
                <div class="w-full sm:w-10 shrink-0 flex justify-end">
                    <flux:button
                        icon="trash"
                        variant="ghost"
                        size="sm"
                        wire:click="confirmDelete({{ $data->id }})"
                        class="sm:opacity-0 group-hover:opacity-100 transition-opacity text-slate-500 hover:text-rose-500 rounded-lg" />
                </div>
                @endif
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-16 px-6">
                <div class="w-16 h-16 rounded-2xl bg-slate-50 dark:bg-zinc-800 flex items-center justify-center mb-4">
                    <flux:icon.academic-cap class="size-8 text-slate-300 dark:text-zinc-600" />
                </div>
                <p class="text-sm font-semibold text-slate-500 uppercase tracking-widest">No qualifications listed yet</p>
                @if($canCreate)
                <p class="text-[10px] text-slate-300 dark:text-zinc-600 mt-1">Click "Add New" to add a qualification</p>
                @endif
            </div>
            @endforelse
        </div>
    </section>


    {{-- Add Modal --}}
    @if($canCreate)
    <flux:modal wire:model="showModal" name="add-qualification" class="md:w-160">
        <div class="space-y-6">
            <div class="pb-4 border-b border-slate-200 dark:border-zinc-700">
                <h3 class="text-sm font-black tracking-widest text-slate-900 dark:text-white uppercase">Add Qualification</h3>
                <p class="text-[10px] font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest mt-0.5">Academic background and certifications</p>
            </div>

            <form wire:submit.prevent="save" class="space-y-5">
                <flux:select wire:model.live="qualification" label="Qualification" icon="academic-cap">
                    <flux:select.option value="">Select Qualification</flux:select.option>
                    @foreach ($educationQualificationList as $data)
                    <flux:select.option value="{{ $data->qualifications_id }}">{{ $data->qualification }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input label="Institution / University" wire:model.live="institution" icon="building-library" placeholder="e.g. University of Colombo" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input label="Effective Date" wire:model.live="effectiveDate" type="date" />
                    <flux:select wire:model.live="grade" label="Grade / Result">
                        <flux:select.option value="">Select Grade</flux:select.option>
                        @foreach ($gradeOption as $key => $value)
                        <flux:select.option value="{{ $key }}">{{ $value }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>

                <flux:textarea rows="3" wire:model.live="description" label="Additional Details" placeholder="Major subjects, thesis title, or special awards..." />

                <div class="flex gap-3 pt-4">
                    <flux:modal.close>
                        <flux:button variant="ghost" class="flex-1 font-bold rounded-xl h-12">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary" class="flex-1 font-black rounded-xl h-12 bg-indigo-600 dark:bg-white text-white dark:text-slate-900">Save Qualification</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
    @endif

    <x-delete-confirmation 
        name="confirm-delete-qualification" 
        wireAction="delete" 
        model="showDeleteModal"
        title="Remove Qualification?" 
        description="Are you sure you want to remove this educational qualification? This will permanently delete the record from the employee profile."
    />
</div>