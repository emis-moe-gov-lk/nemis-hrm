<section class="w-full">
    {{-- 1. Header Section --}}
    <header class="mb-10">
        <flux:heading size="xl" level="1" class="!text-3xl font-black tracking-tight text-slate-900 dark:text-white leading-none mb-3">
            {{ __('Institution Profile') }}
        </flux:heading>
        <flux:subheading size="lg" class="text-slate-500 dark:text-slate-500 font-medium max-w-2xl">
            {{ __('Comprehensive administrative records, location data, and organizational settings.') }}
        </flux:subheading>
    </header>

    <x-institutions.institution-layout :institutionId="$id">
        <div class="mt-8 space-y-10">
            {{-- Main Content Card --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-[2.5rem] shadow-sm overflow-hidden">
                <div class="p-8 md:p-12 space-y-12">
                    
                    {{-- Basic Information Section --}}
                    <div class="relative">
                        <div class="absolute -left-12 top-0 bottom-0 w-1 bg-indigo-500 rounded-full opacity-20 hidden md:block"></div>
                        <livewire:institutions.profile.basic-information :institutionId="$id" />
                    </div>

                    {{-- Contact Information Section --}}
                    <div class="relative pt-12 border-t border-slate-200 dark:border-slate-700">
                        <div class="absolute -left-12 top-12 bottom-0 w-1 bg-blue-500 rounded-full opacity-20 hidden md:block"></div>
                        <livewire:institutions.profile.contact-information :institutionId="$id" />
                    </div>

                    {{-- Location & Administration Section --}}
                    <div class="relative pt-12 border-t border-slate-200 dark:border-slate-700">
                        <div class="absolute -left-12 top-12 bottom-0 w-1 bg-emerald-500 rounded-full opacity-20 hidden md:block"></div>
                        <livewire:institutions.profile.location-and-administration :institutionId="$id" />
                    </div>

                    {{-- Mission & Vision Section --}}
                    <div class="relative pt-12 border-t border-slate-200 dark:border-slate-700">
                        <div class="absolute -left-12 top-12 bottom-0 w-1 bg-purple-500 rounded-full opacity-20 hidden md:block"></div>
                        <livewire:institutions.profile.mission-vision :institutionId="$id" />
                    </div>

                    {{-- System Hash / Footer Meta --}}
                    <div class="pt-8 mt-12 border-t border-slate-200 dark:border-slate-700 flex flex-col md:flex-row justify-between items-center gap-4">
                        <div class="flex items-center gap-3 px-4 py-2 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                            <flux:icon name="key" variant="micro" class="text-slate-500" />
                            <span class="text-[10px] font-mono text-slate-500 dark:text-slate-500 uppercase tracking-widest">
                                {{ __('System Key:') }} <span class="text-indigo-600 dark:text-indigo-400 font-bold">{{ $institution->id }}</span>
                            </span>
                        </div>
                        <p class="text-[10px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-[0.3em]">
                            {{ __('CEMIS Core Infrastructure') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </x-institutions.institution-layout>
</section>