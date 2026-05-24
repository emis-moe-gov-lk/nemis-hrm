<div class="space-y-8">
    {{-- Section Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 px-1">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 flex items-center justify-center bg-indigo-50 dark:bg-indigo-900/20 rounded-xl shrink-0">
                <flux:icon.academic-cap class="size-5 text-indigo-600 dark:text-indigo-400" />
            </div>
            <div>
                <h2 class="text-base font-black tracking-widest text-slate-700 dark:text-zinc-200 uppercase">Teaching Information</h2>
                <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-[0.2em] mt-0.5">Academic subjects & professional category</p>
            </div>
        </div>

        <div class="flex items-center justify-between sm:justify-end gap-3 border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-200 dark:border-zinc-700">
            @can('teacher.profile.employment.teacher-information.update')
            <flux:modal.trigger name="teacher-data-edit">
                <flux:button variant="ghost" size="sm" class="rounded-xl border border-slate-300 dark:border-zinc-700 font-bold text-xs px-4 text-slate-600 dark:text-zinc-300 hover:border-indigo-400 hover:text-indigo-600 transition-all">Edit Details</flux:button>
            </flux:modal.trigger>
            @endcan
        </div>
    </div>

    {{-- Data Table --}}
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-300 dark:border-zinc-700 overflow-hidden">

        {{-- Teacher Category --}}
        <div class="flex flex-col sm:flex-row sm:items-center border-b border-dashed border-slate-300 dark:border-zinc-700 px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors gap-1 sm:gap-0">
            <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Teacher Category</span>
            <span class="text-sm font-semibold text-slate-800 dark:text-zinc-100 uppercase">{{ $teacherData->teacherCategory->name ?? 'N/A' }}</span>
        </div>

        {{-- Appointment Type --}}
        <div class="flex flex-col sm:flex-row sm:items-center border-b border-dashed border-slate-300 dark:border-zinc-700 px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors gap-1 sm:gap-0">
            <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Appointment Type</span>
            <span class="text-sm font-semibold text-slate-800 dark:text-zinc-100 uppercase">{{ $teacherData->teacherType->type_name ?? 'N/A' }}</span>
        </div>

        {{-- Medium --}}
        <div class="flex flex-col sm:flex-row sm:items-center border-b border-dashed border-slate-300 dark:border-zinc-700 px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors gap-1 sm:gap-0">
            <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Instruction Medium</span>
            <span class="text-sm font-semibold text-slate-800 dark:text-zinc-100 uppercase">{{ $teacherData->medium->name ?? 'N/A' }}</span>
        </div>

        {{-- Appointment Subject --}}
        <div class="flex flex-col sm:flex-row sm:items-center border-b border-dashed border-slate-300 dark:border-zinc-700 px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors gap-1 sm:gap-0">
            <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Appointment Subject</span>
            <span class="text-sm font-semibold text-slate-800 dark:text-zinc-100 uppercase">{{ $teacherData->appointmentSubject->name_en ?? 'N/A' }}</span>
        </div>

        {{-- Main Teaching Subject --}}
        <div class="flex flex-col sm:flex-row sm:items-center border-b border-dashed border-slate-300 dark:border-zinc-700 px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors gap-1 sm:gap-0">
            <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Main Subject</span>
            <span class="text-sm font-semibold text-slate-800 dark:text-zinc-100 uppercase">{{ $teacherData->mainSubject->name_en ?? 'N/A' }}</span>
        </div>

        {{-- Secondary Teaching Subject --}}
        <div class="flex flex-col sm:flex-row sm:items-center border-b border-slate-300 dark:border-zinc-700 px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors gap-1 sm:gap-0">
            <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Secondary Subject</span>
            <span class="text-sm font-semibold text-slate-800 dark:text-zinc-100 uppercase">{{ $teacherData->secondarySubject->name_en ?? 'N/A' }}</span>
        </div>

        {{-- Current Teaching Subject Footer --}}
        <div class="flex flex-col sm:flex-row sm:items-center px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors gap-1 sm:gap-0 bg-slate-50/50 dark:bg-zinc-800/30">
            <div class="w-full sm:w-48 sm:shrink-0 flex items-center gap-2">
                <div class="size-1.5 bg-emerald-500 rounded-full animate-pulse"></div>
                <span class="text-[11px] font-black text-slate-600 dark:text-zinc-400 uppercase tracking-widest">Currently Assigned</span>
            </div>
            <span class="text-sm font-bold text-slate-900 dark:text-white uppercase">{{ $teacherData->currentTeachingSubject->name_en ?? 'N/A' }}</span>
        </div>

    </div>

    {{-- Modal logic --}}
    @can('teacher.profile.employment.teacher-information.update')
    <flux:modal name="teacher-data-edit" class="md:w-150">
        <div class="space-y-6">
            <div class="pb-4 border-b border-slate-200 dark:border-zinc-700">
                <h3 class="text-sm font-black tracking-widest text-slate-900 dark:text-white uppercase">Update Teaching Data</h3>
                <p class="text-[10px] font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest mt-0.5">Ensure your subject specializations are accurate</p>
            </div>

            <form wire:submit.prevent="save" class="space-y-6">
                @csrf
                <flux:select label="Teacher Type" wire:model.live="teacherType">
                    <option value="">Select</option>
                    @foreach ($teacherTypeOptions as $teacherType)
                    <option value="{{ $teacherType->teacher_types_id }}">{{ $teacherType->type_name }}</option>
                    @endforeach
                </flux:select>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:select label="Teacher Category" wire:model.live="teacherCategory">
                        <option value="">Select</option>
                        @foreach ($teacherCategoryOptions as $teacherCategory)
                        <option value="{{ $teacherCategory->categories_id }}">{{ $teacherCategory->name }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select label="Medium of instruction" wire:model.live="appointmentMedium">
                        <option value="">Select</option>
                        @foreach ($mediumOptions as $medium)
                        <option value="{{ $medium->medium_id }}">{{ $medium->name }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <flux:select label="Appointment Subject" wire:model.live="appointmentSubject">
                    <option value="">Select</option>
                    @foreach ($appointmentSubjectOptions as $appointmentSubject)
                    <option value="{{ $appointmentSubject->a_subject_id }}">{{ $appointmentSubject->name_en }}</option>
                    @endforeach
                </flux:select>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:select label="Main Subject" wire:model.live="mainSubject">
                        <option value="">Select</option>
                        @foreach ($subjectOptions as $subject)
                        <option value="{{ $subject->subject_id }}">{{ $subject->name_en }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select label="Secondary Subject (Optional)" wire:model.live="secondarySubject">
                        <option value="">Select</option>
                        @foreach ($subjectOptions as $subject)
                        <option value="{{ $subject->subject_id }}">{{ $subject->name_en }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <flux:select label="Current or most Teaching Subject" wire:model.live="currentTeachingSubject">
                    <option value="">Select</option>
                    @foreach ($subjectOptions as $subject)
                    <option value="{{ $subject->subject_id }}">{{ $subject->name_en }}</option>
                    @endforeach
                </flux:select>

                <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-slate-200 dark:border-zinc-700">
                    <flux:button type="button" variant="ghost" wire:click="resetFields" class="font-bold rounded-xl h-11">Reset</flux:button>
                    <flux:button type="submit" variant="primary" class="font-black rounded-xl h-11 bg-indigo-600 dark:bg-white text-white dark:text-slate-900">Save changes</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
    @endcan
</div>