<div class="space-y-6">
    {{-- Section Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 px-1">
        <div class="flex items-center gap-3">
            <div class="p-2.5 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl">
                <flux:icon.academic-cap class="size-5 text-indigo-600 dark:text-indigo-400" />
            </div>
            <div>
                <h2 class="text-lg sm:text-xl font-extrabold tracking-tight text-gray-900 dark:text-white leading-tight">Teaching Information</h2>
                <p class="text-xs sm:text-sm text-gray-500 italic">Academic subjects and professional category</p>
            </div>
        </div>

        <div class="flex items-center justify-between sm:justify-end gap-2 border-t sm:border-t-0 pt-3 sm:pt-0 border-gray-100 dark:border-gray-800">
            <flux:badge variant="pill" color="indigo" class="text-[10px] sm:text-xs font-bold uppercase">
                {{ $employee->appointment->service->service_name ?? 'N/A' }}
            </flux:badge>
            
            @can('teacher.profile.employment.teacher-information.update')
                <flux:modal.trigger name="teacher-data-edit">
                    <flux:button variant="ghost" icon="pencil-square" size="sm" class="rounded-full">Edit</flux:button>
                </flux:modal.trigger>
            @endcan
        </div>
    </div>

    {{-- Info Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        
        {{-- Teacher Category --}}
        <div class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm">
            <div class="p-2 bg-gray-50 dark:bg-gray-900 rounded-lg">
                <flux:icon.user-group class="size-4 text-gray-400" />
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Teacher Category</p>
                <p class="text-sm font-bold text-gray-900 dark:text-white uppercase">{{ $teacherData->teacherCategory->name ?? 'N/A' }}</p>
            </div>
        </div>

        {{-- Appointment Type --}}
        <div class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm">
            <div class="p-2 bg-gray-50 dark:bg-gray-900 rounded-lg">
                <flux:icon.identification class="size-4 text-gray-400" />
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Appointment Type</p>
                <p class="text-sm font-bold text-gray-900 dark:text-white uppercase">{{ $teacherData->teacherType->type_name ?? 'N/A' }}</p>
            </div>
        </div>

        {{-- Medium --}}
        <div class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm">
            <div class="p-2 bg-gray-50 dark:bg-gray-900 rounded-lg">
                <flux:icon.language class="size-4 text-gray-400" />
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Instruction Medium</p>
                <p class="text-sm font-bold text-gray-900 dark:text-white uppercase">{{ $teacherData->medium->name ?? 'N/A' }}</p>
            </div>
        </div>

        {{-- Appointment Subject --}}
        <div class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm md:col-span-1">
            <div class="p-2 bg-gray-50 dark:bg-gray-900 rounded-lg">
                <flux:icon.bookmark-square class="size-4 text-gray-400" />
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Appointment Subject</p>
                <p class="text-sm font-bold text-gray-900 dark:text-white uppercase">{{ $teacherData->appointmentSubject->name_en ?? 'N/A' }}</p>
            </div>
        </div>

        {{-- Main Teaching Subject --}}
        <div class="flex items-center gap-4 p-4 bg-indigo-50/50 dark:bg-indigo-900/10 border border-indigo-100 dark:border-indigo-900/30 rounded-2xl shadow-sm md:col-span-2 lg:col-span-2">
            <div class="p-2 bg-indigo-100 dark:bg-indigo-900/40 rounded-lg">
                <flux:icon.book-open class="size-4 text-indigo-600 dark:text-indigo-400" />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 w-full">
                <div>
                    <p class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider">Main Teaching Subject</p>
                    <p class="text-sm font-bold text-gray-900 dark:text-white uppercase">{{ $teacherData->mainSubject->name_en ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider">Secondary Subject</p>
                    <p class="text-sm font-bold text-gray-900 dark:text-white uppercase">{{ $teacherData->secondarySubject->name_en ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Current Teaching Subject Footer Card --}}
    <div class="p-4 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 rounded-2xl flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="size-2 bg-green-500 rounded-full animate-pulse"></div>
            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400">
                Currently assigned by school: <span class="text-gray-900 dark:text-white uppercase ml-1">{{ $teacherData->currentTeachingSubject->name_en ?? 'N/A' }}</span>
            </p>
        </div>
    </div>

    {{-- Modal logic --}}
    @can('teacher.profile.employment.teacher-information.update')
    <flux:modal name="teacher-data-edit" class="md:w-150">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Update Teacher Data</flux:heading>
                <flux:text class="mt-2">Ensure your subject specializations are accurate.</flux:text>
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

                    <flux:select label="Medium of instruction" wire:model.live="medium">
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

                <div class="flex pt-4">
                    <flux:button type="button" variant="ghost" wire:click="resetFields">Reset</flux:button>
                    <flux:spacer />
                    <flux:button type="submit" variant="primary">Save changes</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
    @endcan
</div>