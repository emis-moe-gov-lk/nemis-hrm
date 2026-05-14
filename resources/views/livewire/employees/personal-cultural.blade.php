<div>
    <section>
        {{-- Header: Shifted to a more minimal style --}}
        <div class="flex items-center justify-between mb-5 px-1">
            <div>
                <h2 class="text-xl font-extrabold tracking-tight text-gray-900 dark:text-white">Personal Profile</h2>
                <p class="text-sm text-gray-500">Identity and cultural details</p>
            </div>
            @if($canEdit)
                <flux:modal.trigger name="edit-profile-personal-info">
                    <flux:button variant="ghost" icon="pencil-square" class="rounded-full">
                        Edit Details
                    </flux:button>
                </flux:modal.trigger>
            @endif
        </div>

        <div class="space-y-4">
            {{-- Primary Identity Card --}}
            <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon.user class="size-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Legal Full Name</p>
                            <p class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $employee->full_name }}</p>
                        </div>
                    </div>
                    
                    <div class="h-px bg-gray-100 dark:bg-gray-700 w-full"></div>

                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                            <flux:icon.identification class="size-5 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Display Name</p>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $employee->name_with_initials }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Secondary Info Grid: 2 columns on mobile --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Birth Date</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $employee->date_of_birth }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Gender</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $employee->gender->gender_name }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Religion</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $employee->religion->religion_name }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Ethnicity</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $employee->ethnicity->ethnicity_name }}</p>
                </div>

                <div class="col-span-2 bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase">Civil Status</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $employee->civilStatus->civil_status_name }}</p>
                    </div>
                    <flux:icon.heart class="size-5 text-pink-500/50" />
                </div>
            </div>
        </div>
    </section>

    {{-- Mobile-First Modal (Bottom Sheet Style on Mobile) --}}
    @if($canEdit)
        <flux:modal wire:model="showModalPersonalInfo" name="edit-profile-personal-info" class="md:w-125">
            <div class="space-y-6">
                <flux:heading size="lg" badge="Action Required">Update Info</flux:heading>

                <form wire:submit.prevent="editPersonalInfo" class="grid grid-cols-1 gap-y-5">
                    @csrf
                    
                    <flux:input label="NIC Number" icon="identification" wire:model.live="nic" />

                    <div class="flex gap-3">
                        <div class="w-1/3">
                            <flux:select label="Title" wire:model.live="title">
                                @foreach ($titleOptions as $data)
                                    <option value="{{ $data->title_id }}">{{ $data->title_name }}</option>
                                @endforeach
                            </flux:select>
                        </div>
                        <div class="w-2/3">
                            <flux:input label="Full Name" wire:model.live="fullName" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <flux:select label="Gender" wire:model.live="gender">
                            @foreach ($genderOptions as $data)
                                <option value="{{ $data->gender_id }}">{{ $data->gender_name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:input type="date" label="Birthday" wire:model.live="birthday" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <flux:select label="Ethnicity" wire:model.live="ethnicity">
                            @foreach ($ethnicityOptions as $data)
                                <option value="{{ $data->ethnicity_id }}">{{ $data->ethnicity_name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:select label="Religion" wire:model.live="religion">
                            @foreach ($religionOptions as $data)
                                <option value="{{ $data->religion_id }}">{{ $data->religion_name }}</option>
                            @endforeach
                        </flux:select>
                    </div>

                    <flux:select label="Civil Status" wire:model.live="civilStatus">
                        @foreach ($civilStatusOptions as $data)
                            <option value="{{ $data->civil_status_id }}">{{ $data->civil_status_name }}</option>
                        @endforeach
                    </flux:select>

                    <div class="flex gap-3 pt-2">
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