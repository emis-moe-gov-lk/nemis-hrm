<section class="w-full">
    <x-offices.zeo.zeo-layout :officeId="$officeId">
        {{-- Main Stats Section --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
            {{-- Students Card --}}
            <div class="group relative overflow-hidden rounded-3xl p-7 border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 dark:bg-indigo-950/20 rounded-bl-full -translate-y-2 translate-x-2 group-hover:scale-110 transition-transform duration-300"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-xs font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">{{ __('Student Population') }}</div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400">
                            <flux:icon name="users" variant="solid" class="w-6 h-6" />
                        </div>
                    </div>
                    <div class="text-5xl font-black text-slate-900 dark:text-white tracking-tight mb-2">
                        {{ number_format($studentCount) }}
                    </div>
                    <div class="text-xs font-bold text-slate-500 dark:text-slate-500">
                        {{ __('Estimated Total Students enrolled in active schools') }}
                    </div>
                    <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800 flex items-center gap-2">
                        <span class="inline-block w-2.5 h-2.5 bg-indigo-500 rounded-full animate-pulse"></span>
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">{{ __('Active Academic Year') }}</span>
                    </div>
                </div>
            </div>

            {{-- Divisions Card --}}
            <div class="group relative overflow-hidden rounded-3xl p-7 border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-50 dark:bg-emerald-950/20 rounded-bl-full -translate-y-2 translate-x-2 group-hover:scale-110 transition-transform duration-300"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-xs font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">{{ __('Divisions') }}</div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400">
                            <flux:icon name="building-office" variant="solid" class="w-6 h-6" />
                        </div>
                    </div>
                    <div class="text-5xl font-black text-slate-900 dark:text-white tracking-tight mb-2">
                        {{ number_format($divisionCount) }}
                    </div>
                    <div class="text-xs font-bold text-slate-500 dark:text-slate-500">
                        {{ __('Divisional offices under Zonal administration') }}
                    </div>
                    <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800 flex items-center gap-2">
                        <span class="inline-block w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse"></span>
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">{{ __('Administrative Units') }}</span>
                    </div>
                </div>
            </div>

            {{-- Institutions Card --}}
            <div class="group relative overflow-hidden rounded-3xl p-7 border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="absolute top-0 right-0 w-24 h-24 bg-violet-50 dark:bg-violet-950/20 rounded-bl-full -translate-y-2 translate-x-2 group-hover:scale-110 transition-transform duration-300"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-xs font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">{{ __('Schools') }}</div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-50 dark:bg-violet-950/40 text-violet-600 dark:text-violet-400">
                            <flux:icon name="academic-cap" variant="solid" class="w-6 h-6" />
                        </div>
                    </div>
                    <div class="text-5xl font-black text-slate-900 dark:text-white tracking-tight mb-2">
                        {{ number_format($institutionCount) }}
                    </div>
                    <div class="text-xs font-bold text-slate-500 dark:text-slate-500">
                        {{ __('Schools and educational centers under supervision') }}
                    </div>
                    <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800 flex items-center gap-2">
                        <span class="inline-block w-2.5 h-2.5 bg-violet-500 rounded-full animate-pulse"></span>
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">{{ __('Educational Institutions') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Staff by Service Section --}}
        <div class="mt-12">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ __('Staff by Service') }}</h2>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-500 mt-1">{{ __('Distribution of personnel across different official services.') }}</p>
                </div>
                <div class="px-3.5 py-1.5 rounded-full bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-800">
                    <span class="text-xs font-black text-indigo-700 dark:text-indigo-400 uppercase tracking-widest">{{ count($serviceCounts) }} {{ __('Services') }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                $maxStaffCount = 0;
                foreach ($serviceCounts as $service) {
                if ($service['staff_count'] > $maxStaffCount) {
                $maxStaffCount = $service['staff_count'];
                }
                }
                $maxStaffCount = $maxStaffCount > 0 ? $maxStaffCount : 1;
                @endphp

                @foreach ($serviceCounts as $index => $service)
                @php
                $colorIndex = $index % 4;

                $gradientClass = match($colorIndex) {
                0 => 'from-blue-500 to-indigo-400',
                1 => 'from-emerald-500 to-teal-400',
                2 => 'from-amber-500 to-orange-400',
                default => 'from-violet-500 to-fuchsia-400',
                };

                $barClass = match($colorIndex) {
                0 => 'bg-indigo-600 dark:bg-indigo-500',
                1 => 'bg-emerald-600 dark:bg-emerald-500',
                2 => 'bg-amber-600 dark:bg-amber-500',
                default => 'bg-violet-600 dark:bg-violet-500',
                };

                $percentage = min(($service['staff_count'] / $maxStaffCount) * 100, 100);
                @endphp
                <div class="group relative overflow-hidden rounded-3xl p-6 border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                    {{-- Decorative edge color --}}
                    <div class="absolute top-0 left-0 w-1.5 h-full bg-linear-to-b {{ $gradientClass }}"></div>

                    <div class="pl-2 flex flex-col justify-between h-full">
                        <div>
                            <div class="flex items-start justify-between mb-4">
                                <div class="text-4xl font-black text-slate-950 dark:text-white tracking-tight">{{ number_format($service['staff_count']) }}</div>
                                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-400 dark:text-slate-500 group-hover:scale-110 transition-transform duration-300 shadow-inner">
                                    <flux:icon name="users" class="w-5 h-5" />
                                </div>
                            </div>

                            <div class="mb-5">
                                <h3 class="text-base font-black text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors leading-tight">{{ $service['name'] }}</h3>
                                <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 mt-1 leading-relaxed line-clamp-2">{{ $service['description'] ?? __('No description provided') }}</p>
                            </div>
                        </div>

                        {{-- Progress bar with beautiful small value formatting --}}
                        <div class="mt-2 border-t border-slate-100 dark:border-slate-800/80 pt-4">
                            <div class="flex justify-between text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1.5">
                                <span>{{ __('Capacity') }}</span>
                                <span>
                                    @if ($service['staff_count'] > 0 && $percentage < 1)
                                        {{ number_format($percentage, 1) }}%
                                        @else
                                        {{ round($percentage) }}%
                                        @endif
                                        </span>
                            </div>
                            <div class="h-2 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden shadow-inner">
                                <div class="h-full rounded-full {{ $barClass }} transition-all duration-500"
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