<div class="antialiased min-h-screen">

    {{-- Main Card --}}
    <div class="max-w-5xl bg-white dark:bg-gray-800 p-6 rounded-b-lg">

        {{-- Header --}}
        <div class="flex items-center space-x-4 mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
            <div
                class="relative w-20 h-20 sm:w-24 sm:h-24 rounded-lg overflow-hidden border-2 {{ $zonalEducationOffice->active_status ? 'border-green-500' : 'border-red-500' }}">

                @if ($zonalEducationOffice->logo)
                    <img src="{{ asset('storage/' . $zonalEducationOffice->logo) }}"
                        alt="{{ $zonalEducationOffice->name }}"
                        class="w-full h-full object-cover {{ $zonalEducationOffice->active_status ? '' : 'grayscale opacity-60' }}">
                    @unless ($zonalEducationOffice->active_status)
                        <div
                            class="absolute inset-0 flex items-center justify-center bg-red-500 bg-opacity-30 text-white text-xs font-bold">
                            Inactive
                        </div>
                    @endunless
                @else
                    <div class="w-full h-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 21V8a1 1 0 011-1h4a1 1 0 011 1v13M5 21V11a1 1 0 011-1h12a1 1 0 011 1v10M3 21h18" />
                        </svg>
                    </div>
                @endif
            </div>

            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                    {{ $zonalEducationOffice->name }}
                </h1>
                @if ($zonalEducationOffice->short_name)
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $zonalEducationOffice->short_name }}
                    </p>
                @endif
            </div>
        </div>

        {{-- Main Content --}}
        <div class="space-y-6">

            {{-- 1. Basic Information --}}
            <section>
                <div class="mb-3">
                    <div class="flex items-baseline justify-between py-2">
                        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200">Basic Information</h2>
                    </div>
                    <flux:separator variant="subtle" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div
                        class="p-2 bg-gray-50 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Workplace ID</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $zonalEducationOffice->workplace_id ?? 'N/A' }}
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

                    {{-- Address --}}
                    <div
                        class="col-span-2 p-3 bg-gray-100 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Address</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $zonalEducationOffice->address ?? 'N/A' }}
                        </p>
                    </div>

                    {{-- Postal Code --}}
                    <div
                        class="p-2 bg-gray-50 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Postal Code</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $zonalEducationOffice->postal_code ?? 'N/A' }}
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

