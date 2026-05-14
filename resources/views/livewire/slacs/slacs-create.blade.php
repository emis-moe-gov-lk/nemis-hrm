<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Create Sri Lanka Accountancy Service Officer') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Create SLAcS profile and account') }}</flux:subheading>
        <flux:separator variant="subtle" />

        {{-- Alert Messages --}}
        <div class="max-w-2xl my-8">

            <div>
                @if (session('success'))
                <x-alert type="success" dismissible class="mb-4">
                    {{ session('success') }}
                </x-alert>
                @endif

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
        </div>

        {{-- Step Progress Bar --}}
        <div class="max-w-2xl my-12">
            <div class="relative flex justify-between w-full px-2">
                <!-- Connecting Line Background -->
                <div class="absolute left-0 top-5 -translate-y-1/2 w-full h-1 bg-gray-200 dark:bg-neutral-800 rounded-lg -z-10"></div>

                <!-- Connecting Line Filled -->
                <div class="absolute left-0 top-5 -translate-y-1/2 h-1 bg-indigo-600 dark:bg-indigo-500 rounded-lg -z-10 transition-all duration-500 ease-out"
                    style="width: {{ (($step - 1) / ($maxStep - 1)) * 100 }}%"></div>

                @php
                $stepLabels = [
                1 => 'Verification',
                2 => 'Personal',
                3 => 'Contact',
                4 => 'First Appt',
                5 => 'Current Appt'
                ];
                @endphp

                @for ($i = 1; $i <= $maxStep; $i++)
                    <div class="flex flex-col items-center relative group w-20">
                    <!-- Step Circle -->
                    <div class="w-10 h-10 flex items-center justify-center rounded-full border-4 transition-all duration-300 z-10
                            {{ $i < $step
                                ? 'bg-indigo-600 border-indigo-600 dark:border-indigo-500 text-white'
                                : ($i == $step
                                    ? 'bg-white dark:bg-neutral-900 border-indigo-600 dark:border-indigo-500 text-indigo-600 dark:text-indigo-400 scale-110 shadow-lg shadow-indigo-500/20'
                                    : 'bg-white dark:bg-neutral-900 border-gray-300 dark:border-neutral-700 text-gray-400 dark:text-gray-600')
                            }}">

                        @if ($i < $step)
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            @else
                            <span class="text-sm font-bold">{{ $i }}</span>
                            @endif
                    </div>

                    <!-- Pulse Effect for Active Step -->
                    @if ($i == $step)
                    <div class="absolute top-0 w-10 h-10 rounded-full bg-indigo-400/30 animate-ping -z-10"></div>
                    @endif

                    <!-- Step Label -->
                    <div class="mt-2 hidden md:block text-center">
                        <span class="text-xs font-medium {{ $i <= $step ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400' }}">
                            {{ $stepLabels[$i] }}
                        </span>
                    </div>
            </div>
            @endfor
        </div>
    </div>
</div>

<form wire:submit.prevent="save" class="mt-6 max-w-2xl space-y-6">
    @csrf

    <!-- Personal Details -->
    @if ($step == 1)
    <div class="mt-6 max-w-2xl space-y-6">
        <flux:heading size="lg" level="2" class="mt-8 mb-4">Please verify the NIC before continuing</flux:heading>
        <flux:separator variant="subtle" />

        <div class="mb-6 p-4 rounded-xl border border-amber-200 bg-amber-50 dark:bg-amber-900/20 dark:border-amber-700/30 text-amber-900 dark:text-amber-100 shadow-sm">
            <div class="flex items-start gap-4">
                <!-- Icon -->
                <div class="p-2 bg-amber-100 dark:bg-amber-800/50 rounded-lg shrink-0">
                    <svg class="h-6 w-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3m0 4h.01M4.93 19h14.14a2 2 0 0 0 1.72-3l-7.07-12a2 2 0 0 0-3.46 0L3.21 16a2 2 0 0 0 1.72 3z" />
                    </svg>
                </div>

                <div class="space-y-2 pt-1">
                    <p class="text-sm font-medium leading-relaxed">
                        Before starting the registration, ensure that the National Identity Card (NIC)
                        number already exists in the system. After confirming the correctness of the NIC
                        you enter, proceed further.
                    </p>

                    <p class="text-sm leading-relaxed text-amber-800 dark:text-amber-300 opacity-90">
                        ලියාපදිංචිය ආරම්භ කිරීමට පෙර, කරුණාකර ජාතික හැඳුනුම්පත් අංකය දැනටමත් පද්ධතියේ ඇති බව
                        තහවුරු කර ගන්න. ඔබ ඇතුළත් කරන NIC අංකය නිවැරදි බව තහවුරු වූ පසු, ඉදිරියට යන්න.
                    </p>
                </div>
            </div>
        </div>


        <div>
            <div class="flex items-center justify-center gap-3">
                <div class="flex-1">
                    <flux:field>
                        <flux:input
                            wire:model.live.debounce.300ms="nic"
                            placeholder="Enter NIC" />
                    </flux:field>
                </div>

                <flux:button
                    variant="primary"
                    class=""
                    wire:click="searchNic"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove>Check NIC</span>
                    <span wire:loading>Searching...</span>
                </flux:button>

            </div>
            <!-- IMPORTANT: error MUST be inside flux:field, after the input -->
            @error('nic')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

    </div>
    @endif

    @if ($step == 2)
    <div class="mt-6 max-w-2xl space-y-6">
        <flux:heading size="lg" level="2" class="mt-8 mb-4">Personal Details</flux:heading>
        <flux:separator variant="subtle" />

        <div class="space-y-6">
            <div class="flex gap-4">
                <div class="w-1/5">
                    <flux:field>
                        <flux:select label="Title" wire:model.live.debounce.150ms="title">
                            <option value="">Select</option>
                            @foreach ($titleOptions as $data)
                            <option value="{{ $data->title_id }}">{{ $data->title_name }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                </div>

                <div class="w-4/5">
                    <flux:field>
                        <flux:input label="Full Name" wire:model.live.debounce.150ms="fullName"
                            placeholder="Enter full name" />
                    </flux:field>
                </div>
            </div>

            <div class="flex gap-4">
                <!-- Gender -->
                <div class="w-1/2">
                    <flux:field>
                        <flux:select label="Gender" wire:model.live.debounce.150ms="gender">
                            <option value="">Select</option>
                            @foreach ($genderOptions as $data)
                            <option value="{{ $data->gender_id }}">{{ $data->gender_name }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                </div>

                <!-- Birthday -->
                <div class="w-1/2">
                    <flux:field>
                        <flux:input type="date" label="Birthday" wire:model.live.debounce.150ms="birthday" />
                    </flux:field>
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-4">
                <!-- Ethnicity -->
                <div class="md:w-1/2 w-full">
                    <flux:field>
                        <flux:select label="Ethnicity" wire:model.live.debounce.150ms="ethnicity">
                            <option value="">Select</option>
                            @foreach ($ethnicityOptions as $data)
                            <option value="{{ $data->ethnicity_id }}">{{ $data->ethnicity_name }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                </div>

                <!-- Religion Status -->
                <div class="md:w-1/2 w-full">
                    <flux:field>
                        <flux:select label="Religion" wire:model.live.debounce.150ms="religion">
                            <option value="">Select Religion</option>
                            @foreach ($religionOptions as $data)
                            <option value="{{ $data->religion_id }}">{{ $data->religion_name }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-4">
                <!-- Civil Status -->
                <div class="md:w-1/2 w-full">
                    <flux:field>
                        <flux:select label="Civil Status" wire:model.live.debounce.150ms="civilStatus">
                            <option value="">Select</option>
                            @foreach ($civilStatusOptions as $data)
                            <option value="{{ $data->civil_status_id }}">{{ $data->civil_status_name }}
                            </option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                </div>

                <!-- Blood Group -->
                <div class="md:w-1/2 w-full">
                    <flux:field>
                        <flux:select label="Blood Group" wire:model.live.debounce.150ms="bloodGroup">
                            <option value="">Select</option>
                            @foreach ($bloodGroupOptions as $data)
                            <option value="{{ $data->blood_group_id }}">
                                {{ $data->blood_group_name ?? $data->blood_group }}
                            </option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                </div>
            </div>

            <!-- Health Condition -->
            <div class="w-full">
                <flux:field>
                    <flux:select label="Healthy?" wire:model.live.debounce.150ms="healthCondition">
                        @foreach ($healthConditionOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </flux:select>
                </flux:field>
            </div>

            <!-- Health Problem -->
            @if (!$healthCondition)
            <div class="w-full">
                <flux:field>
                    <flux:textarea label="Please provide details of the health problem."
                        wire:model.live.debounce.150ms="healthProblem" placeholder="Enter health problem details here..."
                        rows="4" />
                </flux:field>
            </div>
            @endif

            <flux:field>
                <flux:select label="District" wire:model.live.debounce.150ms="district" placeholder="Select District">
                    <option value="">Select</option>
                    @foreach ($districtOption as $data)
                    <option value="{{ $data->district_id }}">{{ $data->district_name }}</option>
                    @endforeach
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:select label="Divisional Secretary office" wire:model.live.debounce.150ms="divisionalDecretaryOffice"
                    placeholder="Select Divisional Secretary office">
                    <option value="">Select</option>
                    @foreach ($divisionalSecretaryofficeOption as $data)
                    <option value="{{ $data->dso_id }}">{{ $data->dso_name }}</option>
                    @endforeach
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:select label="GN Division" wire:model.live.debounce.150ms="gnDivision" placeholder="Select GN Division">
                    <option value="">Select</option>
                    @foreach ($gnDivisionOption as $data)
                    <option value="{{ $data->gn_division_id }}">({{ $data->gn_division_code }}) -
                        {{ $data->gn_division_name }}
                    </option>
                    @endforeach
                </flux:select>
            </flux:field>
        </div>
    </div>
    @endif

    <!-- Contact Details -->
    @if ($step === 3)
    <div class="mt-6 max-w-2xl space-y-6">
        <flux:heading size="lg" level="2" class="mt-8 mb-4">Contact Details</flux:heading>
        <flux:separator variant="subtle" />

        <flux:field>
            <flux:input label="Contact" wire:model.live.debounce.150ms="contact"
                placeholder="Enter Contact (10 digits)" />
        </flux:field>

        <flux:field>
            <flux:input label="Email" type="email" wire:model.live.debounce.150ms="email"
                placeholder="Enter email" />
        </flux:field>

        <flux:field>
            <flux:input label="Address Line 1" wire:model.live.debounce.150ms="addressLine1"
                placeholder="Enter address line 1" />
        </flux:field>

        <flux:field>
            <flux:input label="Address Line 2" wire:model.live.debounce.150ms="addressLine2"
                placeholder="Enter address line 2" />
        </flux:field>

        <div class="flex flex-col md:flex-row gap-4">
            <!-- Address Line 3 -->
            <div class="md:w-3/4 w-full">
                <flux:field>
                    <flux:input label="Address Line 3" wire:model.live.debounce.150ms="addressLine3"
                        placeholder="Enter address line 3" />
                </flux:field>
            </div>

            <!-- Postal Code -->
            <div class="md:w-1/4 w-full">
                <flux:field>
                    <flux:input label="Postal Code" wire:model.live.debounce.150ms="postalCode"
                        placeholder="Enter postal code" />
                </flux:field>
            </div>
        </div>

        <div class="flex flex-col md:flex-row gap-4">
            <!-- latitude -->
            <div class="md:w-1/2 w-full">
                <flux:field>
                    <flux:input label="Latitude" wire:model.live.debounce.150ms="latitude"
                        placeholder="Enter latitude (optional)" />
                </flux:field>
            </div>

            <!-- longitude -->
            <div class="md:w-1/2 w-full">
                <flux:field>
                    <flux:input label="Longitude" wire:model.live.debounce.150ms="longitude"
                        placeholder="Enter longitude (optional)" />
                </flux:field>
            </div>
        </div>

        <div
            class="p-4 space-y-6 bg-gray-100 dark:bg-gray-900 rounded-md border border-gray-300 dark:border-gray-700">
            <p class="text-gray-700 dark:text-gray-200 font-bold">
                Temporary Address (If different from permanent address)
            </p>

            <flux:field class="text-gray-700 dark:text-gray-300">
                <flux:input label="Address Line 1" wire:model.live.debounce.150ms="tAddressLine1"
                    class="bg-white dark:bg-gray-800 text-black dark:text-white border-gray-300 dark:border-gray-600 placeholder-gray-400"
                    placeholder="Enter address line 1" />
            </flux:field>

            <flux:field class="text-gray-700 dark:text-gray-300">
                <flux:input label="Address Line 2" wire:model.live.debounce.150ms="tAddressLine2"
                    class="bg-white dark:bg-gray-800 text-black dark:text-white border-gray-300 dark:border-gray-600 placeholder-gray-400"
                    placeholder="Enter address line 2" />
            </flux:field>

            <div class="flex flex-col md:flex-row gap-4">
                <div class="md:w-3/4 w-full">
                    <flux:field class="text-gray-700 dark:text-gray-300">
                        <flux:input label="Address Line 3" wire:model.live.debounce.150ms="tAddressLine3"
                            class="bg-white dark:bg-gray-800 text-black dark:text-white border-gray-300 dark:border-gray-600 placeholder-gray-400"
                            placeholder="Enter address line 3" />
                    </flux:field>
                </div>

                <div class="md:w-1/4 w-full">
                    <flux:field class="text-gray-700 dark:text-gray-300">
                        <flux:input label="Postal Code" wire:model.live.debounce.150ms="tPostalCode"
                            class="bg-white dark:bg-gray-800 text-black dark:text-white border-gray-300 dark:border-gray-600 placeholder-gray-400"
                            placeholder="Enter postal code" />
                    </flux:field>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Appointment Details -->
    @if ($step === 4)
    <div class="mt-6 max-w-2xl space-y-6">
        <flux:heading size="lg" level="2" class="mt-8 mb-4">First Appointment Details
        </flux:heading>
        <flux:separator variant="subtle" />

        <div class="flex flex-col md:flex-row gap-4">
            <!-- First Appointment Date -->
            <div class="md:w-1/2 w-full">
                <flux:field>
                    <flux:input type="date" label="First Appointment Date"
                        wire:model.live.debounce.150ms="firstAppointmentDate" />
                </flux:field>
            </div>

            <!-- Appointment letter number -->
            <div class="md:w-1/2 w-full">
                <flux:field>
                    <flux:input label="Appointment Letter No" wire:model.live.debounce.150ms="appointmentLetterNo"
                        placeholder="Enter letter number" />
                </flux:field>
            </div>
        </div>

        <div class="flex flex-col md:flex-row gap-4">
            <!-- Service -->
            <div class="md:w-1/2 w-full">
                <flux:field>
                    <flux:select label="Service" wire:model.live.debounce.150ms="service">
                        <option value="">Select</option>
                        @foreach ($servicesOption as $service)
                        <option value="{{ $service->service_id }}">{{ $service->service_name }}
                        </option>
                        @endforeach
                    </flux:select>
                </flux:field>
            </div>

            <!-- Rank -->
            <div class="md:w-1/2 w-full">
                <flux:field>
                    <flux:select label="Service Rank" wire:model.live.debounce.150ms="serviceRank">
                        <option value="">Select</option>
                        @foreach ($ranksOption as $rank)
                        <option value="{{ $rank->rank_id }}">{{ $rank->rank_name }}</option>
                        @endforeach
                    </flux:select>
                </flux:field>
            </div>
        </div>

        <div class="space-y-6">
            <flux:select label="Appointed working Place Level" wire:model.live="firstOfficeLevel">
                <option value="">Select</option>
                @foreach ($firstOfficeLevelOption as $level)
                    <option value="{{ $level->office_level_id }}">{{ $level->office_level_name }}</option>
                @endforeach
            </flux:select>

            @if ($firstOfficeLevel == 'OLID006')
                <flux:select label="Zonal Education Office" wire:model.live="firstZonalEducationOffice">
                    <option value="">Select</option>
                    @foreach ($firstZonalEducationOfficeOption as $zone)
                        <option value="{{ $zone->workplace_id }}">{{ $zone->short_name }}
                        </option>
                    @endforeach
                </flux:select>

                <flux:select label="Institution Category" wire:model.live="firstInstitutionCategory">
                    <option value="">Select</option>
                    @foreach ($firstInstitutionCategoryOption as $data)
                        <option value="{{ $data->institution_category_id }}">
                            {{ $data->institution_category_name }}</option>
                    @endforeach
                </flux:select>
            @endif

            <flux:select label="Working Place" wire:model.live="firstWorkingPlace">
                <option value="">Select</option>
                @foreach ($firstWorkingPlaceOption as $office)
                    <option value="{{ $office->workplace_id }}">{{ $office->office_name }}</option>
                @endforeach
            </flux:select>
        </div>

        <flux:field>
            <flux:select label="Appointmented Position" wire:model.live.debounce.150ms="position">
                <option value="">Select</option>
                @foreach ($positionOption as $position)
                <option value="{{ $position->position_id }}">
                    {{ $position->position_name }}
                </option>
                @endforeach
            </flux:select>
        </flux:field>
    </div>
    @endif

    @if ($step === 5)
    <div class="mt-6 max-w-2xl space-y-6">
        <flux:heading size="lg" level="2" class="mt-8 mb-4">Current Appointment Details
        </flux:heading>
        <flux:separator variant="subtle" />

        <flux:radio.group label="Select registration type for the SLAcS Officer"
            wire:model.live.debounce.150ms="slacsRegType">
            <flux:radio name="slacsRegType" value="new" disabled label="New SLAcS Officer"
                description="New SLAcS Officer users can perform any action." />
            <flux:radio name="slacsRegType" value="existing" label="Existing SLAcS Officer"
                description="Existing SLAcS Officer users have the ability to read, create, and update." />
        </flux:radio.group>

        @if ($slacsRegType != 'new')
        <div class=" border border-red-500 bg-red-100 rounded-lg p-2">
            <p class=" text-red font">නවක ගණකාධිකාරීවරයකු ලෙස පත්වීමක් ලබා නොගන්නා අවස්තාවක, සේවා කාලයක්
                සහිත ගණකාධිකාරීවරයකු ලියාපදිංචි කිරීම සඳහා පමණි
            <div class="br"></div>Only for the registration of a SLAcS Officer with a period of service,
            in the event that an appointment is not obtained as a new SLAcS Officer.</p>
        </div>
        <div class=" space-y-6">
            <div class="flex flex-col md:flex-row gap-4">
                <!-- Current Appointment Date -->
                <div class="md:w-1/2 w-full">
                    <flux:field>
                        <flux:input type="date" label="Current Appointment Date"
                            wire:model.live.debounce.150ms="currentAppointmentDate" />
                    </flux:field>
                </div>

                <!-- Current Appointment letter number -->
                <div class="md:w-1/2 w-full">
                    <flux:field>
                        <flux:input label="Appointment/Transfer Letter No"
                            wire:model.live.debounce.150ms="currentAppointmentLetterNo"
                            placeholder="Enter letter number" />
                    </flux:field>
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-4">
                <!-- Current Service -->
                <div class="md:w-1/2 w-full">
                    <flux:field>
                        <flux:select label="Current Service" wire:model.live.debounce.150ms="currentService">
                            <option value="">Select</option>
                            @foreach ($servicesOption as $service)
                            <option value="{{ $service->service_id }}">
                                {{ $service->service_name }}
                            </option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                </div>

                <!-- Current Rank -->
                <div class="md:w-1/2 w-full">
                    <flux:field>
                        <flux:select label="Current Service Rank"
                            wire:model.live.debounce.150ms="currentServiceRank">
                            <option value="">Select</option>
                            @foreach ($currentRanksOption as $rank)
                            <option value="{{ $rank->rank_id }}">{{ $rank->rank_name }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                </div>
            </div>

            <div class="space-y-6">
                <flux:select label="Current working Place Level" wire:model.live="currentOfficeLevel">
                    <option value="">Select</option>
                    @foreach ($currentOfficeLevelOption as $level)
                        <option value="{{ $level->office_level_id }}">{{ $level->office_level_name }}</option>
                    @endforeach
                </flux:select>

                @if ($currentOfficeLevel == 'OLID006')
                    <flux:select label="Zonal Education Office" wire:model.live="currentZonalEducationOffice">
                        <option value="">Select</option>
                        @foreach ($currentZonalEducationOfficeOption as $zone)
                            <option value="{{ $zone->workplace_id }}">{{ $zone->short_name }}
                            </option>
                        @endforeach
                    </flux:select>

                    <flux:select label="Institution Category" wire:model.live="currentInstitutionCategory">
                        <option value="">Select</option>
                        @foreach ($currentInstitutionCategoryOption as $data)
                            <option value="{{ $data->institution_category_id }}">
                                {{ $data->institution_category_name }}</option>
                        @endforeach
                    </flux:select>
                @endif

                <flux:select label="Working Place" wire:model.live="currentWorkingPlace">
                    <option value="">Select</option>
                    @foreach ($currentWorkingPlaceOption as $office)
                        <option value="{{ $office->workplace_id }}">{{ $office->office_name }}</option>
                    @endforeach
                </flux:select>
            </div>

            <flux:field>
                <flux:select label="Current Appointed Position" wire:model.live.debounce.150ms="currentPosition">
                    <option value="">Select</option>
                    @foreach ($currentPositionOption as $position)
                    <option value="{{ $position->position_id }}">
                        {{ $position->position_name }}
                    </option>
                    @endforeach
                </flux:select>
            </flux:field>
        </div>
        @endif
    </div>
    @endif

    @if($nicCheck)
    <div class="flex justify-between mt-8 bg-gray-50 dark:bg-neutral-900 p-2 rounded-xl border border-gray-200 dark:border-neutral-700">
        @if ($step > 1)
        <flux:button type="button" wire:click="previousStep" icon="arrow-left">Previous</flux:button>
        @else
        <div></div> <!-- Empty div for spacing -->
        @endif

        @if ($step < $maxStep)
            <flux:button type="button" wire:click="nextStep" icon:trailing="arrow-right" variant="primary">Next</flux:button>
            @else
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                <span wire:loading.remove>Create SLAcS Officer</span>
                <span wire:loading>Creating...</span>
            </flux:button>
            @endif
    </div>
    @endif
</form>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('scroll-top', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });
</script>

</div>