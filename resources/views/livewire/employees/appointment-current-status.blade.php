<div>
    <section>
        {{-- Responsive Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 px-1">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-blue-100 dark:bg-blue-900/30 rounded-xl">
                    <flux:icon.briefcase class="size-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <h2 class="text-lg sm:text-xl font-extrabold tracking-tight text-gray-900 dark:text-white leading-tight">Employment Status</h2>
                    <p class="text-xs sm:text-sm text-gray-500">Current appointment & placement</p>
                </div>
            </div>

            <div class="flex items-center justify-between sm:justify-end gap-2 border-t sm:border-t-0 pt-3 sm:pt-0 border-gray-100 dark:border-gray-800">
                <flux:badge variant="pill" icon="clock" color="blue" class="text-[10px] sm:text-xs">
                    {{ $employee->currentAppointment->service_years ?? '0' }} Service
                </flux:badge>
                @if($canEdit)
                    <flux:modal.trigger name="current-employment-edit">
                        <flux:button variant="ghost" icon="pencil-square" size="sm" class="rounded-full">Edit</flux:button>
                    </flux:modal.trigger>
                @endif
            </div>
        </div>

        <div class="space-y-4">
            {{-- Mobile-Optimized Primary Card --}}
            <div class="relative overflow-hidden bg-gradient-to-br from-slate-800 via-slate-900 to-blue-950 p-5 sm:p-6 rounded-2xl shadow-xl text-white">
                {{-- Decorative Background --}}
                <div class="absolute inset-0 opacity-10 pointer-events-none">
                    <svg class="h-full w-full" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid-mobile" width="30" height="30" patternUnits="userSpaceOnUse"><path d="M 30 0 L 0 0 0 30" fill="none" stroke="white" stroke-width="0.5"/></pattern></defs><rect width="100%" height="100%" fill="url(#grid-mobile)" /></svg>
                </div>

                <div class="relative flex flex-col xs:flex-row items-start xs:items-center gap-4">
                    {{-- Icon - Hidden on very small screens to save space if needed, or kept small --}}
                    <div class="hidden xs:block p-3.5 bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 shadow-inner">
                        <flux:icon.building-office-2 class="size-6 text-blue-300" />
                    </div>

                    <div class="flex-1 min-w-0 w-full">
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="text-[9px] font-bold text-blue-300/70 uppercase tracking-[0.2em]">Active Placement</span>
                        </div>

                        <h3 class="text-base sm:text-lg font-bold leading-tight flex flex-wrap items-center gap-2">
                            @if ($employee->currentAppointment?->workplace?->office()?->census_no)
                                <span class="bg-blue-400/20 text-blue-100 px-1.5 py-0.5 rounded text-[10px] font-mono border border-white/10">
                                    {{ $employee->currentAppointment->workplace->office()->census_no }}
                                </span>
                            @endif
                            <span class="break-words">{{ $employee->currentAppointment->workplace->office()->name ?? 'Not Assigned' }}</span>
                        </h3>

                        <div class="mt-2.5 flex items-start gap-2 text-slate-300/90">
                            <flux:icon.map-pin class="size-3.5 mt-0.5 flex-shrink-0 text-blue-400/60" />
                            <p class="text-xs sm:text-sm leading-relaxed">
                                {{ $employee->currentAppointment->workplace->office()->address ?? 'Address not available' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Service Details Grid - Now 2 columns on mobile --}}
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
                <div class="p-3.5 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm col-span-2 sm:col-span-1">
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1">Service Branch</p>
                    <p class="text-sm font-bold text-gray-900 dark:text-gray-100 truncate">
                        {{ $employee->currentAppointment->service->service_name ?? 'N/A' }}
                    </p>
                </div>

                <div class="p-3.5 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm">
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1">Current Grade</p>
                    <flux:badge size="sm" color="blue" class="text-[10px] px-1.5">{{ $employee->currentAppointment->rank->rank_name ?? 'N/A' }}</flux:badge>
                </div>

                <div class="p-3.5 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm">
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1">Designation</p>
                    <p class="text-sm font-bold text-gray-900 dark:text-gray-100 truncate">
                        {{ $employee->currentAppointment->position->position_name ?? 'N/A' }}
                    </p>
                </div>

                <div class="p-3.5 bg-gray-50/50 dark:bg-gray-900/40 rounded-xl border border-dashed border-gray-200 dark:border-gray-800">
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1">Appointed</p>
                    <p class="text-xs font-mono font-bold text-gray-600 dark:text-gray-400">
                        {{ $employee->currentAppointment->appoint_date?->format('Y-m-d') ?? 'N/A' }}
                    </p>
                </div>

                <div class="p-3.5 bg-gray-50/50 dark:bg-gray-900/40 rounded-xl border border-dashed border-gray-200 dark:border-gray-800 col-span-1">
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1">Reference</p>
                    <p class="text-xs font-mono font-bold text-gray-600 dark:text-gray-400 truncate">
                        {{ $employee->currentAppointment->appointment_letter_no ?? '---' }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Edit Modal - Using scrollable area for mobile --}}
    @if($canEdit)
        <flux:modal name="current-employment-edit" class="w-full max-w-lg">
            <div class="space-y-6 max-h-[80vh] overflow-y-auto pr-2">
                <div>
                    <flux:heading size="lg">Service Update</flux:heading>
                    <flux:text class="text-sm">Modify current deployment and service record.</flux:text>
                </div>

                <form wire:submit.prevent="save" class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <flux:input type="date" label="Appointment Date" wire:model.live="appointmentDate" />
                        <flux:input label="Letter Number" wire:model.live="appointmentLetterNo" />
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
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Placement</p>
                        
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

                        <flux:select label="Working Place" wire:model.live="workingPlace">
                            <option value="">Select Office</option>
                            @foreach ($workingPlaceOption as $office)
                                <option value="{{ $office->workplace_id }}">@if ($officeLevel == 'OLID006') {{'['. str_pad((string)$office->institution->census_no, 5, "0", STR_PAD_LEFT) .']' }} @endif {{ $office->office_name }}</option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row gap-3 pt-4 pb-2">
                        <flux:button type="button" variant="ghost" wire:click="resetFields" class="w-full sm:flex-1">Reset</flux:button>
                        <flux:button type="submit" variant="primary" class="w-full sm:flex-[2]">Update Records</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>
    @endif
</div>