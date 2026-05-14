<section class="w-full">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Divisional Education Office') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">
            {{ __('Statistics about Divisional Education Office structure and staff distribution.') }}
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <x-offices.deo.deo-layout :officeId="$officeId">

        <!-- ================= MAIN STATS ================= -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 mb-8">

            <!-- Students Card -->
            <div class="group relative overflow-hidden rounded-xl p-6 shadow-lg border border-gray-200 dark:border-slate-700
                bg-gradient-to-br from-white to-gray-50 dark:from-slate-800 dark:to-slate-900
                hover:shadow-xl hover:-translate-y-1 transition-all">

                <div class="absolute top-0 right-0 w-20 h-20 bg-blue-100 dark:bg-blue-900/20
                    rounded-full -translate-y-10 translate-x-10 group-hover:scale-125 transition-transform duration-300"></div>

                <div class="relative">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-4xl font-bold text-slate-900 dark:text-white">
                            {{ $studentCount }}
                        </div>
                        <svg class="w-8 h-8 text-blue-500 dark:text-blue-400 opacity-80" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4" />
                        </svg>
                    </div>

                    <div class="text-lg font-semibold text-slate-800 dark:text-slate-200">Students</div>
                    <div class="text-sm text-slate-500 dark:text-slate-400 mt-1">Estimated Total Students</div>

                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-slate-700">
                        <div class="text-xs text-slate-400 dark:text-slate-500 flex items-center">
                            <span class="inline-block w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                            Active Academic Year
                        </div>
                    </div>
                </div>
            </div>

            <!-- Institutions Card -->
            <div class="group relative overflow-hidden rounded-xl p-6 shadow-lg border border-gray-200 dark:border-slate-700
                bg-gradient-to-br from-white to-gray-50 dark:from-slate-800 dark:to-slate-900
                hover:shadow-xl hover:-translate-y-1 transition-all">

                <div class="absolute top-0 right-0 w-20 h-20 bg-emerald-100 dark:bg-emerald-900/20
                    rounded-full -translate-y-10 translate-x-10 group-hover:scale-125 transition-transform duration-300"></div>

                <div class="relative">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-4xl font-bold text-slate-900 dark:text-white">
                            {{ $institutionCount }}
                        </div>
                        <svg class="w-8 h-8 text-emerald-500 dark:text-emerald-400 opacity-80" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M2 1a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z" />
                        </svg>
                    </div>

                    <div class="text-lg font-semibold text-slate-800 dark:text-slate-200">Schools / Institutions</div>
                    <div class="text-sm text-slate-500 dark:text-slate-400 mt-1">Institutions under Division</div>

                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-slate-700">
                        <div class="text-xs text-slate-400 dark:text-slate-500 flex items-center">
                            <span class="inline-block w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
                            Educational Units
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- ================= STAFF BY SERVICE ================= -->
        <div class="mt-12">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">Staff by Service</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Distribution of staff across service categories
                    </p>
                </div>
                <div class="px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800">
                    <span class="text-sm font-medium text-blue-700 dark:text-blue-300">
                        {{ count($serviceCounts) }} Services
                    </span>
                </div>
            </div>

            @php
                $maxStaffCount = collect($serviceCounts)->max('staff_count') ?: 1;
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($serviceCounts as $index => $service)
                    <div class="group relative overflow-hidden rounded-xl p-5 shadow border border-gray-200 dark:border-slate-700
                        bg-white dark:bg-slate-800 hover:shadow-lg hover:-translate-y-1 transition-all">

                        <!-- Accent -->
                        <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b
                            @switch($index % 4)
                                @case(0) from-blue-500 to-blue-400 @break
                                @case(1) from-emerald-500 to-emerald-400 @break
                                @case(2) from-amber-500 to-amber-400 @break
                                @default from-violet-500 to-violet-400
                            @endswitch">
                        </div>

                        <div class="ml-3">
                            <div class="flex items-start justify-between mb-3">
                                <div class="text-3xl font-bold text-slate-900 dark:text-white">
                                    {{ $service['staff_count'] }}
                                </div>
                            </div>

                            <div class="mb-2">
                                <div class="text-base font-semibold text-slate-800 dark:text-slate-200">
                                    {{ $service['name_en'] }}
                                </div>
                                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                    Staff in this service
                                </div>
                            </div>

                            <!-- Progress -->
                            <div class="mt-4">
                                <div class="flex justify-between text-xs text-slate-500 dark:text-slate-400 mb-1">
                                    <span>Capacity</span>
                                    <span>{{ round(($service['staff_count'] / $maxStaffCount) * 100) }}%</span>
                                </div>
                                <div class="h-1.5 w-full bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full
                                        @switch($index % 4)
                                            @case(0) bg-blue-500 @break
                                            @case(1) bg-emerald-500 @break
                                            @case(2) bg-amber-500 @break
                                            @default bg-violet-500
                                        @endswitch"
                                        style="width: {{ min(($service['staff_count'] / $maxStaffCount) * 100, 100) }}%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </x-offices.deo.deo-layout>
</section>
