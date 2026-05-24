<div class="space-y-8">
    <section>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
                <h2 class="text-base font-black tracking-widest text-slate-700 dark:text-zinc-200 uppercase">Current Appointment</h2>
                <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-[0.2em] mt-0.5">Official Entry & Service Details</p>
            </div>
            @if($canEdit)
            <flux:modal.trigger name="employment-edit">
                <flux:button variant="ghost" size="sm" class="rounded-xl border border-slate-300 dark:border-zinc-700 font-bold text-xs px-5 text-slate-600 dark:text-zinc-300 hover:border-emerald-400 hover:text-emerald-600 transition-all w-fit">Edit Details</flux:button>
            </flux:modal.trigger>
            @endif
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-300 dark:border-zinc-700 overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-start border-b border-dashed border-slate-300 dark:border-zinc-700 px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors gap-1 sm:gap-0">
                <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest sm:pt-0.5">Initial Workplace</span>
                <div>
                    @if ($employee->appointment)
                    <div class="flex flex-wrap items-center gap-2">
                        @if ($employee->appointment->workplace?->office()?->census_no)
                        <span class="font-mono text-[10px] bg-slate-100 dark:bg-zinc-700 px-1.5 py-0.5 rounded text-slate-500">{{ $employee->appointment->workplace->office()->census_no }}</span>
                        @endif
                        <span class="text-sm font-semibold text-slate-800 dark:text-zinc-100">{{ $employee->appointment->workplace?->office()?->name ?? 'N/A' }}</span>
                    </div>
                    @if($employee->appointment->workplace?->office()?->address ?? null)
                    <p class="text-[11px] text-slate-500 mt-1">{{ $employee->appointment->workplace->office()->address }}</p>
                    @endif
                    @else
                    <span class="text-sm font-medium text-slate-500 italic">No record found</span>
                    @endif
                </div>
            </div>
            @if ($employee->appointment)
            <div class="flex flex-col sm:flex-row sm:items-center border-b border-dashed border-slate-300 dark:border-zinc-700 px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors gap-1 sm:gap-0">
                <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Service Branch</span>
                <span class="text-sm font-semibold text-slate-800 dark:text-zinc-100">{{ $employee->appointment->service->service_name ?? 'N/A' }}</span>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center border-b border-dashed border-slate-300 dark:border-zinc-700 px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors gap-1 sm:gap-0">
                <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Entry Grade</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[10px] font-black bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 uppercase tracking-widest w-fit">{{ $employee->appointment->rank->rank_name ?? 'N/A' }}</span>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center border-b border-dashed border-slate-300 dark:border-zinc-700 px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors gap-1 sm:gap-0">
                <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Position</span>
                <span class="text-sm font-semibold text-slate-800 dark:text-zinc-100">{{ $employee->appointment->position->position_name ?? 'N/A' }}</span>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center border-b border-dashed border-slate-300 dark:border-zinc-700 px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors gap-1 sm:gap-0">
                <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Appointed On</span>
                <span class="text-sm font-mono font-semibold text-slate-800 dark:text-zinc-100">{{ $employee->appointment->first_appointment_date ? $employee->appointment->first_appointment_date->format('Y-m-d') : 'N/A' }}</span>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center border-b border-dashed border-slate-300 dark:border-zinc-700 px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors gap-1 sm:gap-0">
                <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Letter Reference</span>
                <span class="text-sm font-mono font-semibold text-slate-600 dark:text-zinc-300">{{ $employee->appointment->appointment_letter_no ?? '—' }}</span>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors gap-1 sm:gap-0">
                <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">System Entry</span>
                <span class="text-sm font-mono text-slate-500 dark:text-zinc-400">{{ $employee->appointment->created_at->format('Y-m-d') }}</span>
            </div>
            @endif
        </div>
    </section>

    @if($canEdit)
    <flux:modal name="employment-edit" class="w-full max-w-lg">
        <div class="space-y-6 max-h-[85vh] overflow-y-auto pr-1">
            <div>
                <h3 class="text-sm font-black tracking-widest text-slate-900 dark:text-white uppercase">Entry Record</h3>
                <p class="text-[10px] font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest mt-0.5">Update initial appointment details</p>
            </div>
            <form wire:submit.prevent="save" class="space-y-5">
                <div class="space-y-6 px-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <flux:input type="date" label="Appointment Date" wire:model.live="appointmentDate" />
                        <flux:input label="Appointment No" wire:model.live="appointmentLetterNo" />
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <flux:select label="Service Rank" wire:model.live="serviceRank">
                            <option value="">Select Rank</option>
                            @foreach ($ranksOptions as $rank)
                            <option value="{{ $rank->rank_id }}">{{ $rank->rank_name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:select label="Designation" wire:model.live="position">
                            <option value="">Select Position</option>
                            @foreach ($positionOption as $data)
                            <option value="{{ $data->position_id }}">{{ $data->position_name }}</option>
                            @endforeach
                        </flux:select>
                    </div>
                </div>
                <div class="p-4 bg-slate-50 dark:bg-zinc-800/50 rounded-2xl border border-slate-200 dark:border-zinc-700 space-y-4">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Initial Placement</p>
                    <flux:select label="Workplace Level" wire:model.live="officeLevel">
                        <option value="">Select Level</option>
                        @foreach ($officeLevelOption as $level)
                        <option value="{{ $level->office_level_id }}">{{ $level->office_level_name }}</option>
                        @endforeach
                    </flux:select>
                    @if ($officeLevel == 'OLID006')
                    <flux:select label="Zonal Education Office" wire:model.live="zonalEducationOffice">
                        <option value="">Select</option>
                        @foreach ($zonalEducationOfficeOption as $zone)
                        <option value="{{ $zone->workplace_id }}">{{ $zone->short_name }}</option>
                        @endforeach
                    </flux:select>
                    <flux:select label="Institution Category" wire:model.live="institutionCategory">
                        <option value="">Select</option>
                        @foreach ($institutionCategoryOption as $data)
                        <option value="{{ $data->institution_category_id }}">{{ $data->institution_category_name }}</option>
                        @endforeach
                    </flux:select>
                    @endif
                    <flux:select label="Working Place" wire:model.live="workingPlace">
                        <option value="">Select Workplace</option>
                        @foreach ($workingPlaceOption as $office)
                        <option value="{{ $office->workplace_id }}">@if ($officeLevel == 'OLID006' && $office->institution) {{'['. str_pad((string)$office->institution->census_no, 5, "0", STR_PAD_LEFT) .']' }} @endif {{ $office->office_name }}</option>
                        @endforeach
                    </flux:select>
                </div>
                <div class="flex gap-3 pt-2">
                    <flux:button type="button" variant="ghost" wire:click="resetFields" class="flex-1 font-bold rounded-xl h-12">Reset</flux:button>
                    <flux:button type="submit" variant="primary" class="flex-2 font-black rounded-xl h-12 bg-indigo-600 dark:bg-white text-white dark:text-slate-900">Save Appointment</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
    @endif
</div>