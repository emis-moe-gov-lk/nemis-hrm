<section class="w-full">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Zonal Education Office') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">
            {{ __('Statistics about Zonal Education Office structure and staff distribution.') }}
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <x-offices.zeo.zeo-layout :officeId="$officeId">
        <!-- Main Stats Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Students Card -->
            <div class="group relative overflow-hidden rounded-xl p-6 shadow-lg border border-gray-200 dark:border-slate-700 bg-gradient-to-br from-white to-gray-50 dark:from-slate-800 dark:to-slate-900 hover:shadow-xl  hover:-translate-y-1">
                <div class="absolute top-0 right-0 w-20 h-20 bg-blue-100 dark:bg-blue-900/20 rounded-full -translate-y-10 translate-x-10 group-hover:scale-125 transition-transform duration-300"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-4xl font-bold text-slate-900 dark:text-white">{{ $studentCount }}</div>
                        <svg class="w-8 h-8 text-blue-500 dark:text-blue-400 opacity-80" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16">
                            <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4" />
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

            <!-- Divisions Card -->
            <div class="group relative overflow-hidden rounded-xl p-6 shadow-lg border border-gray-200 dark:border-slate-700 bg-gradient-to-br from-white to-gray-50 dark:from-slate-800 dark:to-slate-900 hover:shadow-xl  hover:-translate-y-1">
                <div class="absolute top-0 right-0 w-20 h-20 bg-emerald-100 dark:bg-emerald-900/20 rounded-full -translate-y-10 translate-x-10 group-hover:scale-125 transition-transform duration-300"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-4xl font-bold text-slate-900 dark:text-white">{{ $divisionCount }}</div>
                        <svg class="w-8 h-8 text-emerald-500 dark:text-emerald-400 opacity-80" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-building-check" viewBox="0 0 16 16">
                            <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m1.679-4.493-1.335 2.226a.75.75 0 0 1-1.174.144l-.774-.773a.5.5 0 0 1 .708-.708l.547.548 1.17-1.951a.5.5 0 1 1 .858.514" />
                            <path d="M2 1a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6.5a.5.5 0 0 1-1 0V1H3v14h3v-2.5a.5.5 0 0 1 .5-.5H8v4H3a1 1 0 0 1-1-1z" />
                            <path d="M4.5 2a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm-6 3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm-6 3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5z" />
                        </svg>
                    </div>
                    <div class="text-lg font-semibold text-slate-800 dark:text-slate-200">Divisional Offices</div>
                    <div class="text-sm text-slate-500 dark:text-slate-400 mt-1">Divisions under Zones</div>
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-slate-700">
                        <div class="text-xs text-slate-400 dark:text-slate-500 flex items-center">
                            <span class="inline-block w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
                            Administrative Units
                        </div>
                    </div>
                </div>
            </div>

            <!-- Institutions Card -->
            <div class="group relative overflow-hidden rounded-xl p-6 shadow-lg border border-gray-200 dark:border-slate-700 bg-gradient-to-br from-white to-gray-50 dark:from-slate-800 dark:to-slate-900 hover:shadow-xl  hover:-translate-y-1">
                <div class="absolute top-0 right-0 w-20 h-20 bg-violet-100 dark:bg-violet-900/20 rounded-full -translate-y-10 translate-x-10 group-hover:scale-125 transition-transform duration-300"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-4xl font-bold text-slate-900 dark:text-white">{{ $institutionCount }}</div>
                        <svg class="w-8 h-8 text-violet-500 dark:text-violet-400 opacity-80" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-building" viewBox="0 0 16 16">
                            <path d="M4 2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zM4 5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM7.5 5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM4.5 8a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5z" />
                            <path d="M2 1a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1zm11 0H3v14h3v-2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5V15h3z" />
                        </svg>
                    </div>
                    <div class="text-lg font-semibold text-slate-800 dark:text-slate-200">Schools/Institutions</div>
                    <div class="text-sm text-slate-500 dark:text-slate-400 mt-1">Schools under Divisions</div>
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-slate-700">
                        <div class="text-xs text-slate-400 dark:text-slate-500 flex items-center">
                            <span class="inline-block w-2 h-2 bg-violet-500 rounded-full mr-2"></span>
                            Educational Institutions
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Staff by Service Section -->
        <div class="mt-12">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">Staff by Service</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Distribution of staff across different service categories</p>
                </div>
                <div class="px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800">
                    <span class="text-sm font-medium text-blue-700 dark:text-blue-300">{{ count($serviceCounts) }} Services</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
                @php
                // Calculate max staff count for progress bars
                $maxStaffCount = 0;
                foreach ($serviceCounts as $service) {
                if ($service['staff_count'] > $maxStaffCount) {
                $maxStaffCount = $service['staff_count'];
                }
                }
                // Ensure we don't divide by zero
                $maxStaffCount = $maxStaffCount > 0 ? $maxStaffCount : 1;
                @endphp

                @foreach ($serviceCounts as $index => $service)
                @php
                $colorIndex = $index % 4;

                $gradientClass = match($colorIndex) {
                0 => 'from-blue-500 to-blue-400',
                1 => 'from-emerald-500 to-emerald-400',
                2 => 'from-amber-500 to-amber-400',
                default => 'from-violet-500 to-violet-400',
                };

                $barClass = match($colorIndex) {
                0 => 'bg-blue-500',
                1 => 'bg-emerald-500',
                2 => 'bg-amber-500',
                default => 'bg-violet-500',
                };

                $percentage = min(($service['staff_count'] / $maxStaffCount) * 100, 100);
                @endphp
                <div class="group relative overflow-hidden rounded-xl p-5 shadow border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:shadow-lg hover:-translate-y-1">
                    <!-- Gradient accent -->
                    <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b {{ $gradientClass }}"></div>

                    <div class="ml-3">
                        <div class="flex items-start justify-between mb-3">
                            <div class="text-3xl font-bold text-slate-900 dark:text-white">{{ $service['staff_count'] }}</div>
                            <div class="p-2 rounded-lg bg-slate-50 dark:bg-slate-700/50 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-5 h-5 text-slate-600 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                        </div>

                        <div class="mb-2">
                            <div class="text-base font-semibold text-slate-800 dark:text-slate-200 line-clamp-1">{{ $service['name'] }}</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $service['description'] }}</div>
                        </div>

                        <!-- Progress bar -->
                        <div class="mt-4">
                            <div class="flex justify-between text-xs text-slate-500 dark:text-slate-400 mb-1">
                                <span>Capacity</span>
                                <span>{{ round($percentage) }}%</span>
                            </div>
                            <div class="h-1.5 w-full bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full rounded-full {{ $barClass }}"
                                    style="width: {{ number_format($percentage, 2) }}%;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </x-offices.zeo.zeo-layout>
</section>