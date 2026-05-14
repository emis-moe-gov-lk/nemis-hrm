<div class="space-y-6">
    {{-- Section Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 px-1">
        <div class="flex items-center gap-3">
            <div class="p-2.5 bg-blue-100 dark:bg-blue-900/30 rounded-xl">
                <flux:icon.briefcase class="size-5 text-blue-600 dark:text-blue-400" />
            </div>
            <div>
                <h2 class="text-lg sm:text-xl font-extrabold tracking-tight text-gray-900 dark:text-white leading-tight">Previous Service</h2>
                <p class="text-xs sm:text-sm text-gray-500">History of official career appointments</p>
            </div>
        </div>

        <div class="flex items-center justify-between sm:justify-end gap-2 border-t sm:border-t-0 pt-3 sm:pt-0 border-gray-100 dark:border-gray-800">
            @if ($canCreate)
                <flux:modal.trigger name="add-previous-service">
                    <flux:button variant="subtle" icon="plus" size="sm" class="rounded-full">Add Record</flux:button>
                </flux:modal.trigger>
            @endif
        </div>
    </div>

    {{-- Horizontal Cards Stack --}}
    <div class="space-y-4">
        @forelse ($employeeServiceList->where('updated_type', '!=', '1') as $item)
            {{-- Enhanced Horizontal Card --}}
            <div class="relative overflow-hidden bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all group">
                
                {{-- Subtle background decoration to fill space --}}
                <div class="absolute top-0 right-0 -mr-4 -mt-4 p-8 opacity-[0.03] dark:opacity-[0.05] group-hover:scale-110 transition-transform">
                    <flux:icon.briefcase class="size-24" />
                </div>

                <div class="flex flex-col md:flex-row md:items-center gap-6 relative">
                    
                    {{-- Icon & Main Info --}}
                    <div class="flex items-start gap-4 flex-1">
                        <div class="p-3 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl text-indigo-600 dark:text-indigo-400">
                            <flux:icon.briefcase class="size-6" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-1">Official Assignment</p>
                            <h3 class="text-base font-extrabold text-gray-900 dark:text-white uppercase leading-tight truncate">
                                {{ $item->service->service_name }}
                            </h3>
                            <div class="flex items-center gap-2 mt-1.5">
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase">{{ $item->rank->rank_name }}</span>
                                <span class="text-gray-300 dark:text-gray-600">|</span>
                                <span class="text-xs text-gray-500 font-mono">{{ $item->appointment_letter_no }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Timeline Details --}}
                    <div class="grid grid-cols-2 md:flex md:flex-row gap-8 md:gap-12 border-t md:border-t-0 md:border-l border-gray-100 dark:border-gray-700 pt-4 md:pt-0 md:pl-10">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tight mb-1">Appointed On</p>
                            <div class="flex items-center gap-2">
                                <flux:icon.calendar class="size-3.5 text-gray-400" />
                                <span class="text-sm font-bold font-mono text-gray-700 dark:text-gray-300">{{ $item->first_appointment_date->format('Y-m-d') }}</span>
                            </div>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tight mb-1">Released On</p>
                            <div class="flex items-center gap-2">
                                <flux:icon.calendar class="size-3.5 text-gray-400" />
                                <span class="text-sm font-bold font-mono text-gray-700 dark:text-gray-300">{{ $item->retirement_date->format('Y-m-d') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Status & Actions --}}
                    <div class="flex items-center justify-between md:flex-col md:items-end md:justify-center gap-3">
                        @if ($item->active_status == 1)
                            <flux:badge color="green" size="sm" inset="top bottom" class="font-bold">ACTIVE</flux:badge>
                        @else
                            <flux:badge color="zinc" size="sm" inset="top bottom" class="font-bold">INACTIVE</flux:badge>
                        @endif

                        <div class="flex gap-1">
                            @if ($canDelete)
                                <flux:button icon="trash" variant="ghost" size="sm"
                                    class="text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/20"
                                    wire:click="deleteServiceRecord({{ $item->id }})"
                                    onclick="confirm('Are you sure you want to delete this record?') || event.stopImmediatePropagation()" />
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center p-16 bg-gray-50/50 dark:bg-gray-900/10 border-2 border-dashed border-gray-200 dark:border-gray-800 rounded-3xl">
                <flux:icon.briefcase class="size-10 text-gray-200 dark:text-gray-700 mb-4" />
                <p class="text-sm text-gray-500 font-medium">No previous service history found.</p>
            </div>
        @endforelse
    </div>

    {{-- Modal Section --}}
    @if ($canCreate)
        <flux:modal name="add-previous-service" class="md:w-150" wire:model="showModal" dismissible="false">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Add Previous Record</flux:heading>
                    <flux:text class="mt-2">Enter the details of the previous service record.</flux:text>
                </div>

                <form wire:submit.prevent="saveServiceRecord" class="space-y-4">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="md:w-1/2 w-full">
                            <flux:input type="date" label="First Appointment Date" wire:model.live="firstAppointmentDate" />
                        </div>
                        <div class="md:w-1/2 w-full">
                            <flux:input label="Appointment Letter No" wire:model.live="appointmentLetterNo" placeholder="Enter letter number" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                        <flux:select label="Service" wire:model.live="service">
                            <flux:select.option value="">Select Service</flux:select.option>
                            @foreach ($servicesOptions as $service)
                                <flux:select.option value="{{ $service->service_id }}">{{ $service->service_name }}</flux:select.option>
                            @endforeach
                        </flux:select>

                        <flux:select label="Grade" wire:model.live="rank">
                            <flux:select.option value="">Select Rank</flux:select.option>
                            @foreach ($ranksOptions as $rank)
                                <flux:select.option value="{{ $rank->rank_id }}">{{ $rank->rank_name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>

                    <flux:select label="Position" wire:model.live="position">
                        <flux:select.option value="">Select Position</flux:select.option>
                        @foreach ($positionOption as $position)
                            <flux:select.option value="{{ $position->position_id }}">{{ $position->position_name }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:select label="Working Place Level" wire:model.live="officeLevel">
                        <option value="">Select</option>
                        @foreach ($officeLevelOption as $level)
                            <option value="{{ $level->office_level_id }}">{{ $level->office_level_name }}</option>
                        @endforeach
                    </flux:select>

                    @if ($officeLevel == 'OLID006')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                        </div>
                    @endif

                    <flux:select label="Working Place" wire:model.live="workingPlace">
                        <option value="">Select</option>
                        @foreach ($workingPlaceOption as $office)
                            <option value="{{ $office->workplace_id }}">{{ $office->office_name }}</option>
                        @endforeach
                    </flux:select>

                    <div class="flex pt-2">
                        <flux:spacer />
                        <flux:button type="submit" variant="primary">Save changes</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>
    @endif
</div>