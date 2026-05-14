<div>
    <section>
        {{-- Header: Clean and Minimal --}}
        <div class="flex items-center justify-between mb-5 px-1">
            <div>
                <h2 class="text-xl font-extrabold tracking-tight text-gray-900 dark:text-white">Health Information</h2>
                <p class="text-sm text-gray-500">Medical overview and vitals</p>
            </div>
            @if($canEdit)
                <flux:modal.trigger name="edit-profile-health-info">
                    <flux:button variant="ghost" icon="pencil-square" class="rounded-full">
                        Edit Details
                    </flux:button>
                </flux:modal.trigger>
            @endif
        </div>

        <div class="space-y-4">
            {{-- Primary Health Card: Blood Group --}}
            <div class="bg-gradient-to-br from-white to-red-50/30 dark:from-gray-800 dark:to-red-900/10 p-5 rounded-2xl border border-red-100 dark:border-gray-700 shadow-sm relative overflow-hidden">
                {{-- Subtle decorative blood drop background icon --}}
                <div class="absolute -right-2 -top-2 opacity-5 dark:opacity-10">
                    <flux:icon.beaker class="size-24 text-red-600" />
                </div>

                <div class="flex items-center gap-4 relative z-10">
                    <div class="p-3 bg-red-100 dark:bg-red-900/40 rounded-xl shadow-inner">
                        <span class="text-xl font-black text-red-600 dark:text-red-400">
                            {{ $employee->bloodGroup->blood_group }}
                        </span>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Blood Group Type</p>
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Emergency Vital Information</p>
                    </div>
                </div>
            </div>

            {{-- Secondary Info Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                {{-- Overall Condition --}}
                <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm flex items-start gap-3">
                    <div class="mt-1">
                        @if($employee->health_status == 'Healthy') {{-- Adjust logic based on your actual value --}}
                            <div class="size-2.5 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]"></div>
                        @else
                            <div class="size-2.5 rounded-full bg-amber-500"></div>
                        @endif
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Overall Condition</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wide">
                            {{ $employee->health_status }}
                        </p>
                    </div>
                </div>

                {{-- Known Problems --}}
                <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Known Problems</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 leading-tight">
                                {{ $employee->health_problem ?? 'No reported medical conditions' }}
                            </p>
                        </div>
                        <flux:icon.shield-check class="size-4 text-gray-300 dark:text-gray-600" />
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Edit Modal --}}
    @if($canEdit)
        <flux:modal wire:model="showModalHealthInfo" name="edit-profile-health-info" class="md:w-110">
            <div class="space-y-6">
                <flux:heading size="lg" badge="Medical">Health Details</flux:heading>

                <form wire:submit.prevent="editHealthInfo" class="space-y-5">
                    @csrf
                    
                    <flux:field>
                        <flux:select label="Blood Group" icon="beaker" wire:model.live="bloodGroup">
                            <option value="">Select</option>
                            @foreach ($bloodGroupOptions as $data)
                                <option value="{{ $data->blood_group_id }}">{{ $data->blood_group }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>

                    <flux:field>
                        <flux:select label="Current Health Status" wire:model.live="healthCondition">
                            <option value="">Select Health Condition</option>
                            @foreach ($healthConditionOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>

                    @if ($healthCondition == false)
                        <div class="animate-in fade-in slide-in-from-top-2 duration-300">
                            <flux:textarea 
                                label="Medical Details"
                                description="Please describe any ongoing health concerns."
                                wire:model.live="healthProblem" 
                                placeholder="Enter details..."
                                rows="3" 
                            />
                        </div>
                    @endif

                    <div class="flex gap-3 pt-4">
                        <flux:modal.close>
                            <flux:button variant="ghost" class="flex-1">Cancel</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-1">Save Changes</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>
    @endif
</div>