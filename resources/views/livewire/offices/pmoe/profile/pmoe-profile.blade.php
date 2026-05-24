<section class="w-full">
    <x-offices.pmoe.layout :officeid="$id">
        {{-- 1. Header Section --}}
        <header class="mb-10">
            <flux:heading size="xl" level="1">
                {{ __('PMOE Profile') }}
            </flux:heading>
            <flux:subheading size="lg" class="text-slate-500 dark:text-slate-500 font-medium max-w-2xl">
                {{ __('Manage and view the profile of the provincial ministry of education, their roles, and professional history within this region.') }}
            </flux:subheading>
        </header>
        <div class="mt-8 space-y-10">
            {{-- Main Content Card --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-[2.5rem] shadow-sm overflow-hidden">
                <div class="p-8 md:p-12 space-y-12">

                    {{-- Administration Information Section --}}
                    <div class="relative">
                        <div class="absolute -left-12 top-0 bottom-0 w-1 bg-indigo-500 rounded-full opacity-20 hidden md:block"></div>
                        <section>
                            <div class="mb-3">
                                <div class="flex items-baseline justify-between py-2">
                                    <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200">Administration Information</h2>
                                </div>
                                <flux:separator variant="subtle" />
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Workplace ID</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $provincialEducationMinistry->workplace_id ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </section>
                    </div>

                    {{-- Contact Details Section --}}
                    <div class="relative pt-12 border-t border-slate-200 dark:border-slate-700">
                        <div class="absolute -left-12 top-12 bottom-0 w-1 bg-blue-500 rounded-full opacity-20 hidden md:block"></div>
                        <section>
                            <div class="mb-3">
                                <div class="flex items-baseline justify-between py-2">
                                    <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200">Contact Details</h2>
                                    <flux:modal.trigger name="edit-pmoe-contact">
                                        <flux:button>Edit</flux:button>
                                    </flux:modal.trigger>
                                </div>
                                <flux:separator variant="subtle" />
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Email</p>
                                    <p class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                        <a href="mailto:{{ $provincialEducationMinistry->email }}">{{ $provincialEducationMinistry->email ?? 'N/A' }}</a>
                                    </p>
                                </div>
                                <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Phone</p>
                                    <p class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                        <a href="tel:{{ $provincialEducationMinistry->phone }}">{{ $provincialEducationMinistry->phone ?? 'N/A' }}</a>
                                    </p>
                                </div>
                            </div>
                        </section>
                    </div>

                    {{-- Location Section --}}
                    <div class="relative pt-12 border-t border-slate-200 dark:border-slate-700">
                        <div class="absolute -left-12 top-12 bottom-0 w-1 bg-emerald-500 rounded-full opacity-20 hidden md:block"></div>
                        <section>
                            <div class="mb-3">
                                <div class="flex items-baseline justify-between py-2">
                                    <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200">Location</h2>
                                    <flux:modal.trigger name="edit-pmoe-location">
                                        <flux:button>Edit</flux:button>
                                    </flux:modal.trigger>
                                </div>
                                <flux:separator variant="subtle" />
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="col-span-2 sm:col-span-1 p-3 bg-gray-100 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Address</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $provincialEducationMinistry->address ?? 'N/A' }}
                                    </p>
                                </div>
                                <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Postal Code</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $provincialEducationMinistry->postal_code ?? 'N/A' }}
                                    </p>
                                </div>
                                <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Latitude</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $provincialEducationMinistry->latitude ?? 'N/A' }}
                                    </p>
                                </div>
                                <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Longitude</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $provincialEducationMinistry->longitude ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </section>
                    </div>

                    {{-- Mission & Vision Section --}}
                    <div class="relative pt-12 border-t border-slate-200 dark:border-slate-700">
                        <div class="absolute -left-12 top-12 bottom-0 w-1 bg-amber-500 rounded-full opacity-20 hidden md:block"></div>
                        <section>
                            <div class="mb-3">
                                <div class="flex items-baseline justify-between py-2">
                                    <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200">Mission & Vision</h2>
                                    <flux:modal.trigger name="edit-pmoe-mission-vision">
                                        <flux:button>Edit</flux:button>
                                    </flux:modal.trigger>
                                </div>
                                <flux:separator variant="subtle" />
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Mission</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $provincialEducationMinistry->mission ?? 'N/A' }}
                                    </p>
                                </div>
                                <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Vision</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $provincialEducationMinistry->vision ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </section>
                    </div>

                    {{-- System Hash / Footer Meta --}}
                    <div class="pt-8 mt-12 border-t border-slate-200 dark:border-slate-700 flex flex-col md:flex-row justify-between items-center gap-4">
                        <div class="flex items-center gap-3 px-4 py-2 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                            <flux:icon name="key" variant="micro" class="text-slate-500" />
                            <span class="text-[10px] font-mono text-slate-500 dark:text-slate-500 uppercase tracking-widest">
                                {{ __('System Key:') }} <span class="text-indigo-600 dark:text-indigo-400 font-bold">{{ $provincialEducationMinistry->id }}</span>
                            </span>
                        </div>
                        <p class="text-[10px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-[0.3em]">
                            {{ __('CEMIS Core Infrastructure') }}
                        </p>
                    </div>

                </div>
            </div>
        </div>

        {{-- ── Edit Contact Details Modal ───────────────────────────────── --}}
        <flux:modal name="edit-pmoe-contact" class="md:w-130">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Update Contact Details</flux:heading>
                    <flux:text class="mt-1">Update the email address and phone number for this office.</flux:text>
                </div>
                <form wire:submit.prevent="updateContactDetails" class="space-y-4">
                    @csrf
                    <flux:field>
                        <flux:input label="Email" id="email" type="email" wire:model.live="email" placeholder="office@email.com" />
                        @error('email') <flux:error>{{ $message }}</flux:error> @enderror
                    </flux:field>
                    <flux:field>
                        <flux:input label="Phone" id="phone" type="text" wire:model.live="phone" placeholder="+94 11 234 5678" />
                        @error('phone') <flux:error>{{ $message }}</flux:error> @enderror
                    </flux:field>
                    <div class="flex justify-end gap-3 pt-2">
                        <flux:modal.close>
                            <flux:button variant="ghost">Cancel</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary">Save Changes</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- ── Edit Location Modal ──────────────────────────────────────── --}}
        <flux:modal name="edit-pmoe-location" class="md:w-130">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Update Location</flux:heading>
                    <flux:text class="mt-1">Update the office address, postal code, and geographic coordinates.</flux:text>
                </div>
                <form wire:submit.prevent="updateLocation" class="space-y-4">
                    @csrf
                    <flux:field>
                        <flux:textarea label="Address" wire:model.live="address" placeholder="Enter full office address" rows="3" />
                        @error('address') <flux:error>{{ $message }}</flux:error> @enderror
                    </flux:field>
                    <flux:field>
                        <flux:input label="Postal Code" type="text" wire:model.live="postal_code" placeholder="e.g. 10000" />
                        @error('postal_code') <flux:error>{{ $message }}</flux:error> @enderror
                    </flux:field>
                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:input label="Latitude" id="latitude" type="text" wire:model.live="latitude" placeholder="e.g. 6.9271" />
                            @error('latitude') <flux:error>{{ $message }}</flux:error> @enderror
                        </flux:field>
                        <flux:field>
                            <flux:input label="Longitude" id="longitude" type="text" wire:model.live="longitude" placeholder="e.g. 79.8612" />
                            @error('longitude') <flux:error>{{ $message }}</flux:error> @enderror
                        </flux:field>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <flux:modal.close>
                            <flux:button variant="ghost">Cancel</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary">Save Changes</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- ── Edit Mission & Vision Modal ──────────────────────────────── --}}
        <flux:modal name="edit-pmoe-mission-vision" class="md:w-130">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Update Mission & Vision</flux:heading>
                    <flux:text class="mt-1">Update the strategic mission and vision statements.</flux:text>
                </div>
                <form wire:submit.prevent="updateMissionVision" class="space-y-4">
                    @csrf
                    <flux:field>
                        <flux:textarea label="Mission" wire:model.live="mission" placeholder="Enter mission statement" rows="4" />
                        @error('mission') <flux:error>{{ $message }}</flux:error> @enderror
                    </flux:field>
                    <flux:field>
                        <flux:textarea label="Vision" wire:model.live="vision" placeholder="Enter vision statement" rows="4" />
                        @error('vision') <flux:error>{{ $message }}</flux:error> @enderror
                    </flux:field>
                    <div class="flex justify-end gap-3 pt-2">
                        <flux:modal.close>
                            <flux:button variant="ghost">Cancel</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary">Save Changes</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

    </x-offices.pmoe.layout>
</section>