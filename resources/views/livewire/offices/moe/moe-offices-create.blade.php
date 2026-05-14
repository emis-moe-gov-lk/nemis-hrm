<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Create Ministry of Education Office') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Create Ministry of Education Office profile and account') }}
        </flux:subheading>
        <flux:separator variant="subtle" />

        <form wire:submit.prevent="save" class="mt-6 max-w-xl space-y-6">

            <div class="flex flex-col space-y-4">

                <div class="flex flex-col md:flex-row gap-4">
                    <div class="md:w w-full">
                    <!-- workplaceId Code -->
                        <flux:field>
                            <flux:input label="Work Place ID" wire:model.live="workplaceId"
                                placeholder="MOE0000001" mask="MOE9999999"/>
                        </flux:field>

                        @if ($workplaceId)
                            @if ($workplaceIdExists)
                                <div role="alert" aria-live="polite" aria-atomic="true" class="mt-3 text-sm font-medium text-red-500 dark:text-red-400" data-flux-error="">
                                    <svg class="shrink-0 [:where(&amp;)]:size-5 inline" data-flux-icon="" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495ZM10 5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 5Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"></path>
                                    </svg>
                                    This Work Place ID is already exists.
                                </div>
                            @else
                                <div role="alert" aria-live="polite" aria-atomic="true" class="mt-3 text-sm font-medium text-green-500 dark:text-green-400" data-flux-error="">
                                    <svg class="shrink-0 [:where(&)]:size-5 inline text-green-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-10.707a1 1 0 00-1.414-1.414L9 9.586 7.707 8.293A1 1 0 006.293 9.707l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    This Work Place ID is available.
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    <flux:field>
                        <flux:input label="Ministry of Education Name" id="moeName" type="text" wire:model.live="moeName" placeholder="Ministry of Education Name"/>
                    </flux:field>
                </div>

                <div class="flex flex-col gap-4">
                    <flux:field>
                        <flux:input label="Short Name (Optional)" id="otherName" type="text" wire:model.live="otherName" placeholder="Other Name (Optional)"/>
                    </flux:field>
                </div>

                <div>
                    <flux:heading size="lg" level="2" class="mt-8 mb-4">Contact Details & Location Information</flux:heading>
                    <flux:field>
                        <div class="flex flex-col md:flex-row gap-4">
                            <div class="md:w-1/2 w-full">
                                <flux:input label="Address Line 1" id="addressLine1" class="mb-2" type="text" wire:model.live="addressLine1" placeholder="Address Line 1"/>
                            </div>
                            <div class="md:w-1/2 w-full">
                                <flux:input label="Address Line 2" id="addressLine2" class="mb-2" type="text" wire:model.live="addressLine2" placeholder="Address Line 2"/>
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row gap-4">
                            <div class="md:w-4/5 w-full">
                                <flux:input label="City" id="city" type="text" wire:model.live="city" placeholder="City"/>
                            </div>
                            <div class="md:w-1/5 w-full">
                                <flux:input label="Postal Code" id="postalCode" type="number" wire:model.live="postalCode" placeholder="Postal Code" mask="99999"/>
                            </div>
                        </div>
                    </flux:field>
                </div>

                <div class="flex flex-col gap-4">
                    <flux:field>
                        <flux:input label="Email Address" id="email" type="email" wire:model.live="email" placeholder="mail@example.com"/>
                    </flux:field>
                </div>

                <div class="flex flex-col gap-4">
                    <flux:field>
                        <flux:input label="Contact Number" id="contactNumber" type="phone" wire:model.live="contactNumber" placeholder="055-5555555" mask="999-9999999"/>
                    </flux:field>
                </div>

                <div class="flex flex-col gap-4">
                    <flux:field>
                        <div class="flex flex-col md:flex-row gap-4">
                            <div class="md:w-1/2 w-full">
                                <flux:input label="Latitude (5.916 - 9.835)" id="latitude" type="number" wire:model.live="latitude" step="0.000001" min="5.916" max="9.835" placeholder="{{ __('Enter latitude (5.916 - 9.835)') }}" />
                            </div>
                            <div class="md:w-1/2 w-full">
                                <flux:input label="longitude (79.652 - 81.881)" id="longitude" type="number" wire:model.live="longitude" step="0.000001" min="79.652" max="81.881" placeholder="{{ __('Enter longitude (79.652 - 81.881)') }}"/>
                            </div>
                        </div>
                    </flux:field>
                </div>

                <div class="flex flex-col gap-4">
                    <flux:field>
                        <flux:select label="Office Status" id="status" wire:model.live="status">
                            <option value="Active">{{ __('Active') }}</option>
                            <option value="Inactive">{{ __('Inactive') }}</option>
                        </flux:select>
                    </flux:field>
                </div>
            </div>

            <div class="flex justify-end">
                <flux:field>
                    <flux:button type="submit" variant="primary">
                        Create Ministry of Education
                    </flux:button>
                </flux:field>
            </div>
        </form>

        @if (session()->has('success'))
            <div class="mt-4 text-success">
                {{ session('success') }}
            </div>
        @endif

    </div>
</div>
