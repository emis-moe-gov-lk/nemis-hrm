<div>
    <div class="space-y-8">

        @if ($employee->civil_status_id != 'C01')

        {{-- === SPOUSE SECTION === --}}
        <section>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                <div>
                    <h2 class="text-base font-black tracking-widest text-slate-700 dark:text-zinc-200 uppercase">Family Management</h2>
                    <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-[0.2em] mt-0.5">Spouse & Children Records</p>
                </div>
                @if ($canCreate)
                <flux:modal.trigger name="add-spouse-info">
                    <flux:button variant="ghost" size="sm" icon="plus" class="rounded-xl border border-slate-300 dark:border-zinc-700 font-bold text-xs px-5 text-slate-600 dark:text-zinc-300 hover:border-indigo-400 hover:text-indigo-600 transition-all w-fit">
                        Add Spouse
                    </flux:button>
                </flux:modal.trigger>
                @endif
            </div>

            {{-- Spouse Cards --}}
            <div class="space-y-6">
                @forelse ($familyList as $data)
                @php $spouse = $data->getSpousInfo($employee->people_id); @endphp

                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-300 dark:border-zinc-700 overflow-hidden">

                    {{-- Spouse Header --}}
                    <div class="flex flex-wrap items-center justify-between gap-2 px-6 py-4 bg-slate-50 dark:bg-zinc-800/50 border-b border-slate-300 dark:border-zinc-700">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center">
                                <flux:icon.user variant="micro" class="size-4 text-indigo-600 dark:text-indigo-400" />
                            </div>
                            <div>
                                <p class="text-sm font-black text-slate-900 dark:text-zinc-100">
                                    {{ $spouse->title->title_name ?? '' }} {{ $spouse->name_with_initials }}
                                </p>
                                <p class="text-[10px] font-mono text-slate-500 uppercase tracking-widest">Spouse Record</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <flux:badge size="sm" :variant="$data->active_status == 1 ? ($data->divorce_date ? 'danger' : 'success') : 'neutral'" class="uppercase text-[9px] font-black tracking-widest">
                                {{ $data->active_status == 1 ? ($data->divorce_date ? 'Divorce' : 'Active') : 'Inactive' }}
                            </flux:badge>
                            @if ($canDelete)
                            <flux:button icon="trash" variant="ghost" size="sm"
                                wire:click="confirmDeleteSpouse('{{ $data->family_id }}')"
                                class="text-slate-500 hover:text-rose-500 rounded-lg" />
                            @endif
                        </div>
                    </div>

                    {{-- Spouse Details - Row Style --}}
                    <div class="flex flex-col sm:flex-row sm:items-center border-b border-dashed border-slate-300 dark:border-zinc-700 px-6 py-3 hover:bg-slate-50/50 dark:hover:bg-zinc-800/20 transition-colors gap-1 sm:gap-0">
                        <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">NIC</span>
                        <span class="text-sm font-mono font-semibold text-slate-800 dark:text-zinc-100">{{ $spouse->nic }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center border-b border-dashed border-slate-300 dark:border-zinc-700 px-6 py-3 hover:bg-slate-50/50 dark:hover:bg-zinc-800/20 transition-colors gap-1 sm:gap-0">
                        <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Date of Birth</span>
                        <span class="text-sm font-semibold text-slate-800 dark:text-zinc-100">{{ $spouse->date_of_birth }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center border-b border-dashed border-slate-300 dark:border-zinc-700 px-6 py-3 hover:bg-slate-50/50 dark:hover:bg-zinc-800/20 transition-colors gap-1 sm:gap-0">
                        <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Married Date</span>
                        <span class="text-sm font-semibold text-slate-800 dark:text-zinc-100">{{ $data->married_date }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center px-6 py-3 hover:bg-slate-50/50 dark:hover:bg-zinc-800/20 transition-colors gap-1 sm:gap-0">
                        <span class="w-full sm:w-48 sm:shrink-0 text-[11px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Marriage Cert. No</span>
                        <span class="text-sm font-mono font-semibold text-slate-800 dark:text-zinc-100">{{ $data->married_cf_no }}</span>
                    </div>

                    {{-- Add Child action --}}
                    @if ($canCreate)
                    <div class="px-6 py-3 border-t border-slate-200 dark:border-zinc-700 bg-slate-50/30 dark:bg-zinc-800/20 flex justify-end">
                        @if ($data->active_status == 1 && !$data->divorce_date)
                        <flux:button wire:click="terminateRelationship('{{ $data->family_id }}')" variant="ghost" size="sm" icon="link-slash"
                            class="rounded-xl border border-slate-300 dark:border-zinc-700 font-bold text-xs px-5 hover:border-orange-400 hover:text-orange-600 transition-all mr-2">
                            Terminate
                        </flux:button>
                        @endif

                        @if ($data->active_status == 0)
                        <flux:button wire:click="reactivateRelationship('{{ $data->family_id }}')" variant="ghost" size="sm" icon="arrow-path"
                            class="rounded-xl border border-slate-300 dark:border-zinc-700 font-bold text-xs px-5 hover:border-emerald-400 hover:text-emerald-600 transition-all mr-2">
                            Reactivate
                        </flux:button>
                        @endif
                        @if ($data->active_status == 1 && !$data->divorce_date)
                        <flux:button wire:click="openDivorceModal('{{ $data->family_id }}')" variant="ghost" size="sm" icon="x-mark"
                            class="rounded-xl border border-slate-300 dark:border-zinc-700 font-bold text-xs px-5 hover:border-rose-400 hover:text-rose-600 transition-all mr-2">
                            Record Divorce
                        </flux:button>
                        @endif
                        <flux:button wire:click="openChildModal('{{ $data->family_id }}')" variant="ghost" size="sm" icon="plus"
                            class="rounded-xl border border-slate-300 dark:border-zinc-700 font-bold text-xs px-5 hover:border-indigo-400 hover:text-indigo-600 transition-all">
                            Add Child
                        </flux:button>
                    </div>
                    @endif
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-16 bg-white dark:bg-zinc-900 rounded-2xl border border-dashed border-slate-300 dark:border-zinc-700">
                    <div class="w-16 h-16 rounded-2xl bg-slate-50 dark:bg-zinc-800 flex items-center justify-center mb-4">
                        <flux:icon.users class="size-8 text-slate-300 dark:text-zinc-600" />
                    </div>
                    <p class="text-sm font-semibold text-slate-500 uppercase tracking-widest">No spouse records found</p>
                </div>
                @endforelse
            </div>
        </section>

        {{-- === CHILDREN SECTION === --}}
        <section>
            <div class="flex items-center gap-4 mb-6">
                <h2 class="text-base font-black tracking-widest text-slate-700 dark:text-zinc-200 uppercase whitespace-nowrap">Children's List</h2>
                <div class="h-px flex-1 bg-slate-200 dark:bg-zinc-800"></div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-300 dark:border-zinc-700 overflow-hidden">

                {{-- Children Table Header — desktop only --}}
                <div class="hidden sm:flex items-center bg-slate-50 dark:bg-zinc-800/50 border-b border-slate-300 dark:border-zinc-700 px-6 py-3">
                    <span class="flex-1 text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Name</span>
                    <span class="w-24 text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Gender</span>
                    <span class="w-32 text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Date of Birth</span>
                    <span class="w-32 text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest">BC No</span>
                    @if($canDelete)<span class="w-10"></span>@endif
                </div>

                @forelse($familyMemberList as $child)
                <div class="flex flex-col sm:flex-row sm:items-center border-b border-dashed border-slate-300 dark:border-zinc-700 last:border-b-0 px-6 py-3.5 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors group gap-2 sm:gap-0">
                    <div class="flex-1 flex items-center gap-3">
                        <div class="w-7 h-7 flex items-center justify-center rounded-lg bg-indigo-50 dark:bg-indigo-900/20 shrink-0">
                            <flux:icon.user variant="micro" class="size-3.5 text-indigo-500" />
                        </div>
                        <span class="text-sm font-semibold text-slate-800 dark:text-zinc-100">{{ $child->child_name }}</span>
                    </div>

                    {{-- Gender + DOB + BC No: inline on mobile, fixed columns on desktop --}}
                    <div class="flex items-center gap-3 sm:contents">
                        <div class="sm:w-24 sm:shrink-0">
                            <span class="sm:hidden text-[9px] font-black text-slate-500 uppercase tracking-widest"></span>
                            <span class="text-[10px] font-black uppercase tracking-widest text-indigo-500">{{ $child->gender->gender_name ?? 'N/A' }}</span>
                        </div>
                        <div class="sm:w-32 sm:shrink-0">
                            <span class="sm:hidden text-[9px] font-black text-slate-500 uppercase tracking-widest">DOB: </span>
                            <span class="text-sm font-semibold text-slate-600 dark:text-zinc-300">{{ $child->date_of_birth }}</span>
                        </div>
                        <div class="sm:w-32 sm:shrink-0">
                            <span class="sm:hidden text-[9px] font-black text-slate-500 uppercase tracking-widest">BC: </span>
                            <span class="text-sm font-mono font-semibold text-slate-600 dark:text-zinc-300">{{ $child->birth_fc_no }}</span>
                        </div>
                    </div>

                    @if ($canDelete)
                    <div class="w-10 shrink-0 flex justify-end">
                        <flux:button wire:click="confirmDeleteChild('{{ $child->id }}')"
                            variant="ghost" icon="trash" size="sm"
                            class="opacity-0 group-hover:opacity-100 transition-opacity text-slate-500 hover:text-rose-500 rounded-lg" />
                    </div>
                    @endif
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-12 px-6">
                    <p class="text-sm font-semibold text-slate-500 uppercase tracking-widest">No children records found</p>
                </div>
                @endforelse
            </div>
        </section>

        @else
        {{-- Single Status --}}
        <div class="flex flex-col items-center justify-center py-20 bg-white dark:bg-zinc-900 rounded-2xl border border-dashed border-slate-300 dark:border-zinc-700">
            <div class="w-16 h-16 rounded-2xl bg-blue-50 dark:bg-blue-900/10 flex items-center justify-center mb-4">
                <flux:icon.information-circle class="size-8 text-blue-400 dark:text-blue-500" />
            </div>
            <p class="text-sm font-black text-slate-700 dark:text-zinc-300 uppercase tracking-widest">Civil Status: Single</p>
            <p class="text-[11px] text-slate-500 dark:text-zinc-400 mt-2 font-semibold uppercase tracking-widest text-center max-w-xs">
                Family management is only available for married or other civil statuses.
            </p>
        </div>
        @endif

    </div>

    {{-- Edit general information --}}
    @if ($canCreate)
    <flux:modal wire:model="showModalSpouseReg" name="add-spouse-info" class="md:w-150">
        <div class="space-y-6">
            <div class="pb-4 border-b border-slate-200 dark:border-zinc-700">
                <h3 class="text-sm font-black tracking-widest text-slate-900 dark:text-white uppercase">Spouse Register</h3>
                <p class="text-[10px] font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest mt-0.5">Register spouse details and marriage information</p>
            </div>

            <div>
                @if (session('error'))
                <x-alert type="error" dismissible class="mb-4">
                    {{ session('error') }}
                </x-alert>
                @endif

                @if (session('warning'))
                <x-alert type="warning" dismissible class="mb-4">
                    {{ session('warning') }}
                </x-alert>
                @endif

                @if (session('info'))
                <x-alert type="info" dismissible class="mb-4">
                    {{ session('info') }}
                </x-alert>
                @endif
            </div>

            <form wire:submit.prevent="spouseReg">
                @csrf
                <div class="mt-6 max-w-xl space-y-4">

                    <flux:field label="National Identity Card (NIC)">

                        <div class="flex items-end gap-2">
                            <flux:input id="nic" name="nic" wire:model.live="nic" placeholder="Enter NIC"
                                class="w-full" />

                            <flux:button type="button" icon="check" variant="primary" wire:click="checkNIC"
                                class="whitespace-nowrap">
                                Check
                            </flux:button>
                        </div>

                        <!-- IMPORTANT: error MUST be inside flux:field, after the input -->
                        @error('nic')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror

                    </flux:field>


                    @if ($isCheckNIC)
                    @if ($isPeopleFound)
                    <div class=" p-2 bg-gray-100">
                        <p>
                            <span>This NIC number is already registered in the information system. If the
                                information is correct, follow the next steps.</span><br />
                            <span class=" text-xs">මෙම NIC අංකය මේ වනවිටත් තොරතුරු පද්ධතියේ ලියාපදිංචි වී පවතී. තොරතුරු
                                නිවැරදිනම් ඉදිරි පියවර අනුගමනය කරන්න.</span>
                        </p>
                        <div class=" mt-3">
                            <p>NIC: {{ $peopleData->nic }}</p>
                            <p>Name: {{ $peopleData->name_with_initials }}</p>
                            <p>Date of Birth: {{ $peopleData->date_of_birth }}</p>
                        </div>
                    </div>
                    @else
                    <div class="mt-6 max-w-xl space-y-4">
                        <div class="flex w-full flex-col md:flex-row gap-4">
                            <div class="w-1/5">
                                <flux:field>
                                    <flux:select label="Title" wire:model.live="title">
                                        <option value="">Select</option>
                                        @foreach ($titleOptions as $data)
                                        <option value="{{ $data->title_id }}">{{ $data->title_name }}
                                        </option>
                                        @endforeach
                                    </flux:select>
                                </flux:field>
                            </div>

                            <div class="w-4/5">
                                <flux:field>
                                    <flux:input label="Full Name" wire:model.live="fullName"
                                        placeholder="Enter full name" />
                                </flux:field>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <!-- Gender -->
                            <div class="w-1/2">
                                <flux:field>
                                    <flux:select label="Gender" wire:model.live="gender">
                                        <option value="">Select</option>
                                        @foreach ($genderOptions as $data)
                                        <option value="{{ $data->gender_id }}">{{ $data->gender_name }}
                                        </option>
                                        @endforeach
                                    </flux:select>
                                </flux:field>
                            </div>

                            <!-- Birthday -->
                            <div class="w-1/2">
                                <flux:field>
                                    <flux:input type="date" label="Birthday" wire:model.live="birthday" />
                                </flux:field>
                            </div>
                        </div>

                        <div class="flex flex-col md:flex-row gap-4">
                            <!-- Ethnicity -->
                            <div class="md:w-1/2 w-full">
                                <flux:field>
                                    <flux:select label="Ethnicity" wire:model.live="ethnicity">
                                        <option value="">Select</option>
                                        @foreach ($ethnicityOptions as $data)
                                        <option value="{{ $data->ethnicity_id }}">
                                            {{ $data->ethnicity_name }}
                                        </option>
                                        @endforeach
                                    </flux:select>
                                </flux:field>
                            </div>

                            <!-- Religion Status -->
                            <div class="md:w-1/2 w-full">
                                <flux:field>
                                    <flux:select label="Religion" wire:model.live="religion">
                                        <option value="">Select Religion</option>
                                        @foreach ($religionOptions as $data)
                                        <option value="{{ $data->religion_id }}">
                                            {{ $data->religion_name }}
                                        </option>
                                        @endforeach
                                    </flux:select>
                                </flux:field>
                            </div>
                        </div>

                        <flux:field>
                            <flux:input label="Contact" wire:model.live="contact"
                                placeholder="Enter Contact (10 digits)" />
                        </flux:field>

                        <flux:field>
                            <flux:input label="Email" type="email" wire:model.live="email"
                                placeholder="Enter email" />
                        </flux:field>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:input type="date" label="Married date" wire:model.live="marriedDate" />
                        </flux:field>
                        <flux:field>
                            <flux:input type="text" label="Married certificate no" wire:model.live="marriedCfNo" />
                        </flux:field>
                    </div>

                    <div class="flex justify-end mt-4">
                        <flux:button type="submit" variant="primary" class="font-black rounded-xl">Save changes</flux:button>
                    </div>
                    @endif
                </div>


            </form>
        </div>
    </flux:modal>
    @endif

    @if ($canCreate)
    <flux:modal wire:model="showModalChildReg" name="reg-children" class="md:w-100">
        <div class="space-y-4">
            <div class="pb-4 border-b border-slate-200 dark:border-zinc-700">
                <h3 class="text-sm font-black tracking-widest text-slate-900 dark:text-white uppercase">Children's Register</h3>
                <p class="text-[10px] font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest mt-0.5">Add child details to the family record</p>
            </div>

            <div>
                @if (session('error'))
                <x-alert type="error" dismissible class="mb-4">
                    {{ session('error') }}
                </x-alert>
                @endif

                @if (session('warning'))
                <x-alert type="warning" dismissible class="mb-4">
                    {{ session('warning') }}
                </x-alert>
                @endif

                @if (session('info'))
                <x-alert type="info" dismissible class="mb-4">
                    {{ session('info') }}
                </x-alert>
                @endif
            </div>

            <form wire:submit.prevent="childReg" class="space-y-4">
                @csrf

                <flux:input label="Full name" wire:model.live="childName" placeholder="Name" />

                <flux:input label="Date of birth" wire:model.live="childDob" type="date" />

                <flux:field>
                    <flux:select label="Gender" wire:model.live="childGender">
                        <option value="">Select</option>
                        @foreach ($genderOptions as $data)
                        <option value="{{ $data->gender_id }}">{{ $data->gender_name }}</option>
                        @endforeach
                    </flux:select>
                </flux:field>

                <flux:input label="Birth certificate number" wire:model.live="birthCertificateNo" placeholder="254755" />

                <flux:field>
                    <flux:select label="Healthy?" wire:model.live="chailHealthCondition">
                        <option value="">Select Health Condition</option>
                        @foreach ($healthConditionOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </flux:select>
                </flux:field>

                <div class="flex justify-end">
                    <flux:button type="submit" variant="primary" class="font-black rounded-xl">Save changes</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
    @endif
    {{-- Divorce Modal --}}
    <flux:modal wire:model="showModalDivorce" class="min-w-88 space-y-6">
        <div class="space-y-1">
            <flux:heading size="lg">Record Divorce</flux:heading>
            <flux:subheading>Please provide the official divorce date for this family record.</flux:subheading>
        </div>

        <div class="space-y-4">
            <flux:field label="Divorce Date">
                <flux:input wire:model="divorceDate" type="date" />
                @error('divorceDate') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            </flux:field>
        </div>

        <div class="flex justify-end gap-2 pt-2">
            <flux:modal.close>
                <flux:button variant="ghost" class="font-bold">Cancel</flux:button>
            </flux:modal.close>
            <flux:button wire:click="recordDivorce" variant="primary" class="font-black rounded-xl">Confirm Divorce</flux:button>
        </div>
    </flux:modal>

    {{-- Delete Modals --}}
    <x-delete-confirmation 
        name="delete-spouse-confirmation" 
        wireAction="deleteSpouse" 
        title="Delete Spouse Record?" 
        description="Are you sure you want to delete this spouse record? This will permanently remove all associated family history."
    />

    <x-delete-confirmation 
        name="delete-child-confirmation" 
        wireAction="deleteChild" 
        title="Delete Child Record?" 
        description="Are you sure you want to delete this child's record? This action cannot be undone."
    />
</div>