<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    {{-- Page Header --}}
    <x-page-header
        title="Employee Attachments"
        subtitle="List of appointments and attachments of the employee."
        icon="briefcase"
        :breadcrumbs="[
            'Home'    => route('teacher.overview'),
            'Employee Attachments' => '#'
        ]">
        <x-slot:actions>
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                <flux:button x-on:click="$dispatch('modal-show', { name: 'attach-employee-modal' })" wire:click="openAttachModal" variant="primary" icon="user-plus" class="w-full sm:w-auto">Attach Employee</flux:button>
                <div class="w-full sm:w-80">
                    <flux:input
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search by ID, NIC, phone..."
                        icon="magnifying-glass"
                        clearable />
                </div>
            </div>
        </x-slot:actions>
    </x-page-header>
    <div class="space-y-8">
        <section>
            <div class="flex items-center gap-4 mb-6">
                <h2 class="text-base font-black tracking-widest text-slate-700 dark:text-zinc-200 uppercase whitespace-nowrap">Attachments List</h2>
                <div class="h-px flex-1 bg-slate-200 dark:bg-zinc-800"></div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-300 dark:border-zinc-700 overflow-hidden">
                <div class="hidden sm:flex items-center bg-slate-50 dark:bg-zinc-800/50 border-b border-slate-300 dark:border-zinc-700 px-6 py-3">
                    <span class="w-64 text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Employee</span>
                    <span class="flex-1 text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Office / Workplace</span>
                    <span class="w-48 text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Designation</span>
                    <span class="w-32 text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Start Date</span>
                    <span class="w-32 text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">End Date</span>
                    <span class="w-24 text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest text-center">Status</span>
                </div>

                @forelse($attachments as $attachment)
                <div class="flex flex-col sm:flex-row sm:items-center border-b border-dashed border-slate-300 dark:border-zinc-700 last:border-b-0 px-6 py-3.5 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors group gap-2 sm:gap-0">
                    <div class="sm:w-64 sm:shrink-0 flex flex-col gap-0.5">
                        <span class="text-sm font-semibold text-slate-800 dark:text-zinc-100">
                            {{ $attachment->employee->name_with_initials ?? 'N/A' }}
                        </span>
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">{{ $attachment->employee->nic ?? 'N/A' }}</span>
                    </div>

                    <div class="flex-1 flex flex-col gap-0.5 mt-1 sm:mt-0">
                        <span class="sm:hidden text-[9px] font-black text-slate-500 uppercase tracking-widest mb-0.5">Office: </span>
                        <span class="text-sm font-semibold text-slate-800 dark:text-zinc-100">
                            {{ $attachment->workplace->office()->name ?? 'N/A' }}
                        </span>
                        @if($attachment->officeLevel)
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">{{ $attachment->officeLevel->office_level_name }}</span>
                        @endif
                    </div>

                    <div class="flex items-center gap-3 sm:contents">
                        <div class="sm:w-48 sm:shrink-0">
                            <span class="sm:hidden text-[9px] font-black text-slate-500 uppercase tracking-widest">Designation: </span>
                            <span class="text-sm font-semibold text-slate-600 dark:text-zinc-300">{{ $attachment->position->position_name ?? 'N/A' }}</span>
                        </div>
                        <div class="sm:w-32 sm:shrink-0">
                            <span class="sm:hidden text-[9px] font-black text-slate-500 uppercase tracking-widest">Start: </span>
                            <span class="text-sm font-mono font-semibold text-slate-600 dark:text-zinc-300">{{ $attachment->appoint_date ? \Carbon\Carbon::parse($attachment->appoint_date)->format('Y-m-d') : 'N/A' }}</span>
                        </div>
                        <div class="sm:w-32 sm:shrink-0">
                            <span class="sm:hidden text-[9px] font-black text-slate-500 uppercase tracking-widest">End: </span>
                            <span class="text-sm font-mono font-semibold text-slate-600 dark:text-zinc-300">{{ $attachment->end_date ? \Carbon\Carbon::parse($attachment->end_date)->format('Y-m-d') : 'Present' }}</span>
                        </div>
                        <div class="sm:w-24 sm:shrink-0 flex sm:justify-center">
                            @if($attachment->active_status == 1)
                            <flux:badge size="sm" variant="success" class="uppercase text-[9px] font-black tracking-widest">Active</flux:badge>
                            @else
                            <flux:badge size="sm" variant="neutral" class="uppercase text-[9px] font-black tracking-widest">Inactive</flux:badge>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-12 px-6">
                    <p class="text-sm font-semibold text-slate-500 uppercase tracking-widest">No attachment records found</p>
                </div>
                @endforelse
            </div>
        </section>
    </div>

    {{-- Attach Employee Modal --}}
    <flux:modal name="attach-employee-modal" class="md:w-[600px]">
        <form wire:submit="attachEmployee" class="space-y-6">
            <div>
                <h2 class="text-lg font-black tracking-widest text-slate-700 dark:text-zinc-200 uppercase">Attach Employee</h2>
                <p class="text-sm text-slate-500 dark:text-zinc-400">Attach an employee to a child workplace.</p>
            </div>

            <div class="space-y-4">
                {{-- Employee Search --}}
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-black tracking-widest text-slate-500 uppercase">Employee NIC</label>
                    <div class="flex gap-2">
                        <flux:input wire:model="searchNic" placeholder="Enter Employee NIC" class="flex-1" />
                        <flux:button type="button" wire:click="searchEmployee" variant="filled">Search</flux:button>
                    </div>
                    @error('searchNic') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                @if($attachEmployeeName)
                <div class="bg-green-50 border border-green-200 dark:bg-green-900/20 dark:border-green-900 rounded p-3">
                    <p class="text-sm font-semibold text-green-800 dark:text-green-300">Found Employee: {{ $attachEmployeeName }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div class="col-span-1 md:col-span-2 space-y-4">
                        {{-- Office Level --}}
                        <flux:field>
                            <flux:label class="text-xs font-black tracking-widest text-slate-500 uppercase">Working Place Level</flux:label>
                            <flux:select wire:model.live="officeLevel">
                                <option value="">Select</option>
                                @foreach ($officeLevelOption as $level)
                                <option value="{{ $level->office_level_id }}">{{ $level->office_level_name }}</option>
                                @endforeach
                            </flux:select>
                        </flux:field>

                        {{-- Institution sub-filters (only for OLID006) --}}
                        @if ($officeLevel == 'OLID006')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 rounded-2xl bg-slate-50 dark:bg-zinc-800/40 border border-slate-200 dark:border-zinc-700/50">
                            <flux:field>
                                <flux:label class="text-xs font-black tracking-widest text-slate-500 uppercase">Zonal Education Office</flux:label>
                                <flux:select wire:model.live="zonalEducationOffice">
                                    <option value="">Select</option>
                                    @foreach ($zonalEducationOfficeOption as $zone)
                                    <option value="{{ $zone->workplace_id }}">{{ $zone->short_name }}</option>
                                    @endforeach
                                </flux:select>
                            </flux:field>
                            <flux:field>
                                <flux:label class="text-xs font-black tracking-widest text-slate-500 uppercase">Institution Category</flux:label>
                                <flux:select wire:model.live="institutionCategory">
                                    <option value="">Select</option>
                                    @foreach ($institutionCategoryOption as $data)
                                    <option value="{{ $data->institution_category_id }}">{{ $data->institution_category_name }}</option>
                                    @endforeach
                                </flux:select>
                            </flux:field>
                        </div>
                        @endif

                        {{-- Final Workplace --}}
                        @if ($officeLevel)
                        <div class="relative">
                            {{-- Loading skeleton overlay --}}
                            <div
                                wire:loading
                                wire:target="updatedOfficeLevel,updatedZonalEducationOffice,updatedInstitutionCategory"
                                class="absolute inset-0 z-10 flex items-center gap-3 px-4 rounded-xl bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 animate-pulse">
                                <svg class="animate-spin w-4 h-4 text-indigo-500 shrink-0" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                <span class="text-sm font-semibold text-slate-500 dark:text-slate-400">Loading workplaces…</span>
                            </div>

                            <div wire:loading.class="opacity-0 pointer-events-none"
                                wire:target="updatedOfficeLevel,updatedZonalEducationOffice,updatedInstitutionCategory">
                                <flux:field>
                                    <flux:label class="text-xs font-black tracking-widest text-slate-500 uppercase">Working Place</flux:label>
                                    <flux:select wire:model.live="workingPlace">
                                        <option value="">Select</option>
                                        @foreach ($workingPlaceOption as $office)
                                        <option value="{{ $office->workplace_id }}">{{ $office->office_name }}</option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="workingPlace" />
                                </flux:field>
                            </div>
                        </div>
                        @endif
                    </div>

                    <flux:field class="col-span-1 md:col-span-2">
                        <flux:label class="text-xs font-black tracking-widest text-slate-500 uppercase">Designation</flux:label>
                        <flux:select wire:model="attachPositionId" placeholder="Select Designation">
                            @foreach($positions as $position)
                            <flux:select.option value="{{ $position->position_id }}">{{ $position->position_name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="attachPositionId" />
                    </flux:field>

                    <flux:field>
                        <flux:label class="text-xs font-black tracking-widest text-slate-500 uppercase">Start Date</flux:label>
                        <flux:input type="date" wire:model="attachStartDate" />
                        <flux:error name="attachStartDate" />
                    </flux:field>

                    <flux:field>
                        <flux:label class="text-xs font-black tracking-widest text-slate-500 uppercase">End Date</flux:label>
                        <flux:input type="date" wire:model="attachEndDate" />
                        <flux:error name="attachEndDate" />
                    </flux:field>
                </div>
                @endif
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <flux:button x-on:click="$dispatch('modal-close', { name: 'attach-employee-modal' })" variant="ghost">Cancel</flux:button>
                @if($attachEmployeeName)
                <flux:button type="submit" variant="primary">Attach Employee</flux:button>
                @endif
            </div>
        </form>
    </flux:modal>
</div>