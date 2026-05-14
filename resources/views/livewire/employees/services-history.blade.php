<div class="space-y-10">
    {{-- SECTION 1: PREVIOUS SERVICE --}}
    <section>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 px-1">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-blue-100 dark:bg-blue-900/30 rounded-xl">
                    <flux:icon.briefcase class="size-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <h2 class="text-lg sm:text-xl font-extrabold tracking-tight text-gray-900 dark:text-white leading-tight">Previous Service-related information</h2>
                    <p class="text-xs sm:text-sm text-gray-500">History of positions and ranks</p>
                </div>
            </div>

            <div class="flex items-center justify-between sm:justify-end gap-2 border-t sm:border-t-0 pt-3 sm:pt-0 border-gray-100 dark:border-gray-800">
                @if ($canCreate)
                    <flux:modal.trigger name="add-service-record">
                        <flux:button variant="subtle" icon="plus" size="sm" class="rounded-full">Previous Record</flux:button>
                    </flux:modal.trigger>
                @endif
            </div>
        </div>

        <div class="space-y-4">
            @forelse ($serviceUpdate->where('updated_type', '!=', '1') as $item)
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
                    <div class="flex flex-col md:flex-row md:items-center gap-6">
                        {{-- Position & Service --}}
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-extrabold text-gray-900 dark:text-white uppercase truncate">
                                {{ $item->position->position_name }}
                            </h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase">{{ $item->service->service_name }}</span>
                                <span class="text-gray-300 dark:text-gray-600">•</span>
                                <span class="text-xs text-gray-500 italic">{{ $item->rank->rank_name }}</span>
                            </div>
                        </div>

                        {{-- Timeline --}}
                        <div class="flex gap-8 md:gap-12 border-t md:border-t-0 md:border-l border-gray-100 dark:border-gray-700 pt-4 md:pt-0 md:pl-10">
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Start Date</p>
                                <p class="text-sm font-mono font-bold text-gray-700 dark:text-gray-300">{{ $item->appoint_date }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">End Date</p>
                                <p class="text-sm font-mono font-bold text-gray-700 dark:text-gray-300">{{ $item->end_date }}</p>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex justify-end md:w-20">
                            @if ($canDelete)
                                <flux:button icon="trash" variant="ghost" size="sm" class="text-gray-400 hover:text-red-500"
                                    wire:click="deleteServiceRecord({{ $item->id }})"
                                    onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" />
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-10 border-2 border-dashed border-gray-100 dark:border-gray-800 rounded-2xl text-center text-sm text-gray-400">
                    No service records found.
                </div>
            @endforelse
        </div>
    </section>

    {{-- SECTION 2: WORKING PLACE --}}
    <section>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 px-1">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl">
                    <flux:icon.map-pin class="size-5 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div>
                    <h2 class="text-lg sm:text-xl font-extrabold tracking-tight text-gray-900 dark:text-white leading-tight">Previous Working Place</h2>
                    <p class="text-xs sm:text-sm text-gray-500">History of office placements</p>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            @forelse ($serviceUpdate->where('updated_type', 1) as $item)
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
                    <div class="flex flex-col md:flex-row md:items-center gap-6">
                        {{-- Office & Address --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mb-1">Institution</p>
                            <h3 class="text-base font-extrabold text-gray-900 dark:text-white uppercase truncate">
                                {{ $item->workplace->office_name ?? 'N/A' }}
                            </h3>
                            <p class="text-xs text-gray-500 mt-1 truncate">{{ $item->workplace->address ?? 'N/A' }}</p>
                        </div>

                        {{-- Service Period Badge --}}
                        <div class="md:w-32">
                             <flux:badge color="green" variant="pill" size="sm" class="font-bold">{{ $item->service_period }}</flux:badge>
                        </div>

                        {{-- Dates --}}
                        <div class="flex gap-8 md:gap-12 border-t md:border-t-0 md:border-l border-gray-100 dark:border-gray-700 pt-4 md:pt-0 md:pl-10">
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Appointed</p>
                                <p class="text-sm font-mono font-bold text-gray-700 dark:text-gray-300">{{ $item->appoint_date }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Released</p>
                                <p class="text-sm font-mono font-bold text-gray-700 dark:text-gray-300">{{ $item->end_date }}</p>
                            </div>
                        </div>

                        <div class="flex justify-end md:w-20">
                            @if ($canDelete)
                                <flux:button icon="trash" variant="ghost" size="sm" class="text-gray-400 hover:text-red-500"
                                    wire:click="deleteServiceRecord({{ $item->id }})"
                                    onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" />
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-10 border-2 border-dashed border-gray-100 dark:border-gray-800 rounded-2xl text-center text-sm text-gray-400">
                    No workplace history found.
                </div>
            @endforelse
        </div>
    </section>

    {{-- MODAL - Same Logic as Original --}}
    @if ($canCreate)
        <flux:modal name="add-service-record" class="md:w-150" wire:model="showModal" dismissible="false">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Add Previous Record</flux:heading>
                    <flux:text class="mt-2">Enter the details of the previous service record.</flux:text>
                </div>

                <form wire:submit.prevent="saveServiceRecord" class="space-y-4">
                    <flux:select label="What is update type" wire:model.live="recordType">
                        <flux:select.option value="">Select type</flux:select.option>
                        <flux:select.option value="0">Position</flux:select.option>
                        <flux:select.option value="1">Grade update</flux:select.option>
                        <flux:select.option value="2">Previous workplace</flux:select.option>
                    </flux:select>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:select label="Service" wire:model.live="service">
                            <flux:select.option value="">Select Service</flux:select.option>
                            @foreach ($userServicesOptions as $service)
                                <flux:select.option value="{{ $service->service_id }}">{{ $service->service->service_name }}</flux:select.option>
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
                        <flux:select.option value="">Select Rank</flux:select.option>
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:input label="Appointed Date" type="date" wire:model.live="appointDate" />
                        <flux:input label="Ended Date" type="date" wire:model.live="endedDate" />
                    </div>

                    <div class="flex">
                        <flux:spacer />
                        <flux:button type="submit" variant="primary">Save changes</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>
    @endif
</div>