<div>
    <section>
        <div class="mb-3">
            <div class="flex items-baseline justify-between py-2">
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200">Contact Details</h2>
                @can('institution.contact_details.update')
                <flux:modal.trigger name="edit-profile-contact-info">
                    <flux:button>Edit</flux:button>
                </flux:modal.trigger>
                @endcan
            </div>
            <flux:separator variant="subtle" />
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4">
            @php
            $contactInfo = [
            'Email' => $institution->email,
            'Phone' => $institution->phone,
            ];
            @endphp
            @foreach ($contactInfo as $label => $value)
            <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ $label }}</p>
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    @if ($label === 'Email')
                    <a href="mailto:{{ $value }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ $value ?? 'N/A' }}</a>
                    @elseif ($label === 'Phone')
                    <a href="tel:{{ $value }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ $value ?? 'N/A' }}</a>
                    @else
                    {{ $value ?? 'N/A' }}
                    @endif
                </p>
            </div>
            @endforeach
        </div>
    </section>

    <flux:modal name="edit-profile-contact-info" class="md:w-150">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Update profile</flux:heading>
                <flux:text class="mt-2">Make changes to your personal details.</flux:text>
            </div>

            <form wire:submit.prevent="updateContactInformation">
                @csrf
                <div class="space-y-4">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <flux:field>
                                <flux:input label="Intitute Number" id="intituteNumber" type="text" readonly variant="filled" wire:model.live="intituteNumber" placeholder="Intitute Number" />
                            </flux:field>
                        </div>
                        <div>
                            <flux:field>
                                <flux:input label="Cences Number" id="cencesNumber" type="text" readonly variant="filled" wire:model.live="cencesNumber" placeholder="Cences Number" />
                            </flux:field>
                        </div>
                    </div>

                    <div class="flex flex-col gap-4">
                        <flux:field>
                            <flux:input label="Email" id="email" type="text" wire:model.live="email" placeholder="Email" />
                        </flux:field>
                    </div>

                    <div class="flex flex-col gap-4">
                        <flux:field>
                            <flux:input label="Contact" id="phone" type="text" wire:model.live="phone" placeholder="contact" />
                        </flux:field>
                    </div>
                </div>

                <div class="flex mt-4">
                    <flux:spacer />

                    <div class="flex gap-2">
                        <flux:button type="button" wire:click="resetForm">Reset</flux:button>
                        <flux:button type="submit" variant="primary">Save changes</flux:button>
                    </div>
                </div>
            </form>
        </div>
    </flux:modal>

</div>