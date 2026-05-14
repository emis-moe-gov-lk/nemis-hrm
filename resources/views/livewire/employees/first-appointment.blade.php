<div>
    <section>
        {{-- Section Header - Stacked on Mobile --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 px-1">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl">
                    <flux:icon.briefcase class="size-5 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div>
                    <h2 class="text-lg sm:text-xl font-extrabold tracking-tight text-gray-900 dark:text-white leading-tight">First Appointment</h2>
                    <p class="text-xs sm:text-sm text-gray-500">Official entry and service details</p>
                </div>
            </div>

            <div class="flex items-center justify-between sm:justify-end gap-2 border-t sm:border-t-0 pt-3 sm:pt-0 border-gray-100 dark:border-gray-800">
                <flux:badge variant="pill" icon="clock" color="emerald" class="text-[10px] sm:text-xs">
                    {{ $employee->appointment->service_years ?? '0' }} Active
                </flux:badge>
                @if($canEdit)
                    <flux:modal.trigger name="employment-edit">
                        <flux:button variant="ghost" icon="pencil-square" size="sm" class="rounded-full">Edit</flux:button>
                    </flux:modal.trigger>
                @endif
            </div>
        </div>

        <div class="space-y-4">
            {{-- Institutional Placement Card - Enhanced Depth --}}
            <div class="relative overflow-hidden bg-white dark:bg-gray-800 p-5 sm:p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-2">
                        <p class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Initial Workplace</p>
                        <div class="h-px flex-1 bg-emerald-50 dark:bg-emerald-900/20"></div>
                    </div>
                    
                    <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white leading-snug flex flex-wrap items-center gap-2">
                         @if ($employee->appointment?->workplace?->office()?->census_no)
                            <span class="font-mono text-[10px] bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded text-gray-500 border border-gray-200 dark:border-gray-600">
                                {{ $employee->appointment->workplace->office()->census_no }}
                            </span>
                        @endif
                        <span class="break-words">{{ $employee->appointment->workplace->office()->name ?? 'N/A' }}</span>
                    </h3>
                    
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 flex items-start gap-1.5 leading-relaxed">
                        <flux:icon.map-pin class="size-4 mt-0.5 flex-shrink-0 text-emerald-500/50" />
                        {{ $employee->appointment->workplace->office()->address ?? 'Address not specified' }}
                    </p>
                </div>
                
                {{-- Subtle Background Decorative Icon - Adjusted for mobile --}}
                <flux:icon.building-library class="absolute -right-6 -bottom-6 size-24 text-gray-50 dark:text-gray-700/20 -rotate-12 pointer-events-none" />
            </div>

            {{-- Service Data Grid - 2 Column Mobile Layout --}}
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
                <div class="p-3.5 bg-gray-50/50 dark:bg-gray-900/40 rounded-xl border border-gray-100 dark:border-gray-800 col-span-2 sm:col-span-1">
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1">Service Branch</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $employee->appointment->service->service_name }}</p>
                </div>

                <div class="p-3.5 bg-gray-50/50 dark:bg-gray-900/40 rounded-xl border border-gray-100 dark:border-gray-800">
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1">Entry Rank</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $employee->appointment->rank->rank_name }}</p>
                </div>

                <div class="p-3.5 bg-gray-50/50 dark:bg-gray-900/40 rounded-xl border border-gray-100 dark:border-gray-800">
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1">Position</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $employee->appointment->position->position_name ?? 'N/A' }}</p>
                </div>

                <div class="p-3.5 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700">
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1">Appointed On</p>
                    <p class="text-sm font-mono font-bold text-emerald-600 dark:text-emerald-400">
                        {{ $employee->appointment->first_appointment_date ? $employee->appointment->first_appointment_date->format('d M Y') : 'N/A' }}
                    </p>
                </div>

                <div class="p-3.5 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700">
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1">Reference</p>
                    <p class="text-sm font-mono font-bold text-gray-700 dark:text-gray-300 truncate">
                        {{ $employee->appointment->appointment_letter_no }}
                    </p>
                </div>

                <div class="p-3.5 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 col-span-2 lg:col-span-1">
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1">System Entry</p>
                    <p class="text-[11px] text-gray-500">
                        Created: {{ $employee->appointment->created_at->format('Y-m-d') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Update Modal - Full Width on Mobile --}}
    @if($canEdit)
        <flux:modal name="employment-edit" class="w-full max-w-lg">
            <div class="space-y-6 max-h-[85vh] overflow-y-auto pr-1">
                <div>
                    <flux:heading size="lg">Entry Record</flux:heading>
                    <flux:text class="text-sm">Update the initial appointment details for this employee.</flux:text>
                </div>

                <form wire:submit.prevent="save" class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <flux:input type="date" label="Appointment Date" wire:model.live="appointmentDate" />
                        <flux:input label="Appointment No" wire:model.live="appointmentLetterNo" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <flux:select label="Service" wire:model.live="service">
                            <option value="">Select Service</option>
                            @foreach ($userServicesOptions as $service)
                                <option value="{{ $service->service_id }}">{{ $service->service_name }}</option>
                            @endforeach
                        </flux:select>

                        <flux:select label="Service Rank" wire:model.live="serviceRank">
                            <option value="">Select Rank</option>
                            @foreach ($ranksOptions as $rank)
                                <option value="{{ $rank->rank_id }}">{{ $rank->rank_name }}</option>
                            @endforeach
                        </flux:select>
                    </div>

                    <flux:select label="Designation" wire:model.live="position">
                        <option value="">Select Position</option>
                        @foreach ($positionOption as $data)
                            <option value="{{ $data->position_id }}">{{ $data->position_name }}</option>
                        @endforeach
                    </flux:select>

                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 space-y-4">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Initial Placement</p>
                        
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
                                    <option value="{{ $zone->workplace_id }}">{{ $zone->short_name }}
                                    </option>
                                @endforeach
                            </flux:select>

                            <flux:select label="Institution Category" wire:model.live="institutionCategory">
                                <option value="">Select</option>
                                @foreach ($institutionCategoryOption as $data)
                                    <option value="{{ $data->institution_category_id }}">
                                        {{ $data->institution_category_name }}</option>
                                @endforeach
                            </flux:select>
                        @endif

                        <flux:select label="Working Place" wire:model.live="workingPlace" icon="building-library">
                            <option value="">Select Workplace</option>
                            @foreach ($workingPlaceOption as $office)
                                <option value="{{ $office->workplace_id }}">{{ $office->office_name }}</option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row gap-3 pt-2 pb-2">
                        <flux:button type="button" variant="ghost" wire:click="resetFields" class="w-full sm:flex-1">Reset</flux:button>
                        <flux:button type="submit" variant="primary" class="w-full sm:flex-[2] shadow-lg shadow-emerald-500/20">Save Appointment</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>
    @endif
</div>