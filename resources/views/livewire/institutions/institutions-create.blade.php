<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Create Institution') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Create institution profile and account') }}
        </flux:subheading>
        <flux:separator variant="subtle" />

        <form wire:submit.prevent="save" class="mt-6 max-w-xl space-y-6">

            <div class="flex flex-col space-y-4">

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

                <div class="flex flex-col md:flex-row gap-4">
                    <!-- census_no -->
                    <div class="md:w-1/2 w-full">
                        <flux:field>
                            <flux:input type="number" label="Census No" wire:model.live="censusNo"
                                placeholder="Enter census number" mask="99999" />
                        </flux:field>
                        @if ($censusNo)
                        @if ($censusExists)
                        <div role="alert" aria-live="polite" aria-atomic="true" class="mt-3 text-sm font-medium text-red-500 dark:text-red-400" data-flux-error="">
                            <svg class="shrink-0 [:where(&amp;)]:size-5 inline" data-flux-icon="" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495ZM10 5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 5Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"></path>
                            </svg>
                            This census number is already exists.
                        </div>
                        @else
                        <div role="alert" aria-live="polite" aria-atomic="true" class="mt-3 text-sm font-medium text-green-500 dark:text-green-400" data-flux-error="">
                            <svg class="shrink-0 [:where(&)]:size-5 inline text-green-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-10.707a1 1 0 00-1.414-1.414L9 9.586 7.707 8.293A1 1 0 006.293 9.707l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            This census number is available.
                        </div>
                        @endif
                        @endif
                    </div>
                    <div class="md:w-1/2 w-full">
                        <flux:field>
                            <flux:input type="text" label="Workplace ID" wire:model.live="workplaceId"
                                placeholder="Enter workplace ID" mask="99999" disabled />
                        </flux:field>
                    </div>
                </div>

                <div class="flex flex-col">
                    <flux:field>
                        <flux:select label="Provincial Education Office" id="provincialOffice" wire:model.live="provincialOffice">
                            <option value="">{{ __ ('Select Provincial Education Office') }}</option>
                            @foreach ($provinceOption as $data)
                            <option value="{{ $data->workplace_id }}">{{ $data->name }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                </div>


                <div class="flex flex-col md:flex-row gap-4">
                    <div class="md:w-1/2 w-full">
                        <flux:field>
                            <flux:select label="Zonal Education Office" id="zonalOffice" wire:model.live="zonalOffice">
                                <option>{{ __ ('Select Education Zone') }}</option>
                                @foreach ($zonalOfficeOption as $data)
                                <option value="{{ $data->workplace_id }}">{{ $data->short_name }}</option>
                                @endforeach
                            </flux:select>
                        </flux:field>
                    </div>

                    <div class="md:w-1/2 w-full">
                        <flux:field>
                            <flux:select label="Divisional Education Office" id="divisionOffice" wire:model.live="divisionOffice">
                                <option value="">{{ __ ('Select Education Division') }}</option>
                                @foreach ($divisionOfficeOption as $data)
                                <option value="{{ $data->workplace_id }}">{{ $data->short_name }}</option>
                                @endforeach
                            </flux:select>
                        </flux:field>
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    <flux:field>
                        <flux:select label="Administrative District" id="district" wire:model.live="district">
                            <option value="">{{ __ ('Select Administrative District') }}</option>
                            @foreach ($districtOption as $data)
                            <option value="{{ $data->district_id }}">{{ $data->district_name }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                </div>

                <div class="flex flex-col md:flex-row gap-4">
                    <div class="md:w-1/2 w-full">
                        <flux:field>
                            <flux:select label="Institution Category" id="institutionCategory" wire:model.live="institutionCategory">
                                <option value="">{{ __ ('Select Institution Category') }}</option>
                                @foreach ($institutionCategoryOption as $data)
                                <option value="{{ $data->institution_category_id }}">{{ $data->institution_category_name }}</option>
                                @endforeach
                            </flux:select>
                        </flux:field>
                    </div>

                    <div class="md:w-1/2 w-full">
                        <flux:field>
                            <flux:select label="Authority Category" id="authorityCategory" wire:model.live="authorityCategory">
                                <option value="">{{ __ ('Select Authority Category') }}</option>
                                @foreach ($authorityOption as $data)
                                <option value="{{ $data->authority_id }}">{{ $data->authority_name }}</option>
                                @endforeach
                            </flux:select>
                        </flux:field>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-4">
                    <div class="md:w-1/2 w-full">
                        <flux:field>
                            <flux:select label="Institution Type" id="institutionType" wire:model.live="institutionType">
                                <option value="">{{ __ ('Select Institution Type') }}</option>
                                @foreach ($institutionTypeOption as $data)
                                <option value="{{ $data->institution_types_id }}">{{ $data->institution_types_name }}</option>
                                @endforeach
                            </flux:select>
                        </flux:field>
                    </div>

                    <div class="md:w-1/2 w-full">
                        <flux:field>
                            <flux:select label="Language Category" id="languageId" wire:model.live="languageId">
                                <option value="">{{ __ ('Select Language Category') }}</option>
                                @foreach ($institutionLanguagesOption as $data)
                                <option value="{{ $data->language_id }}">{{ $data->name }}</option>
                                @endforeach
                            </flux:select>
                        </flux:field>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-4">
                    <div class="md:w-1/2 w-full">
                        <flux:field>
                            <flux:select label="Gradespan" id="gradespanId" wire:model.live="gradespanId">
                                <option value="">{{ __ ('Select Gradespan') }}</option>
                                @foreach ($gradeSpanOption as $data)
                                <option value="{{ $data->grade_span_id }}">{{ $data->grade_span_name }}</option>
                                @endforeach
                            </flux:select>
                        </flux:field>
                    </div>

                    <div class="md:w-1/2 w-full">
                        <flux:field>
                            <flux:select label="Ethnisity" id="institutionEthnisityId" wire:model.live="institutionEthnisityId">
                                <option value="">{{ __ ('Select Ethnisity') }}</option>
                                @foreach ($institutionEthnisityOption as $data)
                                <option value="{{ $data->ethnicity_id }}">{{ $data->ethnicity_name }}</option>
                                @endforeach
                            </flux:select>
                        </flux:field>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-4">
                    <div class="md:w-1/2 w-full">
                        <flux:field>
                            <flux:select label="Institution Gender" id="institutionGenderId" wire:model.live="institutionGenderId">
                                <option value="">{{ __ ('Select Institution Gender') }}</option>
                                @foreach ($institutionGenderOption as $data)
                                <option value="{{ $data->gender_id }}">{{ $data->name }}</option>
                                @endforeach
                            </flux:select>
                        </flux:field>
                    </div>

                    <div class="md:w-1/2 w-full">
                        <flux:field>
                            <flux:select label="Facility" id="institutionalFacilityId" wire:model.live="institutionalFacilityId">
                                <option value="">{{ __ ('Select Facility') }}</option>
                                @foreach ($institutionalFacilityOption as $data)
                                <option value="{{ $data->facilities_id }}">{{ $data->name }}</option>
                                @endforeach
                            </flux:select>
                        </flux:field>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-4">
                    <div class="md:w-1/2 w-full">
                        <flux:field>
                            <flux:input label="Established Year" id="establishedYear" type="number" wire:model.live="establishedYear" placeholder="Established Year" mask="99999" min="1901" />
                        </flux:field>
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    <flux:field>
                        <flux:input label="Institution Name" id="institution_name" type="text" wire:model.live="institutionName" placeholder="Institution Name" />
                    </flux:field>
                </div>

                <div class="flex flex-col gap-4">
                    <flux:field>
                        <flux:input label="Other Name (Optional)" id="otherName" type="text" wire:model.live="otherName" placeholder="Other Name (Optional)" />
                    </flux:field>
                </div>

                <div>
                    <flux:heading size="lg" level="2" class="mt-8 mb-4">Contact Details & Location Information</flux:heading>
                    <flux:field>
                        <div class="flex flex-col md:flex-row gap-4">
                            <div class="md:w-4/5 w-full">
                                <flux:input label="Address Line 1" id="addressLine1" class="mb-2" type="text" wire:model.live="addressLine1" placeholder="Address Line 1" />
                            </div>
                            <div class="md:w-1/5 w-full">
                                <flux:input label="Postal Code" id="postalCode" type="number" wire:model.live="postalCode" placeholder="Postal Code" mask="99999" />
                            </div>
                        </div>
                    </flux:field>
                </div>

                <div class="flex flex-col gap-4">
                    <flux:field>
                        <flux:input label="Email Address" id="email" type="email" wire:model.live="email" placeholder="mail@example.com" />
                    </flux:field>
                </div>

                <div class="flex flex-col gap-4">
                    <flux:field>
                        <flux:input label="Contact Number" id="contactNumber" type="phone" wire:model.live="contactNumber" placeholder="055-5555555" mask="999-9999999" />
                    </flux:field>
                </div>

                <div class="flex flex-col gap-4">
                    <flux:field>
                        <div class="flex flex-col md:flex-row gap-4">
                            <div class="md:w-1/2 w-full">
                                <flux:input label="Latitude (5.916 - 9.835)" id="latitude" type="number" wire:model.live="latitude" step="0.000001" min="5.916" max="9.835" placeholder="{{ __('Enter latitude (5.916 - 9.835)') }}" />
                            </div>
                            <div class="md:w-1/2 w-full">
                                <flux:input label="longitude (79.652 - 81.881)" id="longitude" type="number" wire:model.live="longitude" step="0.000001" min="79.652" max="81.881" placeholder="{{ __('Enter longitude (79.652 - 81.881)') }}" />
                            </div>
                        </div>
                    </flux:field>
                </div>

                <div class="flex flex-col gap-4">
                    <flux:field>
                        <flux:select label="Institution Status" id="status" wire:model.live="status">
                            <option value="">{{ __('Select Status') }}</option>
                            <option value="1">{{ __('Active') }}</option>
                            <option value="0">{{ __('Inactive') }}</option>
                        </flux:select>
                    </flux:field>
                </div>


            </div>

            <div class="flex justify-end">
                <flux:field>
                    <flux:button type="submit" variant="primary">
                        Create Institution
                    </flux:button>
                </flux:field>
            </div>
        </form>

    </div>
</div>