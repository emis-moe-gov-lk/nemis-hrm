<section class="w-full">
    <x-offices.peo.peo-layout :officeId="$officeId">
        <!-- Main Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <!-- Zonal Offices -->
            <div
                class="group relative overflow-hidden rounded-xl p-6 shadow-lg border border-gray-200 dark:border-slate-700
                        bg-linear-to-br from-white to-gray-50 dark:from-slate-800 dark:to-slate-900
                        hover:shadow-xl hover:-translate-y-1 transition">
                <div
                    class="absolute top-0 right-0 w-20 h-20 bg-violet-100 dark:bg-violet-900/20 rounded-full
                            -translate-y-10 translate-x-10 group-hover:scale-125 transition-transform">
                </div>

                <div class="relative">
                    <div class="text-4xl font-bold text-slate-900 dark:text-white">{{ $zonalOfficeCount }}</div>
                    <div class="text-lg font-semibold text-slate-800 dark:text-slate-200 mt-1">Zonal Offices</div>
                    <div class="text-sm text-slate-500 dark:text-slate-500">Zones under Provinces</div>
                </div>
            </div>

            <!-- Divisions -->
            <div
                class="group relative overflow-hidden rounded-xl p-6 shadow-lg border border-gray-200 dark:border-slate-700
                        bg-linear-to-br from-white to-gray-50 dark:from-slate-800 dark:to-slate-900
                        hover:shadow-xl hover:-translate-y-1 transition">
                <div
                    class="absolute top-0 right-0 w-20 h-20 bg-amber-100 dark:bg-amber-900/20 rounded-full
                            -translate-y-10 translate-x-10 group-hover:scale-125 transition-transform">
                </div>

                <div class="relative">
                    <div class="text-4xl font-bold text-slate-900 dark:text-white">{{ $divisionCount }}</div>
                    <div class="text-lg font-semibold text-slate-800 dark:text-slate-200 mt-1">
                        Divisional Offices
                    </div>
                    <div class="text-sm text-slate-500 dark:text-slate-500">
                        Divisions under Zones
                    </div>
                </div>
            </div>

            <!-- Institutions -->
            <div
                class="group relative overflow-hidden rounded-xl p-6 shadow-lg border border-gray-200 dark:border-slate-700
                        bg-linear-to-br from-white to-gray-50 dark:from-slate-800 dark:to-slate-900
                        hover:shadow-xl hover:-translate-y-1 transition">
                <div
                    class="absolute top-0 right-0 w-20 h-20 bg-rose-100 dark:bg-rose-900/20 rounded-full
                            -translate-y-10 translate-x-10 group-hover:scale-125 transition-transform">
                </div>

                <div class="relative">
                    <div class="text-4xl font-bold text-slate-900 dark:text-white">{{ $institutionCount }}</div>
                    <div class="text-lg font-semibold text-slate-800 dark:text-slate-200 mt-1">Institutions</div>
                    <div class="text-sm text-slate-500 dark:text-slate-500">
                        Schools under Divisions
                    </div>
                </div>
            </div>

            <!-- Students -->
            <div
                class="group relative overflow-hidden rounded-xl p-6 shadow-lg border border-gray-200 dark:border-slate-700
                        bg-linear-to-br from-white to-gray-50 dark:from-slate-800 dark:to-slate-900
                        hover:shadow-xl hover:-translate-y-1 transition">
                <div
                    class="absolute top-0 right-0 w-20 h-20 bg-blue-100 dark:bg-blue-900/20 rounded-full
                            -translate-y-10 translate-x-10 group-hover:scale-125 transition-transform">
                </div>

                <div class="relative">
                    <div class="text-4xl font-bold text-slate-900 dark:text-white">{{ $studentCount }}</div>
                    <div class="text-lg font-semibold text-slate-800 dark:text-slate-200 mt-1">Students</div>
                    <div class="text-sm text-slate-500 dark:text-slate-500">Estimated Total Students</div>
                </div>
            </div>

        </div>

        <!-- Staff by Service -->
        <div class="mt-12">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">Staff by Service</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-500 mt-1">
                        Distribution of staff across services
                    </p>
                </div>
                <span
                    class="px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/30
                   border border-blue-100 dark:border-blue-800
                   text-sm font-medium text-blue-700 dark:text-blue-300">
                    {{ count($serviceCounts) }} Services
                </span>
            </div>

            @php
            // Calculate max staff count for progress bars
            $maxStaffCount = collect($serviceCounts)->max('staff_count') ?? 1;
            $maxStaffCount = $maxStaffCount > 0 ? $maxStaffCount : 1;
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($serviceCounts as $index => $service)
                @php
                $colorIndex = $index % 4;

                $gradientClass = match ($colorIndex) {
                0 => 'from-blue-500 to-blue-400',
                1 => 'from-emerald-500 to-emerald-400',
                2 => 'from-amber-500 to-amber-400',
                default => 'from-violet-500 to-violet-400',
                };

                $barClass = match ($colorIndex) {
                0 => 'bg-blue-500',
                1 => 'bg-emerald-500',
                2 => 'bg-amber-500',
                default => 'bg-violet-500',
                };

                $percentage = min(($service['staff_count'] / $maxStaffCount) * 100, 100);
                @endphp

                <div
                    class="group relative overflow-hidden rounded-xl p-5 shadow border border-gray-200 dark:border-slate-700
                       bg-white dark:bg-slate-800 hover:shadow-lg hover:-translate-y-1 transition">

                    <!-- Gradient accent -->
                    <div class="absolute top-0 left-0 w-1 h-full bg-linear-to-b {{ $gradientClass }}"></div>

                    <div class="ml-3">
                        <div class="flex items-start justify-between mb-3">
                            <div class="text-3xl font-bold text-slate-900 dark:text-white">
                                {{ $service['staff_count'] }}
                            </div>

                            <div
                                class="p-2 rounded-lg bg-slate-50 dark:bg-slate-700/50
                                   group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-5 h-5 text-slate-600 dark:text-slate-300" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2
                                         c0-.656-.126-1.283-.356-1.857M7 20H2v-2
                                         a3 3 0 015.356-1.857M7 20v-2
                                         c0-.656.126-1.283.356-1.857m0 0
                                         a5.002 5.002 0 019.288 0M15 7
                                         a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                        </div>

                        <div class="mb-2">
                            <div class="text-base font-semibold text-slate-800 dark:text-slate-200 line-clamp-1">
                                {{ $service['name_en'] }}
                            </div>
                            <div class="text-xs text-slate-500 dark:text-slate-500">
                                Total Staff in this Service
                            </div>
                        </div>

                        <!-- Progress bar -->
                        <div class="mt-4">
                            <div class="flex justify-between text-xs text-slate-500 dark:text-slate-500 mb-1">
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

    </x-offices.peo.peo-layout>
</section>