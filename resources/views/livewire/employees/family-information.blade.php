<div>
    <div class="antialiased min-h-screen pb-12">
        {{-- Main Container --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
            
            @if ($employee->civil_status_id != 'C01')
                <div class="space-y-10">
                    {{-- 1. Spouse Section --}}
                    <section>
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Family Management</h2>
                                <p class="text-sm text-gray-500">Manage spouse details and children records.</p>
                            </div>
                            @if ($canCreate)
                                <flux:modal.trigger name="add-spouse-info">
                                    <flux:button variant="primary" icon="plus">Add Spouse</flux:button>
                                </flux:modal.trigger>
                            @endif
                        </div>

                        {{-- Spouse Grid (Horizontal Layout) --}}
                        <div class="grid grid-cols-1 gap-6">
                            @forelse ($familyList as $data)
                                @php $spouse = $data->getSpousInfo($employee->people_id); @endphp
                                <div class="flex flex-col md:flex-row bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden transition-all hover:shadow-md">
                                    
                                    {{-- Left Branding/Status Side --}}
                                    <div class="md:w-56 bg-gray-50 dark:bg-gray-900/50 p-6 flex flex-col items-center justify-center border-b md:border-b-0 md:border-r border-gray-100 dark:border-gray-700">
                                        <div class="relative">
                                            <div class="p-5 bg-white dark:bg-gray-800 rounded-2xl shadow-sm text-indigo-600 dark:text-indigo-400">
                                                <flux:icon name="user" variant="outline" size="xl" />
                                            </div>
                                            <div class="absolute -bottom-2 -right-2">
                                                <flux:badge size="sm" :variant="$data->active_status == 1 ? 'success' : 'neutral'" class="shadow-sm">
                                                    {{ $data->active_status == 1 ? 'Active' : 'Inactive' }}
                                                </flux:badge>
                                            </div>
                                        </div>
                                        <div class="mt-4 text-center">
                                            <p class="text-[10px] font-mono text-gray-400 uppercase tracking-widest">Spouse Record</p>
                                        </div>
                                    </div>

                                    {{-- Right Details Side --}}
                                    <div class="flex-1 p-6 flex flex-col">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                                    {{ $spouse->title->title_name ?? '' }} {{ $spouse->name_with_initials }}
                                                </h3>
                                                <p class="text-sm font-mono text-gray-500 mt-1 uppercase">NIC: {{ $spouse->nic }}</p>
                                            </div>
                                            @if ($canDelete)
                                                <flux:button icon="trash" variant="danger" size="sm" variant="ghost"
                                                    wire:click="deleteSpouse('{{ $data->family_id }}')" 
                                                    onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" />
                                            @endif
                                        </div>

                                        <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-6">
                                            <div class="space-y-1">
                                                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Date of Birth</p>
                                                <p class="text-sm font-medium dark:text-gray-300">{{ $spouse->date_of_birth }}</p>
                                            </div>
                                            <div class="space-y-1">
                                                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Married Date</p>
                                                <p class="text-sm font-medium dark:text-gray-300">{{ $data->married_date }}</p>
                                            </div>
                                            <div class="space-y-1">
                                                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Marriage Cert.</p>
                                                <p class="text-sm font-medium dark:text-gray-300">{{ $data->married_cf_no }}</p>
                                            </div>
                                        </div>

                                        <div class="mt-auto pt-6 flex items-center justify-end border-t border-gray-50 dark:border-gray-700/50 mt-6">
                                            @if ($canCreate)
                                                <flux:button wire:click="openChildModal('{{ $data->family_id }}')" variant="primary" size="sm" icon="plus">
                                                    Add Child to this Spouse
                                                </flux:button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full flex flex-col items-center justify-center p-12 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-3xl">
                                    <flux:icon name="users" class="text-gray-300 dark:text-gray-600 mb-4" size="xl" />
                                    <p class="text-gray-500 dark:text-gray-400">No spouses found.</p>
                                </div>
                            @endforelse
                        </div>
                    </section>

                    {{-- 2. Children Section --}}
                    <section>
                        <div class="mb-6 flex items-center gap-4">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white whitespace-nowrap">Children's List</h2>
                            <div class="h-px w-full bg-gray-100 dark:bg-gray-800"></div>
                        </div>

                        <div class="space-y-3">
                            @forelse($familyMemberList as $child)
                                <div class="group flex items-center justify-between p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl hover:shadow-sm transition-all">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 flex items-center justify-center rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                                            <flux:icon name="user" size="sm" />
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-900 dark:text-white leading-none">{{ $child->child_name }}</h4>
                                            <div class="flex items-center gap-3 mt-1">
                                                <span class="text-[10px] uppercase font-bold text-indigo-500">{{ $child->gender->gender_name ?? 'N/A' }}</span>
                                                <span class="text-gray-300 dark:text-gray-600">|</span>
                                                <span class="text-[11px] text-gray-500">DOB: <strong>{{ $child->date_of_birth }}</strong></span>
                                                <span class="text-gray-300 dark:text-gray-600">|</span>
                                                <span class="text-[11px] text-gray-500">BC No: <strong>{{ $child->birth_fc_no }}</strong></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if ($canDelete)
                                        <flux:button wire:click="deleteChild('{{ $child->id }}')" 
                                            onclick="confirm('Delete child record?') || event.stopImmediatePropagation()"
                                            variant="ghost" icon="trash" size="sm" class="opacity-0 group-hover:opacity-100 transition-opacity" />
                                    @endif
                                </div>
                            @empty
                                <div class="py-8 text-center bg-gray-50 dark:bg-gray-900/30 rounded-xl border border-dashed border-gray-200 dark:border-gray-800">
                                    <p class="text-sm text-gray-400">No children records found.</p>
                                </div>
                            @endforelse
                        </div>
                    </section>
                </div>
            @else
                {{-- Single Status State --}}
                <div class="max-w-md mx-auto mt-20 text-center">
                    <div class="inline-flex p-4 bg-blue-50 dark:bg-blue-900/20 rounded-full mb-4">
                        <flux:icon name="information-circle" class="text-blue-600 dark:text-blue-400" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Civil Status: Single</h3>
                    <p class="text-gray-500 mt-2">Family management is only available for married or other civil statuses.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Edit general information --}}
    @if ($canCreate)
        <flux:modal wire:model="showModalSpouseReg" name="add-spouse-info" class="md:w-150">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Spouse Register</flux:heading>
                    <flux:text class="mt-2">Make changes to your personal details.
                </flux:text>
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

                            <flux:button icon="check" variant="primary" wire:click="checkNIC"
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

                        <div class="flex gap-4">
                            <!-- Gender -->
                            <div class="w-1/2">
                                <flux:field>
                                    <flux:input type="date" label="Married date" wire:model.live="marriedDate" />
                                </flux:field>
                            </div>

                            <!-- Birthday -->
                            <div class="w-1/2">
                                <flux:field>
                                    <flux:input type="text" label="Married certifacate no"
                                        wire:model.live="marriedCfNo" />
                                </flux:field>
                            </div>
                        </div>

                        <div class="flex mt-4">
                            <flux:spacer />
                            <flux:button type="submit" variant="primary">Save changes</flux:button>
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
                <div>
                    <flux:heading size="lg">Children's register form</flux:heading>
                <flux:text class="mt-2">Make changes to your family details.</flux:text>
                {{-- <p>{{$family_id}}</p> --}}
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

                <div class="flex">
                    <flux:spacer />

                    <flux:button type="submit" variant="primary">Save changes</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
    @endif
</div>