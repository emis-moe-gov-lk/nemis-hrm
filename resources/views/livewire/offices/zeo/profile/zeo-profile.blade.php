<section class="w-full">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Zonal Education Office') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">
            {{ __('Statistics about Zonal Education Office structure and staff distribution.') }}
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <x-offices.zeo.zeo-layout :officeId="$id">

        <div class="antialiased min-h-screen">

            {{-- Main Content --}}
            <div class="max-w-5xl bg-white dark:bg-gray-800 p-6 rounded-b-lg">

                {{-- Main Content --}}
                <div class="space-y-6">

                    {{-- 1. Administration Information --}}
                    <section>
                        <div class="mb-3">
                            <div class="flex items-baseline justify-between py-2">
                                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200">Administration Information</h2>
                            </div>
                            <flux:separator variant="subtle" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4">
                            <div
                                class="p-2 bg-gray-50 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Provincial department of Education</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $zonalEducationOffice->provincialEducationOffice->short_name ?? 'N/A' }}
                                </p>
                            </div>
                            <div
                                class="p-2 bg-gray-50 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">District</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $zonalEducationOffice->district->district_name ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </section>

                    {{-- 2. Contact Information --}}
                    <section>
                        <div class="mb-3">
                            <div class="flex items-baseline justify-between py-2">
                                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200">Contact Details</h2>
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
                                    <a href="mailto:{{ $zonalEducationOffice->email }}">{{ $zonalEducationOffice->email ?? 'N/A' }}</a>
                                </p>
                            </div>

                            {{-- Phone --}}
                            <div
                                class="p-2 bg-gray-50 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Phone</p>
                                <p class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                    <a href="tel:{{ $zonalEducationOffice->phone }}">{{ $zonalEducationOffice->phone ?? 'N/A' }}</a>
                                </p>
                            </div>
                        </div>
                    </section>

                    {{-- 3. Location --}}
                    <section>
                        <div class="mb-3">
                            <div class="flex items-baseline justify-between py-2">
                                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200">Location</h2>
                                <flux:button icon="pencil-square" size="sm" variant="primary">Edit</flux:button>
                            </div>
                            <flux:separator variant="subtle" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Address --}}
                            <div
                                class="col-span-2 p-3 bg-gray-100 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Address</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $zonalEducationOffice->address ?? 'N/A' }}
                                </p>
                            </div>
                            <div
                                class="p-2 bg-gray-50 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Latitude</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $zonalEducationOffice->latitude ?? 'N/A' }}
                                </p>
                            </div>

                            <div
                                class="p-2 bg-gray-50 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Longitude</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $zonalEducationOffice->longitude ?? 'N/A' }}
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
                                    {{ $zonalEducationOffice->mission ?? 'N/A' }}
                                </p>
                            </div>

                            <div
                                class="p-3 bg-gray-100 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Vision</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $zonalEducationOffice->vision ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </section>

                    {{-- 5. System Hash --}}
                    <section class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-xs font-mono text-gray-500 dark:text-gray-600">
                            **System Key (Hash):** {{ $zonalEducationOffice->id }}
                        </p>
                    </section>

                </div>
            </div>
        </div>

        </x-offices.zoe.zeo-layout>
</section>