<div class="p-6 lg:p-12 dark:bg-zinc-950 min-h-screen transition-colors duration-500">
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <header class="space-y-1 lg:space-y-2">
            <flux:heading size="xl" level="1">[ {{ $employeeServiceType ?? 'N/A' }} ] Register New Employee</flux:heading>
            <flux:subheading class="text-xs lg:text-sm text-slate-500 dark:text-zinc-400 leading-relaxed">Complete the steps below to register a new employee.</flux:subheading>
        </header>

        <!-- Flash Messages -->
        @if(session('success'))
        <div class="p-4 mb-4 rounded-2xl bg-green-50 dark:bg-green-950 border border-green-200 dark:border-green-800">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <p class="text-green-800 dark:text-green-200 font-medium">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="p-4 mb-4 rounded-2xl bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <p class="text-red-800 dark:text-red-200 font-medium">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        @if(session('info'))
        <div class="p-4 mb-4 rounded-2xl bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-800">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
                <p class="text-blue-800 dark:text-blue-200 font-medium">{{ session('info') }}</p>
            </div>
        </div>
        @endif

        <!-- Stepper -->
        <nav class="relative flex items-center justify-between px-2">
            <!-- Progress Line (Locked to center of circles) -->
            <div class="absolute top-4 lg:top-5 left-[10%] w-[80%] h-px bg-slate-200 dark:bg-zinc-800"></div>

            @foreach(['Verification', 'Personal', 'Contact', 'First Appt.', 'Current Status'] as $i => $label)
            @php $stepNum = $i + 1; @endphp
            <div class="relative z-10 flex flex-col items-center flex-1 group">
                <!-- Step Circle -->
                <div @class([ 'w-8 h-8 lg:w-10 lg:h-10 rounded-full flex items-center justify-center border-2 transition-all duration-500 bg-white dark:bg-zinc-900' , 'border-indigo-600! bg-indigo-600! text-white shadow-[0_0_15px_rgba(79,70,229,0.5)] scale-110'=> $step === $stepNum,
                    'border-indigo-600! bg-indigo-600! text-white' => $step > $stepNum,
                    'border-slate-300 dark:border-zinc-700 text-slate-500' => $step < $stepNum,
                        ])>
                        @if($step > $stepNum)
                        <flux:icon.check variant="micro" class="w-5 h-5 lg:w-6 lg:h-6" />
                        @else
                        <span class="text-xs lg:text-sm font-black">{{ $stepNum }}</span>
                        @endif
                </div>

                <!-- Step Label (Hidden on mobile) -->
                <span @class([ 'mt-4 text-[10px] uppercase tracking-widest font-black transition-colors duration-300 text-center hidden sm:block' , 'text-indigo-600'=> $step >= $stepNum,
                    'text-slate-400 dark:text-zinc-500' => $step < $stepNum,
                        ])>
                        {{ $label }}
                </span>
            </div>
            @endforeach
        </nav>

        <flux:card class="p-0 overflow-hidden border-none shadow-xl shadow-slate-200/60 dark:shadow-none bg-white/80 dark:bg-zinc-900/50 backdrop-blur-xl">
            <div class="p-6 lg:p-12 space-y-8">
                <!-- STEP 1: VERIFICATION -->
                @if ($step === 1)
                <div class="space-y-4">
                    <!-- Multi-language Notice (Wide) -->
                    <div class="bg-amber-50/50 dark:bg-amber-500/5 p-6 lg:p-8 rounded-3xl border border-amber-100 dark:border-amber-500/20 text-left animate-in zoom-in duration-500">
                        <div class="flex items-start gap-4">
                            <flux:icon.exclamation-triangle variant="mini" class="text-amber-600 shrink-0 mt-1" />
                            <div class="gap-6 text-[11px] lg:text-xs text-amber-900/80 dark:text-amber-200/80 leading-relaxed font-medium">
                                <p>Before starting the registration, ensure that the National Identity Card (NIC) number already exists in the system. After confirming the correctness of the NIC you enter, proceed further.</p>
                                <p>ලියාපදිංචිය ආරම්භ කිරීමට පෙර, කරුණාකර ජාතික හැඳුනුම්පත් අංකය දැනටමත් පද්ධතියේ ඇති බව තහවුරු කර ගන්න. ඔබ ඇතුළත් කරන NIC අංකය නිවැරදි බව තහවුරු වූ පසු, ඉදිරියට යන්න.</p>
                                <p>பதிவைத் தொடங்குவதற்கு முன்னர், உங்கள் தேசிய அடையாள அட்டை எண் ஏற்கனவே கணினி முறைமையில் உள்ளதா என்பதை உறுதிப்படுத்திக் கொள்ளுங்கள். நீங்கள் உள்ளிடும் NIC எண் சரியானது என்பதை உறுதிப்படுத்தியவுடன், தொடரவும்.</p>
                            </div>
                        </div>
                    </div>
                    <div class="max-w-3xl mx-auto space-y-6 lg:space-y-10 text-center animate-in fade-in slide-in-from-bottom-4 duration-700">
                        <div class="space-y-4">
                            <div class="inline-flex items-center justify-center w-12 h-12 lg:w-16 lg:h-16 bg-indigo-50 dark:bg-indigo-500/10 rounded-3xl text-indigo-600 dark:text-indigo-400 transform rotate-6 shadow-inner ring-1 ring-indigo-100 dark:ring-indigo-900/30 mx-auto">
                                <flux:icon.identification variant="mini" class="w-6 h-6 lg:w-8 lg:h-8" />
                            </div>
                            <div class="space-y-2">
                                <flux:heading size="lg" class="text-slate-900 dark:text-white">Identity Verification</flux:heading>
                                <flux:subheading class="text-xs lg:text-sm text-slate-500 dark:text-zinc-400">Enter the NIC number to begin the registration process.</flux:subheading>
                            </div>
                        </div>

                        <!-- Input Section (Small/Compact) -->
                        <div class="max-w-md mx-auto w-full text-left space-y-4">
                            <flux:field>
                                <flux:label>National Identity Card (NIC)</flux:label>
                                <flux:input wire:model.blur="nic" placeholder="e.g. 199012345678" class="rounded-2xl!" />
                                <flux:error name="nic" />
                            </flux:field>

                            <flux:button wire:click="searchNic" variant="primary" class="w-full h-12 rounded-2xl! bg-indigo-600! hover:bg-indigo-700! shadow-lg shadow-indigo-200 dark:shadow-none" loading="searchNic">
                                Verify & Continue
                            </flux:button>

                            @if($nicCheck)
                            <div class="pt-4 animate-in zoom-in duration-500 text-center">
                                <flux:button wire:click="nextStep" variant="filled" icon-trailing="arrow-right" class="w-full rounded-xl!">
                                    Proceed to Next Step
                                </flux:button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- STEP 2: PERSONAL DETAILS -->
                @if ($step === 2)
                <div class="space-y-10 animate-in fade-in slide-in-from-right-4 duration-500">
                    <div class="mt-6 mx-auto max-w-3xl space-y-6">
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
                        <div class="flex justify-between pt-8 border-t border-slate-100 dark:border-zinc-800">
                            <flux:button wire:click="previousStep" variant="filled" class="rounded-xl!">Back</flux:button>
                            <flux:button wire:click="nextStep" variant="primary" class="rounded-xl! bg-indigo-600! hover:bg-indigo-700!">Continue to Contact Info</flux:button>
                        </div>
                    </div>
                </div>
                @endif

                <!-- STEP 3: CONTACT DETAILS -->
                @if ($step === 3)
                <div class="space-y-10 animate-in fade-in slide-in-from-right-4 duration-500">
                    <div class="mt-6 mx-auto max-w-3xl space-y-6">
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

                        <div class="flex justify-between pt-8 border-t border-slate-100 dark:border-zinc-800">
                            <flux:button wire:click="previousStep" variant="filled" class="rounded-xl!">Back</flux:button>
                            <flux:button wire:click="nextStep" variant="primary" class="rounded-xl! bg-indigo-600! hover:bg-indigo-700!">Continue to Appointment</flux:button>
                        </div>

                    </div>


                </div>
                @endif

                <!-- STEP 4: FIRST APPOINTMENT -->
                @if ($step === 4)
                <div class="space-y-10 animate-in fade-in slide-in-from-right-4 duration-500">
                    <div class="mt-6 mx-auto max-w-3xl space-y-10">
                        <div class="bg-indigo-50/50 dark:bg-indigo-500/5 p-8 rounded-4xl border border-indigo-100 dark:border-indigo-500/20 shadow-inner">
                            <flux:select wire:model.live="service" label="Service Category" class="rounded-xl!">
                                <option value="">Select Service Category</option>
                                @foreach($servicesOption as $opt)
                                <option value="{{ $opt->service_id }}">{{ $opt->service_name }}</option>
                                @endforeach
                            </flux:select>
                        </div>

                        @if($service)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
                            <flux:field>
                                <flux:label>First Appointment Date</flux:label>
                                <flux:input type="date" wire:model="firstAppointmentDate" class="rounded-xl!" />
                                <flux:error name="firstAppointmentDate" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Appointment Letter Number</flux:label>
                                <flux:input wire:model="appointmentLetterNo" class="rounded-xl!" />
                                <flux:error name="appointmentLetterNo" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Service Rank</flux:label>
                                <flux:select wire:model="serviceRank" class="rounded-xl!">
                                    <option value="">Select Rank</option>
                                    @foreach($ranksOption as $opt) <option value="{{ $opt->rank_id }}">{{ $opt->rank_name }}</option> @endforeach
                                </flux:select>
                                <flux:error name="serviceRank" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Position</flux:label>
                                <flux:select wire:model="position" class="rounded-xl!">
                                    <option value="">Select Position</option>
                                    @foreach($positionOption as $opt) <option value="{{ $opt->position_id }}">{{ $opt->position_name }}</option> @endforeach
                                </flux:select>
                                <flux:error name="position" />
                            </flux:field>


                            <flux:field>
                                <flux:label>Working Place Level</flux:label>
                                <flux:select wire:model.live="officeLevel" class="rounded-xl!">
                                    <option value="">Select Level</option>
                                    @foreach ($officeLevelOption as $level)
                                    <option value="{{ $level->office_level_id }}">{{ $level->office_level_name }}</option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="officeLevel" />
                            </flux:field>

                            @if ($officeLevel == 'OLID006')
                            <flux:field>
                                <flux:label>Zonal Education Office</flux:label>
                                <flux:select wire:model.live="zonalEducationOffice" class="rounded-xl!">
                                    <option value="">Select Zonal Office</option>
                                    @foreach ($zonalEducationOfficeOption as $zone)
                                    <option value="{{ $zone->workplace_id }}">{{ $zone->short_name }}</option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="zonalEducationOffice" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Institution Category</flux:label>
                                <flux:select wire:model.live="institutionCategory" class="rounded-xl!">
                                    <option value="">Select Category</option>
                                    @foreach ($institutionCategoryOption as $data)
                                    <option value="{{ $data->institution_category_id }}">{{ $data->institution_category_name }}</option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="institutionCategory" />
                            </flux:field>
                            @endif

                            <flux:field>
                                <flux:label>Working Place</flux:label>
                                <flux:select wire:model.live="workingPlace" class="rounded-xl!">
                                    <option value="">Select Workplace</option>
                                    @foreach ($workingPlaceOption as $office)
                                    <option value="{{ $office->workplace_id }}">{{ $office->office_name }}</option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="workingPlace" />
                            </flux:field>


                            <!-- TEACHER SPECIFIC -->
                            @if($service === 'SER001')
                            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-8 items-start bg-slate-50/50 dark:bg-zinc-900/30 p-6 lg:p-8 rounded-3xl border border-slate-100 dark:border-zinc-800 shadow-sm mt-4 animate-in fade-in slide-in-from-top-4 duration-500">
                                <div class="md:col-span-2 flex items-center gap-3 mb-2">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                                        <flux:icon.academic-cap variant="mini" class="text-indigo-600 dark:text-indigo-400" />
                                    </div>
                                    <flux:heading size="sm" class="text-indigo-900 dark:text-indigo-200 uppercase tracking-wider font-bold">Teacher Specific Details</flux:heading>
                                </div>

                                <flux:field>
                                    <flux:label>Teacher Appointment Category</flux:label>
                                    <flux:select wire:model.live.debounce.150ms="teacherCategory" class="rounded-xl!">
                                        <option value="">Select Category</option>
                                        @foreach ($teacherCategoriesOption as $data)
                                        <option value="{{ $data->categories_id }}">{{ $data->name }}</option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="teacherCategory" />
                                </flux:field>

                                <flux:field>
                                    <flux:label>Types of Teachers</flux:label>
                                    <flux:select wire:model.live.debounce.150ms="teacherType" class="rounded-xl!">
                                        <option value="">Select Type</option>
                                        @foreach ($teacherTypeOptions as $data)
                                        <option value="{{ $data->teacher_types_id }}">{{ $data->type_name }}</option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="teacherType" />
                                </flux:field>

                                <flux:field>
                                    <flux:label>Appointment Subject</flux:label>
                                    <flux:select wire:model.live.debounce.150ms="appointmentSubject" class="rounded-xl!">
                                        <option value="">Select Subject</option>
                                        @foreach ($appointmentSubjectOption as $subject)
                                        <option value="{{ $subject->a_subject_id }}">{{ $subject->name_en }}</option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="appointmentSubject" />
                                </flux:field>

                                <flux:field>
                                    <flux:label>Appointment Medium</flux:label>
                                    <flux:select wire:model.live.debounce.150ms="appointmentMedium" class="rounded-xl!">
                                        <option value="">Select Medium</option>
                                        @foreach ($appointmentMediumOptions as $medium)
                                        <option value="{{ $medium->medium_id }}">{{ $medium->name }}</option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="appointmentMedium" />
                                </flux:field>

                                <flux:field>
                                    <flux:label>Main Teaching Subject</flux:label>
                                    <flux:select wire:model.live.debounce.150ms="mainTeachingSubject" class="rounded-xl!">
                                        <option value="">Select Subject</option>
                                        @foreach ($subjectOption as $subject)
                                        <option value="{{ $subject->subject_id }}">{{ $subject->name_en }}</option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="mainTeachingSubject" />
                                </flux:field>

                                <flux:field>
                                    <flux:label>Secondary Teaching Subject (Optional)</flux:label>
                                    <flux:select wire:model.live.debounce.150ms="secondaryTeachingSubject" class="rounded-xl!">
                                        <option value="">Select Subject</option>
                                        @foreach ($subjectOption as $subject)
                                        <option value="{{ $subject->subject_id }}">{{ $subject->name_en }}</option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="secondaryTeachingSubject" />
                                </flux:field>
                            </div>
                            @endif

                            <!-- Principal SPECIFIC -->
                            @if($service === 'SER004')
                            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-8 items-start bg-indigo-50/50 dark:bg-indigo-500/5 p-6 lg:p-8 rounded-3xl border border-indigo-100 dark:border-indigo-500/20 shadow-sm mt-4 animate-in fade-in slide-in-from-top-4 duration-500">
                                <div class="md:col-span-2 flex items-center gap-3 mb-2">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center">
                                        <flux:icon.user-group variant="mini" class="text-indigo-600 dark:text-indigo-400" />
                                    </div>
                                    <flux:heading size="sm" class="text-indigo-900 dark:text-indigo-200 uppercase tracking-wider font-bold">Principal Specific Details</flux:heading>
                                </div>

                                <flux:field>
                                    <flux:label>Principal Recruitment Category</flux:label>
                                    <flux:select wire:model.live.debounce.150ms="principalCategory" class="rounded-xl!">
                                        <option value="">Select Category</option>
                                        @foreach ($principalCategoriesOption as $data)
                                        <option value="{{ $data->category_id }}">{{ $data->category_name }}</option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="principalCategory" />
                                </flux:field>

                                <flux:field>
                                    <flux:label>Cadre Medium</flux:label>
                                    <flux:select wire:model.live.debounce.150ms="cadreMedium" class="rounded-xl!">
                                        <option value="">Select Medium</option>
                                        @foreach ($appointmentMediumOptions as $medium)
                                        <option value="{{ $medium->medium_id }}">{{ $medium->name }}</option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="cadreMedium" />
                                </flux:field>

                                <flux:field>
                                    <flux:label>Cadre Subject</flux:label>
                                    <flux:select wire:model.live.debounce.150ms="cadreSubject" class="rounded-xl!">
                                        <option value="">Select Subject</option>
                                        @foreach ($principalSubjectOption as $data)
                                        <option value="{{ $data->subject_id }}">{{ $data->name_en }}</option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="cadreSubject" />
                                </flux:field>
                            </div>
                            @endif

                            <!-- SLEAS SPECIFIC -->
                            @if($service === 'SER005')
                            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-8 items-start bg-emerald-50/50 dark:bg-emerald-500/5 p-6 lg:p-8 rounded-3xl border border-emerald-100 dark:border-emerald-500/20 shadow-sm mt-4 animate-in fade-in slide-in-from-top-4 duration-500">
                                <div class="md:col-span-2 flex items-center gap-3 mb-2">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                                        <flux:icon.briefcase class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                                    </div>
                                    <flux:heading size="sm" class="text-emerald-900 dark:text-emerald-200 uppercase tracking-wider font-bold">SLEAS Specific Details</flux:heading>
                                </div>

                                <flux:field>
                                    <flux:label>Recruitment Category</flux:label>
                                    <flux:select wire:model.live.debounce.150ms="recruitmentCategory" class="rounded-xl!">
                                        <option value="">Select Category</option>
                                        @foreach ($recruitmentCategoryOption as $data)
                                        <option value="{{ $data->category_id }}">{{ $data->category_name }}</option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="recruitmentCategory" />
                                </flux:field>

                                <flux:field>
                                    <flux:label>Recruitment Subject</flux:label>
                                    <flux:select wire:model.live.debounce.150ms="recruitmentSubject" class="rounded-xl!">
                                        <option value="">Select Subject</option>
                                        @foreach ($recruitmentSubjectOption as $data)
                                        <option value="{{ $data->eas_subject_id }}">{{ $data->subject }}</option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="recruitmentSubject" />
                                </flux:field>

                                <flux:field>
                                    <flux:label>Cadre Medium</flux:label>
                                    <flux:select wire:model.live.debounce.150ms="cadreMedium" class="rounded-xl!">
                                        <option value="">Select Medium</option>
                                        @foreach ($appointmentMediumOptions as $medium)
                                        <option value="{{ $medium->medium_id }}">{{ $medium->name }}</option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="cadreMedium" />
                                </flux:field>
                            </div>
                            @endif
                        </div>
                        @endif

                        <div class="flex justify-between pt-8 border-t border-slate-100 dark:border-zinc-800">
                            <flux:button type="button" wire:click="previousStep" variant="filled" class="rounded-xl!">Back</flux:button>
                            <flux:button type="button" wire:click="nextStep" variant="primary" class="rounded-xl! bg-indigo-600! hover:bg-indigo-700!">Continue to Status</flux:button>
                        </div>
                    </div>
                </div>
                @endif

                <!-- STEP 5: FINAL STATUS -->
                @if ($step === 5)
                <div class="space-y-10 animate-in fade-in slide-in-from-right-4 duration-500">
                    <div class="mt-6 mx-auto max-w-3xl space-y-10">
                        <div class="bg-slate-50/50 dark:bg-zinc-900/30 p-8 rounded-4xl border border-slate-100 dark:border-zinc-800 shadow-sm">
                            <flux:radio.group wire:model.live="regType" label="Registration Type" variant="cards" class="flex flex-col md:flex-row gap-4">
                                <flux:radio value="new" label="Fresh New Appointment" description="First time appointment for this service" icon="sparkles" class="flex-1" />
                                <flux:radio value="existing" label="New Registration" description="Already in service, updating current status" icon="user-plus" class="flex-1" />
                            </flux:radio.group>
                        </div>
                        <div class="flex flex-col gap-2 text-center">
                            @if($regType === 'existing')
                            <p class="text-sm text-red-600 dark:text-red-300">මුල් පත්වීමේ ස්ථානය වෙනස් වී ඇත්නම් හෝ පත්වීම සිදු කරන ලද සේවයේ තරාතිරම වෙනස් වී ඇත්නම් මෙම කොටස සම්පූර්ණ කරන්න.</p>
                            <p class="text-sm text-red-600 dark:text-red-300">Complete this section if the place of original appointment has changed or if the rank of the service to which the appointment was made has changed.</p>
                            @endif
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            <flux:field>
                                <flux:label>Appointed Date</flux:label>
                                <flux:input type="date" wire:model="currentAppointmentDate" :disabled="$regType === 'new'" class="rounded-xl!" />
                                <flux:error name="currentAppointmentDate" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Appointment Letter Number</flux:label>
                                <flux:input wire:model="currentAppointmentLetterNo" :disabled="$regType === 'new'" class="rounded-xl!" />
                                <flux:error name="currentAppointmentLetterNo" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Service Rank</flux:label>
                                <flux:select wire:model="currentServiceRank" :disabled="$regType === 'new'" class="rounded-xl!">
                                    <option value="">Select Rank</option>
                                    @foreach($currentRanksOption as $opt) <option value="{{ $opt->rank_id }}">{{ $opt->rank_name }}</option> @endforeach
                                </flux:select>
                                <flux:error name="currentServiceRank" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Position</flux:label>
                                <flux:select wire:model="currentPosition" :disabled="$regType === 'new'" class="rounded-xl!">
                                    <option value="">Select Position</option>
                                    @foreach($currentPositionOption as $opt) <option value="{{ $opt->position_id }}">{{ $opt->position_name }}</option> @endforeach
                                </flux:select>
                                <flux:error name="currentPosition" />
                            </flux:field>


                            <flux:field>
                                <flux:label>Working Place Level</flux:label>
                                <flux:select wire:model.live="currentOfficeLevel" :disabled="$regType === 'new'" class="rounded-xl!">
                                    <option value="">Select Level</option>
                                    @foreach ($officeLevelOption as $level)
                                    <option value="{{ $level->office_level_id }}">{{ $level->office_level_name }}</option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="officeLevel" />
                            </flux:field>

                            @if ($currentOfficeLevel == 'OLID006')
                            <flux:field>
                                <flux:label>Zonal Education Office</flux:label>
                                <flux:select wire:model.live="currentZonalEducationOffice" :disabled="$regType === 'new'" class="rounded-xl!">
                                    <option value="">Select Zonal Office</option>
                                    @foreach ($zonalEducationOfficeOption as $zone)
                                    <option value="{{ $zone->workplace_id }}">{{ $zone->short_name }}</option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="currentZonalEducationOffice" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Institution Category</flux:label>
                                <flux:select wire:model.live="currentInstitutionCategory" :disabled="$regType === 'new'" class="rounded-xl!">
                                    <option value="">Select Category</option>
                                    @foreach ($institutionCategoryOption as $data)
                                    <option value="{{ $data->institution_category_id }}">{{ $data->institution_category_name }}</option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="currentInstitutionCategory" />
                            </flux:field>
                            @endif

                            <flux:field>
                                <flux:label>Working Place</flux:label>
                                <flux:select wire:model.live="currentWorkingPlace" :disabled="$regType === 'new'" class="rounded-xl!">
                                    <option value="">Select Workplace</option>
                                    @foreach ($currentWorkingPlaceOption as $office)
                                    <option value="{{ $office->workplace_id }}">{{ $office->office_name }}</option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="currentWorkingPlace" />
                            </flux:field>

                            <!-- Cadre Details (Principal/SLEAS at Institution) -->
                            @if(in_array($service, ['SER004', 'SER005']) && $currentOfficeLevel === 'OLID006')
                            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6 bg-blue-50/30 dark:bg-blue-500/5 p-6 rounded-2xl border border-blue-100 dark:border-blue-500/20 mt-2">
                                <div class="md:col-span-2 flex items-center gap-2 mb-2">
                                    <flux:icon.academic-cap variant="mini" class="text-blue-600 dark:text-blue-400" />
                                    <flux:heading size="sm" class="text-blue-900 dark:text-blue-200 font-bold uppercase tracking-wider">Cadre Information</flux:heading>
                                </div>

                                <flux:field>
                                    <flux:label>Cadre Subject</flux:label>
                                    <flux:select wire:model="cadreSubject" class="rounded-xl!">
                                        <option value="">Select Subject</option>
                                        @foreach ($principalSubjectOption as $data)
                                        <option value="{{ $data->subject_id }}">{{ $data->name_en }}</option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="cadreSubject" />
                                </flux:field>

                                <flux:field>
                                    <flux:label>Cadre Medium</flux:label>
                                    <flux:select wire:model="cadreMedium" class="rounded-xl!">
                                        <option value="">Select Medium</option>
                                        @foreach ($appointmentMediumOptions as $medium)
                                        <option value="{{ $medium->medium_id }}">{{ $medium->name }}</option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="cadreMedium" />
                                </flux:field>
                            </div>
                            @endif
                        </div>
                        <div class="flex justify-between items-center pt-8 border-t border-slate-100 dark:border-zinc-800">
                            <flux:button type="button" wire:click="previousStep" variant="filled" class="rounded-xl!">Back to Appointment</flux:button>
                            <flux:button type="button" wire:click="save" variant="primary" class="rounded-xl! bg-indigo-600! hover:bg-indigo-700! px-8" loading="save">
                                Confirm & Register Employee
                            </flux:button>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </flux:card>
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
