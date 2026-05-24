<div class="space-y-8">
    <section>
        {{-- Section Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
                <h2 class="text-base font-black tracking-widest text-slate-700 dark:text-zinc-200 uppercase">Health Information</h2>
                <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-[0.2em] mt-0.5">Medical Overview & Vitals</p>
            </div>
            @if($canEdit)
            <flux:modal.trigger name="edit-profile-health-info">
                <flux:button variant="ghost" size="sm" class="rounded-xl border border-slate-300 dark:border-zinc-700 font-bold text-xs px-5 text-slate-600 dark:text-zinc-300 hover:border-rose-400 hover:text-rose-600 transition-all w-fit">
                    Edit Details
                </flux:button>
            </flux:modal.trigger>
            @endif
        </div>

        {{-- Data Table --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-300 dark:border-zinc-700 overflow-hidden">

            {{-- Blood Group --}}
            <div class="flex flex-col sm:flex-row sm:items-center border-b border-dashed border-slate-300 dark:border-zinc-700 px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors gap-2 sm:gap-0">
                <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Blood Group</span>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center min-w-[40px] h-10 px-3 rounded-xl bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 text-sm font-black">
                        {{ $employee->bloodGroup->blood_group ?? 'N/A' }}
                    </span>
                    <div class="flex items-center gap-1.5 text-rose-500 text-[10px] font-black uppercase tracking-widest shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                        Priority Medical Data
                    </div>
                </div>
            </div>

            {{-- Health Status --}}
            <div class="flex flex-col sm:flex-row sm:items-center border-b border-dashed border-slate-300 dark:border-zinc-700 px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors gap-2 sm:gap-0">
                <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Overall Condition</span>
                <div class="flex items-center gap-2.5">
                    @if($employee->health_condition == 1)
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">{{ $employee->health_status }}</span>
                    @else
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span class="text-sm font-semibold text-amber-600 dark:text-amber-400">{{ $employee->health_status }}</span>
                    @endif
                </div>
            </div>

            {{-- Medical Observations --}}
            <div class="flex flex-col sm:flex-row sm:items-start px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors gap-1 sm:gap-0">
                <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest sm:pt-0.5">Medical Observations</span>
                <span class="text-sm font-semibold text-slate-800 dark:text-zinc-100 leading-relaxed">
                    {{ $employee->health_problem ?? 'No reported medical conditions' }}
                </span>
            </div>

        </div>
    </section>

    {{-- Edit Modal --}}
    @if($canEdit)
    <flux:modal wire:model="showModalHealthInfo" name="edit-profile-health-info" class="md:w-130">
        <div class="space-y-8">
            <div>
                <h3 class="text-sm font-black tracking-widest text-slate-900 dark:text-white uppercase">Health Profile</h3>
                <p class="text-[10px] font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest mt-0.5">Medical status and vitals</p>
            </div>

            <form wire:submit.prevent="editHealthInfo" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:select label="Blood Group" icon="beaker" wire:model.live="bloodGroup" class="font-bold">
                        <option value="">Select</option>
                        @foreach ($bloodGroupOptions as $data)
                        <option value="{{ $data->blood_group_id }}">{{ $data->blood_group }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select label="Current Health Status" wire:model.live="healthCondition" class="font-bold">
                        <option value="">Select</option>
                        @foreach ($healthConditionOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </flux:select>
                </div>

                @if ($healthCondition === '0')
                <div class="animate-in fade-in slide-in-from-top-4 duration-500">
                    <flux:textarea
                        label="Medical Details"
                        description="Please describe any ongoing health concerns."
                        wire:model.live="healthProblem"
                        placeholder="Enter specific medical details..."
                        rows="4"
                        class="font-bold" />
                </div>
                @endif

                <div class="flex gap-4 pt-4">
                    <flux:modal.close>
                        <flux:button variant="ghost" class="flex-1 font-bold rounded-xl h-12">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary" class="flex-1 font-black rounded-xl h-12 bg-indigo-600 dark:bg-white text-white dark:text-slate-900 hover:scale-[1.02] active:scale-95 transition-all">
                        Save Health Records
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
    @endif
</div>