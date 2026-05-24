<div class="space-y-8">

    {{-- SERVICE HISTORY --}}
    <section>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
                <h2 class="text-base font-black tracking-widest text-slate-700 dark:text-zinc-200 uppercase">Service History xxxxx</h2>
                <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-[0.2em] mt-0.5">History of Positions & Grades</p>
            </div>
            @if ($canCreate)
            <flux:modal.trigger name="add-service-record">
                <flux:button variant="ghost" size="sm" icon="plus" class="rounded-xl border border-slate-300 dark:border-zinc-700 font-bold text-xs px-5 text-slate-600 dark:text-zinc-300 hover:border-indigo-400 hover:text-indigo-600 transition-all w-fit">Add Record</flux:button>
            </flux:modal.trigger>
            @endif
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-300 dark:border-zinc-700 overflow-hidden">
            <div class="hidden sm:flex items-center bg-slate-50 dark:bg-zinc-800/50 border-b border-slate-300 dark:border-zinc-700 px-6 py-3">
                <span class="flex-1 text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Position / Service / Grade</span>
                <span class="w-36 text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Start Date</span>
                <span class="w-36 text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">End Date</span>
                @if($canDelete)<span class="w-10"></span>@endif
            </div>
            @forelse ($serviceUpdate->where('updated_type', '!=', '1') as $item)
            <div class="flex flex-col sm:flex-row sm:items-center border-b border-dashed border-slate-300 dark:border-zinc-700 last:border-b-0 px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors group gap-3 sm:gap-0">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800 dark:text-zinc-100 uppercase leading-tight">{{ $item->position->position_name }}</p>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-[10px] font-semibold text-slate-500 uppercase">{{ $item->service->service_name }}</span>
                        <span class="text-slate-300 dark:text-zinc-600">•</span>
                        <span class="text-[10px] text-slate-500 italic">{{ $item->rank->rank_name }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-4 sm:contents">
                    <div class="sm:w-36">
                        <span class="sm:hidden text-[9px] font-black text-slate-500 uppercase tracking-widest mr-2">From:</span>
                        <span class="text-sm font-mono font-semibold text-slate-600 dark:text-zinc-300">{{ $item->appoint_date }}</span>
                    </div>
                    <div class="sm:w-36">
                        <span class="sm:hidden text-[9px] font-black text-slate-500 uppercase tracking-widest mr-2">To:</span>
                        <span class="text-sm font-mono font-semibold text-slate-600 dark:text-zinc-300">{{ $item->end_date }}</span>
                    </div>
                </div>

                @if ($canDelete)
                <div class="w-full sm:w-10 flex justify-end">
                    <flux:button icon="trash" variant="ghost" size="sm"
                        class="sm:opacity-0 group-hover:opacity-100 transition-opacity text-slate-500 hover:text-rose-500 rounded-lg"
                        wire:click="deleteServiceRecord({{ $item->id }})"
                        onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" />
                </div>
                @endif
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-12 px-6">
                <p class="text-sm font-semibold text-slate-500 uppercase tracking-widest">No service records found</p>
            </div>
            @endforelse
        </div>
    </section>

    {{-- WORKING PLACE HISTORY --}}
    <section>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div class="flex items-center gap-4 flex-1">
                <h2 class="text-base font-black tracking-widest text-slate-700 dark:text-zinc-200 uppercase whitespace-nowrap">Working Place History</h2>
                <div class="hidden sm:block h-px flex-1 bg-slate-200 dark:bg-zinc-800"></div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-300 dark:border-zinc-700 overflow-hidden">
            <div class="hidden sm:flex items-center bg-slate-50 dark:bg-zinc-800/50 border-b border-slate-300 dark:border-zinc-700 px-6 py-3">
                <span class="flex-1 text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Institution</span>
                <span class="w-28 text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Period</span>
                <span class="w-36 text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Appointed</span>
                <span class="w-36 text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Released</span>
                @if($canDelete)<span class="w-10"></span>@endif
            </div>
            @forelse ($serviceUpdate->where('updated_type', 1) as $item)
            <div class="flex flex-col sm:flex-row sm:items-center border-b border-dashed border-slate-300 dark:border-zinc-700 last:border-b-0 px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors group gap-3 sm:gap-0">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800 dark:text-zinc-100 uppercase leading-tight">{{ $item->workplace->office_name ?? 'N/A' }}</p>
                    @if($item->workplace->address ?? null)
                    <p class="text-[10px] text-slate-500 mt-0.5">{{ $item->workplace->address }}</p>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-4 sm:contents">
                    <div class="sm:w-28">
                        <span class="sm:hidden text-[9px] font-black text-slate-500 uppercase tracking-widest mr-2">Duration:</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[9px] font-black bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 uppercase tracking-widest w-fit">{{ $item->service_period }}</span>
                    </div>
                    <div class="sm:w-36">
                        <span class="sm:hidden text-[9px] font-black text-slate-500 uppercase tracking-widest mr-2">From:</span>
                        <span class="text-sm font-mono font-semibold text-slate-600 dark:text-zinc-300">{{ $item->appoint_date }}</span>
                    </div>
                    <div class="sm:w-36">
                        <span class="sm:hidden text-[9px] font-black text-slate-500 uppercase tracking-widest mr-2">To:</span>
                        <span class="text-sm font-mono font-semibold text-slate-600 dark:text-zinc-300">{{ $item->end_date }}</span>
                    </div>
                </div>

                @if ($canDelete)
                <div class="w-full sm:w-10 flex justify-end">
                    <flux:button icon="trash" variant="ghost" size="sm"
                        class="sm:opacity-0 group-hover:opacity-100 transition-opacity text-slate-500 hover:text-rose-500 rounded-lg"
                        wire:click="deleteServiceRecord({{ $item->id }})"
                        onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" />
                </div>
                @endif
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-12 px-6">
                <p class="text-sm font-semibold text-slate-500 uppercase tracking-widest">No workplace history found</p>
            </div>
            @endforelse
        </div>
    </section>

    {{-- Add Modal --}}
    @if ($canCreate)
    <flux:modal name="add-service-record" class="md:w-150" wire:model="showModal" dismissible="false">
        <div class="space-y-6">
            <div>
                <h3 class="text-sm font-black tracking-widest text-slate-900 dark:text-white uppercase">Add Service Record</h3>
                <p class="text-[10px] font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest mt-0.5">Enter details of the previous service record</p>
            </div>
            <form wire:submit.prevent="saveServiceRecord" class="space-y-4">
                <flux:select label="Update Type" wire:model.live="recordType">
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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input label="Appointed Date" type="date" wire:model.live="appointDate" />
                    <flux:input label="Ended Date" type="date" wire:model.live="endedDate" />
                </div>
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
</div>