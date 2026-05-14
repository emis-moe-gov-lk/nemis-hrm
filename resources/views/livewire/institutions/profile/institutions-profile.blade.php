<section class="w-full">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('School Profile') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Manage School profile and settings') }}
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <x-institutions.institution-layout :institutionId="$id">
        <div>
            <div class="antialiased min-h-screen">

                {{-- Main Card --}}
                <div class="max-w-5xl bg-white dark:bg-gray-800 p-6 rounded-b-lg">

                    {{-- Main Content --}}
                    <div class="space-y-6">

                        {{-- Basic Information --}}
                        <livewire:institutions.profile.basic-information :institutionId="$id" />

                        {{-- Contact Information --}}
                        <livewire:institutions.profile.contact-information :institutionId="$id" />

                        {{-- Location & Administration --}}
                        <livewire:institutions.profile.location-and-administration :institutionId="$id" />

                        {{-- Mission & Vision --}}
                        <livewire:institutions.profile.mission-vision :institutionId="$id" />

                        {{-- System Hash --}}
                        <section class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-xs font-mono text-gray-500 dark:text-gray-600">
                                **System Key (Hash):** {{ $institution->id }}
                            </p>
                        </section>

                    </div>
                </div>
            </div>
        </div>
    </x-institutions.institution-layout>
</section>