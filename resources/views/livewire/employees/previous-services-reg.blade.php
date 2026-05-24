<div class="space-y-8">
    <section>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
                <h2 class="text-base font-black tracking-widest text-slate-700 dark:text-zinc-200 uppercase">Previous Service</h2>
                <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-[0.2em] mt-0.5">History of Official Career Appointments</p>
            </div>
            @if ($canCreate)
            <flux:modal.trigger name="add-previous-service">
                <flux:button variant="ghost" size="sm" icon="plus" class="rounded-xl border border-slate-300 dark:border-zinc-700 font-bold text-xs px-5 text-slate-600 dark:text-zinc-300 hover:border-indigo-400 hover:text-indigo-600 transition-all w-fit">Add Record</flux:button>
            </flux:modal.trigger>
            @endif
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-300 dark:border-zinc-700 overflow-hidden">

            <div x-data="{ activeService: null }" class="divide-y divide-slate-100 dark:divide-zinc-800">
                @forelse ($employeeServiceList->where('updated_type', '!=', '1') as $item)
                <div class="group">
                    {{-- Service Accordion Trigger --}}
                    <div
                        x-on:click="activeService = activeService === {{ $item->id }} ? null : {{ $item->id }}"
                        class="flex flex-1 items-center justify-between cursor-pointer hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors px-4 sm:px-6 py-4">
                        <div class="flex flex-1 flex-col lg:flex-row items-start lg:items-center justify-between gap-4 pr-4">
                            {{-- Title and Workplace --}}
                            <div class="flex-1 min-w-0 w-full">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <h3 class="text-xs sm:text-sm font-black text-slate-800 dark:text-zinc-100 uppercase tracking-tight leading-tight">
                                        <span class="text-slate-500 dark:text-zinc-400 font-bold mr-1">{{ $item->position->position_name }}</span>
                                        <span class="text-slate-300 dark:text-zinc-600 font-normal mx-1">•</span>
                                        <span>{{ $item->service->service_name }}</span>
                                        <span class="text-slate-300 dark:text-zinc-600 font-normal mx-1">/</span>
                                        <span class="text-indigo-600 dark:text-indigo-400">{{ $item->rank->rank_name }}</span>
                                    </h3>

                                    @if ($item->active_status == 1)
                                    <span class="inline-flex px-1.5 py-0.5 rounded-md text-[8px] font-black bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 uppercase tracking-widest border border-emerald-100 dark:border-emerald-800/50">Active</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    <flux:icon.map-pin class="size-3 text-slate-300" />
                                    <span class="text-[10px] font-bold text-slate-500 uppercase truncate">{{ $item->workplace->office_name ?? 'No Workplace assigned' }}</span>
                                </div>
                            </div>

                            {{-- Service Period --}}
                            <div class="flex items-center lg:items-end gap-4 w-full lg:w-auto" x-on:click.stop>
                                <div class="flex flex-row lg:flex-col items-center lg:items-end justify-between lg:justify-end w-full lg:w-auto gap-2 lg:gap-1 bg-slate-50 dark:bg-zinc-800/50 lg:bg-transparent p-2 lg:p-0 rounded-lg lg:rounded-none border border-slate-200 dark:border-zinc-700 lg:border-none">
                                    <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest leading-none">Period</span>
                                    <span class="text-[10px] sm:text-[11px] font-mono font-bold text-slate-600 dark:text-zinc-300">
                                        {{ $item->first_appointment_date->format('Y-m-d') }} <span class="text-slate-300 mx-1">→</span> {{ $item->retirement_date->format('Y-m-d') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Chevron --}}
                        <div class="shrink-0">
                            <flux:icon.chevron-down class="size-4 text-slate-500 transition-transform duration-300" x-bind:class="activeService === {{ $item->id }} ? 'rotate-180' : ''" />
                        </div>
                    </div>

                    {{-- Service Accordion Content --}}
                    <div x-show="activeService === {{ $item->id }}" x-collapse x-cloak class="bg-slate-50/50 dark:bg-zinc-800/20 pb-6">
                        <div class="space-y-4 px-6">
                            <div class="flex py-4 items-center justify-between border-b border-slate-300/60 dark:border-zinc-700 pb-2 mb-4">
                                <h4 class="text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-[0.2em]">Rank & Grade Progression History</h4>

                                <div class="flex items-center gap-3">
                                    @if($item->appointment_letter_no)
                                    <span class="text-[9px] font-mono text-slate-500 bg-white dark:bg-zinc-800 px-2 py-1 rounded border border-slate-200 dark:border-zinc-700">Ref: {{ $item->appointment_letter_no }}</span>
                                    @endif

                                    @if($canCreate)
                                    <flux:modal.trigger name="add-rank-history">
                                        <flux:button icon="plus" variant="ghost" size="sm"
                                            class="rounded-lg h-7 px-3 text-[10px] font-black uppercase tracking-widest text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20"
                                            wire:click="setAppointmentForHistory('{{ $item->appointment_id }}')">
                                            Add Rank
                                        </flux:button>
                                    </flux:modal.trigger>
                                    @endif

                                    @if ($canDelete && $item->active_status != 1)
                                    <flux:button icon="trash" variant="ghost" size="sm"
                                        class="text-rose-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg h-7 px-3 text-[10px] font-black uppercase tracking-widest"
                                        wire:click="confirmDeleteService({{ $item->id }})">
                                        Delete Record
                                    </flux:button>
                                    @endif
                                </div>
                            </div>

                            {{-- Nested Rank History Accordion (Alpine) --}}
                            @if($item->rankHistory->count() > 0)
                            <div x-data="{ activeRank: null }" class="space-y-2">
                                @foreach($item->rankHistory as $history)
                                <div class="bg-white dark:bg-zinc-900 rounded-xl border border-slate-300 dark:border-zinc-700 overflow-hidden shadow-sm">
                                    {{-- Rank Trigger --}}
                                    <div
                                        x-on:click="activeRank = activeRank === {{ $history->id }} ? null : {{ $history->id }}"
                                        class="flex items-center justify-between px-4 py-3 cursor-pointer hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center">
                                                <flux:icon.chevron-double-up class="size-4 text-indigo-500" />
                                            </div>
                                            <div>
                                                <p class="text-xs font-black text-slate-700 dark:text-zinc-200 uppercase">Rank : {{ $history->rank->rank_name }}</p>
                                                <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">{{ $history->start_date->format('M Y') }} — {{ $history->end_date ? $history->end_date->format('M Y') : 'Present' }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            @if($history->is_active)
                                            <span class="size-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)] animate-pulse"></span>
                                            @endif

                                            @if($canDelete)
                                            <flux:button icon="trash" variant="ghost" size="sm"
                                                class="text-slate-300 hover:text-rose-500 rounded-lg"
                                                x-on:click.stop
                                                wire:click="confirmDeleteRank({{ $history->id }})" />
                                            @endif

                                            <flux:icon.chevron-down class="size-3.5 text-slate-300 transition-transform duration-300" x-bind:class="activeRank === {{ $history->id }} ? 'rotate-180' : ''" />
                                        </div>
                                    </div>

                                    {{-- Rank Content --}}
                                    <div x-show="activeRank === {{ $history->id }}" x-collapse x-cloak class="px-4 py-3 border-t border-slate-200 dark:border-zinc-700/50 bg-slate-50/30 dark:bg-zinc-800/10 text-[11px]">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">Effective Range</p>
                                                <p class="font-mono font-bold text-slate-600 dark:text-zinc-300">{{ $history->start_date->format('Y-m-d') }} to {{ $history->end_date ? $history->end_date->format('Y-m-d') : 'Active' }}</p>
                                            </div>
                                            @if($history->ref_letter_no)
                                            <div>
                                                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">Reference Letter No</p>
                                                <p class="font-mono font-bold text-slate-600 dark:text-zinc-300">{{ $history->ref_letter_no }}</p>
                                            </div>
                                            @endif
                                            @if($history->remarks)
                                            <div class="col-span-1 sm:col-span-2">
                                                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">Remarks</p>
                                                <p class="italic text-slate-500">{{ $history->remarks }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="flex flex-col items-center justify-center py-8 rounded-xl border border-dashed border-slate-300 dark:border-zinc-700">
                                <flux:icon.list-bullet class="size-6 text-slate-200 dark:text-zinc-700 mb-2" />
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">No detailed rank history recorded</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-16 px-6">
                    <div class="w-16 h-16 rounded-2xl bg-slate-50 dark:bg-zinc-800 flex items-center justify-center mb-4">
                        <flux:icon.briefcase class="size-8 text-slate-300 dark:text-zinc-600" />
                    </div>
                    <p class="text-sm font-semibold text-slate-500 uppercase tracking-widest">No previous service history found</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    @if ($canCreate)
    <flux:modal name="add-previous-service" class="md:w-150" wire:model="showModal" dismissible="false">
        <div class="space-y-6">
            <div>
                <h3 class="text-sm font-black tracking-widest text-slate-900 dark:text-white uppercase">Add Previous Record</h3>
                <p class="text-[10px] font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest mt-0.5">Enter details of the previous service record</p>
            </div>
            <form wire:submit.prevent="saveServiceRecord" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input type="date" label="First Appointment Date" wire:model.live="firstAppointmentDate" />
                    <flux:input label="Appointment Letter No" wire:model.live="appointmentLetterNo" placeholder="Enter letter number" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                <div class="flex gap-4 pt-2">
                    <flux:modal.close>
                        <flux:button variant="ghost" class="flex-1 font-bold rounded-xl h-12">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary" class="flex-1 font-black rounded-xl h-12 bg-indigo-600 dark:bg-white text-white dark:text-slate-900">Save Record</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
    @endif

    {{-- Add Rank History Modal --}}
    @if ($canCreate)
    <flux:modal name="add-rank-history" class="md:w-120" wire:model="showRankModal" dismissible="false">
        <div class="space-y-6">
            <div>
                <h3 class="text-sm font-black tracking-widest text-slate-900 dark:text-white uppercase">Add Rank Progression</h3>
                <p class="text-[10px] font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest mt-0.5">Record a grade change or rank promotion</p>
            </div>

            <form wire:submit.prevent="saveRankHistory" class="space-y-4">
                <flux:select label="Target Rank / Grade" wire:model.live="historyRankId">
                    <option value="">Select Rank</option>
                    @foreach ($ranksOptions as $rank)
                    <option value="{{ $rank->rank_id }}">{{ $rank->rank_name }}</option>
                    @endforeach
                </flux:select>

                <div class="grid grid-cols-2 gap-4">
                    <flux:input type="date" label="Effective From" wire:model.live="historyStartDate" />
                    <flux:input type="date" label="Effective To" wire:model.live="historyEndDate" placeholder="Leave empty if present" />
                </div>

                <flux:textarea label="Remarks / Authority" wire:model.live="historyRemarks" placeholder="e.g. Promotion Letter No, Board Decision..." />

                <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-zinc-800/50 rounded-xl border border-slate-200 dark:border-zinc-700">
                    <flux:checkbox wire:model.live="historyIsActive" label="Mark as current rank for this appointment" />
                </div>

                <div class="flex gap-3 pt-4">
                    <flux:modal.close>
                        <flux:button variant="ghost" class="flex-1 font-bold rounded-xl h-12">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary" class="flex-1 font-black rounded-xl h-12 bg-indigo-600 hover:bg-indigo-700 text-white border-none shadow-lg shadow-indigo-200 dark:shadow-none">Save Progression</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
    @endif

    {{-- Delete Modals --}}
    <x-delete-confirmation 
        name="delete-service-confirmation" 
        wireAction="deleteServiceRecord" 
        title="Delete Service Record?" 
        description="Are you sure you want to delete this entire service record? This will also remove all associated rank, workplace, and position history."
    />

    <x-delete-confirmation 
        name="delete-rank-confirmation" 
        wireAction="deleteRankHistoryRecord" 
        title="Delete Rank Progression?" 
        description="Are you sure you want to delete this rank history record? This action cannot be undone."
    />
</div>