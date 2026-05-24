<section class="mt-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-sm font-black text-slate-700 dark:text-zinc-100 uppercase tracking-widest">Position History</h3>
            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-tight mt-0.5">Chronological record of official positions held</p>
        </div>

        @if($canCreate)
        <flux:modal.trigger name="add-position-history">
            <flux:button variant="ghost" size="sm" icon="plus" class="rounded-xl border border-slate-300 dark:border-zinc-700 font-bold text-xs px-5 text-slate-600 dark:text-zinc-300 hover:border-indigo-400 hover:text-indigo-600 transition-all">Add History</flux:button>
        </flux:modal.trigger>
        @endif
    </div>

    {{-- Position History Modal --}}
    <flux:modal name="add-position-history" class="min-w-md space-y-6">
        <div>
            <h3 class="text-sm font-black tracking-widest text-slate-900 dark:text-white uppercase">Add Position History</h3>
            <p class="text-[10px] font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest mt-0.5">Record a previous position held by this employee.</p>
        </div>
        <form wire:submit="savePositionHistory" class="space-y-4">
            <flux:select label="Associated Service / Appointment" wire:model.live="appointmentId">
                <option value="">Select Appointment</option>
                @foreach($appointments as $app)
                <option value="{{ $app->appointment_id }}">{{ $app->service->service_name ?? 'Unknown Service' }} ({{ optional($app->first_appointment_date)->format('Y') ?? 'N/A' }})</option>
                @endforeach
            </flux:select>

            <flux:select label="Position" wire:model="positionId">
                <option value="">Select Position</option>
                @foreach ($positions as $pos)
                <option value="{{ $pos->position_id }}">{{ $pos->position_name }}</option>
                @endforeach
            </flux:select>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <flux:input type="date" label="Start Date" wire:model="startDate" />
                <flux:input type="date" label="End Date" wire:model="endDate" />
            </div>

            <flux:input label="Reference Letter No" wire:model="refLetterNo" placeholder="e.g. ED/POS/2023/12" />

            <flux:textarea label="Remarks" wire:model="remarks" rows="2" />

            <div class="flex justify-end gap-3 pt-4">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Save History</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- History List --}}
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-300 dark:border-zinc-700 overflow-hidden shadow-sm">
        <div x-data="{ activePosition: null }" class="divide-y divide-slate-100 dark:divide-zinc-800">
            @forelse($positionHistory as $history)
            <div class="group">
                {{-- Position Trigger --}}
                <div
                    x-on:click="activePosition = activePosition === {{ $history->id }} ? null : {{ $history->id }}"
                    class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 p-4 sm:p-5 cursor-pointer hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors">

                    {{-- Position Info --}}
                    <div class="flex-1 min-w-0 w-full">
                        <div class="flex items-center gap-3 mb-1">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center shrink-0">
                                <flux:icon.briefcase class="size-4 text-emerald-500" />
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-slate-800 dark:text-zinc-100 uppercase tracking-tight leading-tight">
                                    {{ $history->position->position_name ?? 'Unknown Position' }}
                                </h4>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[10px] font-bold text-emerald-600/70 uppercase tracking-widest">{{ $history->appointment->service->service_name ?? 'N/A' }}</span>
                                    @if($history->is_active)
                                    <span class="text-slate-300 dark:text-zinc-600">•</span>
                                    <span class="inline-flex px-1.5 py-0.5 rounded text-[8px] font-black bg-emerald-50 text-emerald-600 uppercase border border-emerald-100">Current</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Period & Chevron --}}
                    <div class="flex items-center justify-between w-full lg:w-auto gap-4 mt-3 lg:mt-0" x-on:click.stop>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 lg:text-right">
                            <div class="flex flex-col lg:items-end">
                                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest leading-none mb-1">Duration</p>
                                <p class="text-[11px] font-mono font-bold text-slate-600 dark:text-zinc-300">
                                    {{ $history->start_date ? $history->start_date->format('Y-m-d') : 'Unknown' }} <span class="text-slate-300 mx-1">→</span> {{ $history->end_date ? $history->end_date->format('Y-m-d') : 'Present' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <flux:icon.chevron-down class="size-4 text-slate-500 transition-transform duration-300 cursor-pointer"
                                x-on:click.stop="activePosition = activePosition === {{ $history->id }} ? null : {{ $history->id }}"
                                x-bind:class="activePosition === {{ $history->id }} ? 'rotate-180' : ''" />
                        </div>
                    </div>
                </div>

                {{-- Accordion Content --}}
                <div x-show="activePosition === {{ $history->id }}" x-collapse x-cloak class="px-5 sm:px-16 py-4 border-t border-slate-200 dark:border-zinc-700/50 bg-slate-50/30 dark:bg-zinc-800/10">
                    <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 flex-1 w-full">
                            @if($history->ref_letter_no)
                            <div>
                                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">Reference Letter No</p>
                                <p class="font-mono font-bold text-sm text-slate-700 dark:text-zinc-300">{{ $history->ref_letter_no }}</p>
                            </div>
                            @endif

                            @if($history->remarks)
                            <div class="col-span-1 sm:col-span-2">
                                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">Remarks / Notes</p>
                                <p class="text-sm italic text-slate-600 dark:text-zinc-400 leading-relaxed">{{ $history->remarks }}</p>
                            </div>
                            @endif

                            @if(!$history->ref_letter_no && !$history->remarks)
                            <div class="col-span-1 sm:col-span-2 flex items-center gap-2 py-2">
                                <flux:icon.information-circle class="size-4 text-slate-500" />
                                <p class="text-xs text-slate-500 italic">No additional details recorded for this position.</p>
                            </div>
                            @endif
                        </div>

                        @if($canDelete && !$history->is_active)
                        <div class="shrink-0 mt-2 sm:mt-0 w-full sm:w-auto flex justify-end">
                            <flux:button icon="trash" variant="ghost" size="sm"
                                class="text-slate-500 hover:text-rose-500 rounded-lg shrink-0 transition-colors"
                                wire:click="confirmDelete({{ $history->id }})"></flux:button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-12">
                <flux:icon.briefcase class="size-8 text-slate-200 mb-3" />
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">No position history records found</p>
            </div>
            @endforelse
        </div>
    </div>

    <x-delete-confirmation 
        name="delete-position-history-confirmation" 
        wireAction="deleteHistory" 
        title="Delete Position History?" 
        description="Are you sure you want to delete this position history record? This action cannot be undone."
    />
</section>