<div class="space-y-8">
    <section>
        {{-- Section Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
                <h2 class="text-base font-black tracking-widest text-slate-700 dark:text-zinc-200 uppercase">Personal Profile</h2>
                <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-[0.2em] mt-0.5">Identity & Cultural Background</p>
            </div>
            @if($canEdit)
                <flux:modal.trigger name="edit-profile-personal-info">
                    <flux:button variant="ghost" size="sm" class="rounded-xl border border-slate-300 dark:border-zinc-700 font-bold text-xs px-5 text-slate-600 dark:text-zinc-300 hover:border-indigo-400 hover:text-indigo-600 transition-all w-fit">
                        Edit Details
                    </flux:button>
                </flux:modal.trigger>
            @endif
        </div>

        {{-- Data Table --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-300 dark:border-zinc-700 overflow-hidden">

            {{-- Full Name --}}
            <div class="flex flex-col sm:flex-row sm:items-start border-b border-dashed border-slate-300 dark:border-zinc-700 px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors group gap-1 sm:gap-0">
                <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest sm:pt-0.5">Full Name</span>
                <span class="text-sm font-semibold text-slate-800 dark:text-zinc-100 leading-relaxed">{{ $employee->full_name }}</span>
            </div>

            {{-- Name with Initials --}}
            <div class="flex flex-col sm:flex-row sm:items-start border-b border-dashed border-slate-300 dark:border-zinc-700 px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors group gap-1 sm:gap-0">
                <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest sm:pt-0.5">Name with Initials</span>
                <span class="text-sm font-semibold text-slate-800 dark:text-zinc-100 leading-relaxed">{{ $employee->name_with_initials }}</span>
            </div>

            {{-- Date of Birth --}}
            <div class="flex flex-col sm:flex-row sm:items-center border-b border-dashed border-slate-300 dark:border-zinc-700 px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors group gap-1 sm:gap-0">
                <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Date of Birth</span>
                <span class="text-sm font-semibold text-slate-800 dark:text-zinc-100">{{ \Carbon\Carbon::parse($employee->date_of_birth)->format('Y-m-d') }}</span>
            </div>

            {{-- Gender --}}
            <div class="flex flex-col sm:flex-row sm:items-center border-b border-dashed border-slate-300 dark:border-zinc-700 px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors group gap-1 sm:gap-0">
                <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Gender</span>
                <span class="text-sm font-semibold text-slate-800 dark:text-zinc-100">{{ $employee->gender->gender_name }}</span>
            </div>

            {{-- Religion --}}
            <div class="flex flex-col sm:flex-row sm:items-center border-b border-dashed border-slate-300 dark:border-zinc-700 px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors group gap-1 sm:gap-0">
                <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Religion</span>
                <span class="text-sm font-semibold text-slate-800 dark:text-zinc-100">{{ $employee->religion->religion_name }}</span>
            </div>

            {{-- Ethnicity --}}
            <div class="flex flex-col sm:flex-row sm:items-center border-b border-dashed border-slate-300 dark:border-zinc-700 px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors group gap-1 sm:gap-0">
                <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Ethnicity</span>
                <span class="text-sm font-semibold text-slate-800 dark:text-zinc-100">{{ $employee->ethnicity->ethnicity_name }}</span>
            </div>

            {{-- Civil Status --}}
            <div class="flex flex-col sm:flex-row sm:items-center px-6 py-4 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors group gap-1 sm:gap-0">
                <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Civil Status</span>
                <span class="text-sm font-semibold text-slate-800 dark:text-zinc-100">{{ $employee->civilStatus->civil_status_name }}</span>
            </div>

        </div>
    </section>

    {{-- Edit Modal --}}
    @if($canEdit)
        <flux:modal wire:model="showModalPersonalInfo" name="edit-profile-personal-info" class="md:w-150">
            <div class="space-y-8">
                <div>
                    <h3 class="text-sm font-black tracking-widest text-slate-900 dark:text-white uppercase">Update Profile</h3>
                    <p class="text-[10px] font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest mt-0.5">Personal & Identity Records</p>
                </div>

                <form wire:submit.prevent="editPersonalInfo" class="grid grid-cols-1 gap-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:input label="NIC Number" icon="identification" wire:model.live="nic" class="font-bold" />
                        <flux:select label="Title" wire:model.live="title" class="font-bold">
                            @foreach ($titleOptions as $data)
                                <option value="{{ $data->title_id }}">{{ $data->title_name }}</option>
                            @endforeach
                        </flux:select>
                    </div>

                    <flux:input label="Full Name" wire:model.live="fullName" class="font-bold" />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:select label="Gender" wire:model.live="gender" class="font-bold">
                            @foreach ($genderOptions as $data)
                                <option value="{{ $data->gender_id }}">{{ $data->gender_name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:input type="date" label="Birthday" wire:model.live="birthday" class="font-bold" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:select label="Ethnicity" wire:model.live="ethnicity" class="font-bold">
                            @foreach ($ethnicityOptions as $data)
                                <option value="{{ $data->ethnicity_id }}">{{ $data->ethnicity_name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:select label="Religion" wire:model.live="religion" class="font-bold">
                            @foreach ($religionOptions as $data)
                                <option value="{{ $data->religion_id }}">{{ $data->religion_name }}</option>
                            @endforeach
                        </flux:select>
                    </div>

                    <flux:select label="Civil Status" wire:model.live="civilStatus" class="font-bold">
                        @foreach ($civilStatusOptions as $data)
                            <option value="{{ $data->civil_status_id }}">{{ $data->civil_status_name }}</option>
                        @endforeach
                    </flux:select>

                    <div class="flex gap-4 pt-4">
                        <flux:modal.close>
                            <flux:button variant="ghost" class="flex-1 font-bold rounded-xl h-12">Cancel</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="flex-1 font-black rounded-xl h-12 bg-indigo-600 dark:bg-white text-white dark:text-slate-900 hover:scale-[1.02] active:scale-95 transition-all">
                            Save Profile Changes
                        </flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>
    @endif

</div>