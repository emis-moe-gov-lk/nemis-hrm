<section class="w-full">
    <x-offices.peo.peo-layout :officeId="$id">

        <div class="antialiased min-h-screen">
            <div class="relative mb-6 mx-4 w-full">
                <flux:heading size="xl" level="1">{{ __('Profile') }}</flux:heading>
                <flux:subheading size="lg" class="mb-6">
                    {{ __('Basic information about the Provincial Education Office.') }}
                </flux:subheading>
            </div>

            {{-- Main Card --}}
            <div class="max-w-5xl bg-white dark:bg-gray-800 p-6 rounded-b-lg">
                {{-- Main Content --}}
                <div class="space-y-6">

                    {{-- 1. Basic Information --}}
                    <section>
                        <div class="mb-3">
                            <div class="flex items-baseline justify-between py-2">
                                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200">Basic Information</h2>
                                <flux:button icon="pencil-square" size="sm" variant="primary">Edit</flux:button>
                            </div>
                            <flux:separator variant="subtle" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4">
                            {{-- Email --}}
                            <div
                                class="p-2 bg-gray-50 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Email</p>
                                <p class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                    <a href="mailto:{{ $provincialEducationOffice->email }}">{{ $provincialEducationOffice->email ?? 'N/A' }}</a>
                                </p>
                            </div>

                            {{-- Phone --}}
                            <div
                                class="p-2 bg-gray-50 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Phone</p>
                                <p class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                    <a href="tel:{{ $provincialEducationOffice->phone }}">{{ $provincialEducationOffice->phone ?? 'N/A' }}</a>
                                </p>
                            </div>

                            {{-- Address --}}
                            <div
                                class="col-span-2 p-3 bg-gray-100 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Address</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $provincialEducationOffice->address ?? 'N/A' }} <br />
                                    {{ $provincialEducationOffice->postal_code ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </section>

                    {{-- 3. Location & Administration --}}
                    <section>
                        <div class="mb-3">
                            <div class="flex items-baseline justify-between py-2">
                                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200">Location & Administration</h2>
                                <flux:button icon="pencil-square" size="sm" variant="primary">Edit</flux:button>
                            </div>
                            <flux:separator variant="subtle" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div
                                class="p-2 bg-gray-50 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Latitude</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $provincialEducationOffice->latitude ?? 'N/A' }}
                                </p>
                            </div>

                            <div
                                class="p-2 bg-gray-50 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Longitude</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $provincialEducationOffice->longitude ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </section>

                    {{-- 4. Mission & Vision --}}
                    <section>
                        <div class="mb-3">
                            <div class="flex items-baseline justify-between py-2">
                                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200">Mission & Vision</h2>
                                <flux:button icon="pencil-square" size="sm" variant="primary">Edit</flux:button>
                            </div>
                            <flux:separator variant="subtle" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div
                                class="p-3 bg-gray-100 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Mission</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $provincialEducationOffice->mission ?? 'N/A' }}
                                </p>
                            </div>

                            <div
                                class="p-3 bg-gray-100 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Vision</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $provincialEducationOffice->vision ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </section>

                    {{-- 5. System Hash --}}
                    <section class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-xs font-mono text-gray-500 dark:text-gray-600">
                            **System Key (Hash):** {{ $provincialEducationOffice->id }}
                        </p>
                    </section>

                </div>
            </div>
        </div>

    </x-offices.peo.peo-layout>
</section>