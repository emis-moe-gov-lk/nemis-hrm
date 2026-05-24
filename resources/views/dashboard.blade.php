<x-layouts.app :title="__('Dashboard')">
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-10">

        <div class="xl:col-span-2 space-y-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight flex flex-wrap gap-x-2 gap-y-1">
                        <span><span class="text-transparent bg-clip-text bg-linear-to-r from-indigo-600 to-blue-500 dark:from-indigo-400 dark:to-blue-400">E</span>ducation</span>
                        <span><span class="text-transparent bg-clip-text bg-linear-to-r from-indigo-600 to-blue-500 dark:from-indigo-400 dark:to-blue-400">M</span>anagement</span>
                        <span><span class="text-transparent bg-clip-text bg-linear-to-r from-indigo-600 to-blue-500 dark:from-indigo-400 dark:to-blue-400">I</span>nformation</span>
                        <span><span class="text-transparent bg-clip-text bg-linear-to-r from-indigo-600 to-blue-500 dark:from-indigo-400 dark:to-blue-400">S</span>ystem</span>
                    </h1>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="flex h-2 w-2 rounded-full bg-indigo-500"></span>
                        <p class="text-slate-500 dark:text-zinc-400 font-medium text-sm capitalize">{{ $user_roles->pluck('name')->implode(', ') }} • {{ now()->format('l, F j, Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-3xl bg-linear-to-br from-[#635BFF] via-[#564df0] to-[#4338ca] p-6 md:p-8 shadow-xl shadow-indigo-200/40 dark:shadow-none text-white border border-white/10">
                <div class="relative z-10 md:w-3/4 lg:w-2/3 space-y-4">
                    <div class="inline-flex items-center px-3 py-1 rounded-full bg-white/15 backdrop-blur-xl border border-white/10 text-[9px] font-black tracking-[0.2em] uppercase text-indigo-50">
                        <span class="mr-1.5 flex h-1.5 w-1.5 rounded-full bg-teal-400 animate-pulse"></span>
                        {{ now()->format('F j, Y') }}
                    </div>

                    <div class="space-y-1.5">
                        <h3 class="text-xl md:text-2xl lg:text-3xl font-black tracking-tight leading-tight">
                            Welcome Back,
                            <span class="text-transparent bg-clip-text bg-linear-to-r from-white to-indigo-100 italic">{{ $user->name }}</span>!
                        </h3>
                        <p class="text-indigo-50/80 text-sm font-medium leading-snug">
                            Your profile is <span class="text-white font-bold px-1.5 py-0.5 rounded-lg bg-white/10 border border-white/20">{{ ($people?->appointment?->is_verified == 1) ? 'Verified' : 'Not Verified' }}</span>.
                            <span class="text-xs opacity-70 italic text-indigo-200 ml-1">Keep your information up to date.</span>
                        </p>
                    </div>

                    @can('my-profile.general.view')
                    <div class="pt-2">
                        <a href="{{ route('my-profile.index') }}"
                            class="inline-flex items-center gap-2 bg-white text-indigo-600 px-5 py-3 rounded-xl font-bold text-xs hover:scale-105 hover:bg-indigo-50 transition-all duration-300 shadow-lg shadow-white/20">
                            <span>👤</span>
                            My Profile
                        </a>
                    </div>
                    @endcan
                </div>

                <div class="hidden md:block absolute right-4 lg:right-10 bottom-0 z-10 pointer-events-none">
                    <img src="{{ asset('images/welcome.png') }}" alt="Dashboard Illustration"
                        class="h-32 md:h-44 object-contain drop-shadow-[0_10px_30px_rgba(0,0,0,0.25)] origin-bottom transition-transform duration-700 hover:scale-105">
                </div>

                <div class="absolute -right-16 -bottom-16 w-64 h-64 bg-white/10 rounded-full blur-[60px] pointer-events-none z-0"></div>
                <div class="absolute left-1/3 top-0 w-32 h-32 bg-indigo-300/20 rounded-full blur-2xl pointer-events-none z-0"></div>
            </div>


            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">

                {{-- Zonal Offices --}}
                <div class="group relative bg-white dark:bg-zinc-900/50 border border-slate-300 dark:border-zinc-700 rounded-2xl p-4 transition-all duration-300 hover:border-violet-500 hover:shadow-md">
                    <div class="absolute left-0 top-4 bottom-4 w-1 bg-violet-500 rounded-r-full opacity-40 group-hover:opacity-100 transition-opacity"></div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-slate-50 dark:bg-zinc-800 text-slate-500 dark:text-slate-500 rounded-xl flex items-center justify-center group-hover:bg-violet-50 dark:group-hover:bg-violet-500/10 group-hover:text-violet-600 transition-colors shrink-0">
                            <flux:icon.building-office-2 class="size-5" />
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest leading-none">Zonal Offices</p>
                            <p class="text-xl font-black text-slate-900 dark:text-white leading-tight mt-0.5">{{ number_format($zonalOfficeCount) }}</p>
                        </div>
                    </div>
                </div>

                {{-- Divisional Offices --}}
                <div class="group relative bg-white dark:bg-zinc-900/50 border border-slate-300 dark:border-zinc-700 rounded-2xl p-4 transition-all duration-300 hover:border-sky-500 hover:shadow-md">
                    <div class="absolute left-0 top-4 bottom-4 w-1 bg-sky-500 rounded-r-full opacity-40 group-hover:opacity-100 transition-opacity"></div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-slate-50 dark:bg-zinc-800 text-slate-500 dark:text-slate-500 rounded-xl flex items-center justify-center group-hover:bg-sky-50 dark:group-hover:bg-sky-500/10 group-hover:text-sky-600 transition-colors shrink-0">
                            <flux:icon.building-office class="size-5" />
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest leading-none">Divisional Offices</p>
                            <p class="text-xl font-black text-slate-900 dark:text-white leading-tight mt-0.5">{{ number_format($divisionalOfficeCount) }}</p>
                        </div>
                    </div>
                </div>

                {{-- Total Institutions --}}
                <div class="group relative bg-white dark:bg-zinc-900/50 border border-slate-300 dark:border-zinc-700 rounded-2xl p-4 transition-all duration-300 hover:border-indigo-500 hover:shadow-md">
                    <div class="absolute left-0 top-4 bottom-4 w-1 bg-indigo-500 rounded-r-full opacity-40 group-hover:opacity-100 transition-opacity"></div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-slate-50 dark:bg-zinc-800 text-slate-500 dark:text-slate-500 rounded-xl flex items-center justify-center group-hover:bg-indigo-50 dark:group-hover:bg-indigo-500/10 group-hover:text-indigo-600 transition-colors shrink-0">
                            <flux:icon.academic-cap class="size-5" />
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest leading-none">Institutions</p>
                            <p class="text-xl font-black text-slate-900 dark:text-white leading-tight mt-0.5">{{ number_format($institutionCount) }}</p>
                        </div>
                    </div>
                </div>

                {{-- Teachers --}}
                <div class="group relative bg-white dark:bg-zinc-900/50 border border-slate-300 dark:border-zinc-700 rounded-2xl p-4 transition-all duration-300 hover:border-emerald-500 hover:shadow-md">
                    <div class="absolute left-0 top-4 bottom-4 w-1 bg-emerald-500 rounded-r-full opacity-40 group-hover:opacity-100 transition-opacity"></div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-slate-50 dark:bg-zinc-800 text-slate-500 dark:text-slate-500 rounded-xl flex items-center justify-center group-hover:bg-emerald-50 dark:group-hover:bg-emerald-500/10 group-hover:text-emerald-600 transition-colors shrink-0">
                            <flux:icon.users class="size-5" />
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest leading-none">Teachers</p>
                            <p class="text-xl font-black text-slate-900 dark:text-white leading-tight mt-0.5">{{ number_format($teachersCount) }}</p>
                        </div>
                    </div>
                </div>

                {{-- Principals --}}
                <div class="group relative bg-white dark:bg-zinc-900/50 border border-slate-300 dark:border-zinc-700 rounded-2xl p-4 transition-all duration-300 hover:border-amber-500 hover:shadow-md">
                    <div class="absolute left-0 top-4 bottom-4 w-1 bg-amber-500 rounded-r-full opacity-40 group-hover:opacity-100 transition-opacity"></div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-slate-50 dark:bg-zinc-800 text-slate-500 dark:text-slate-500 rounded-xl flex items-center justify-center group-hover:bg-amber-50 dark:group-hover:bg-amber-500/10 group-hover:text-amber-600 transition-colors shrink-0">
                            <flux:icon.user-circle class="size-5" />
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest leading-none">Principals</p>
                            <p class="text-xl font-black text-slate-900 dark:text-white leading-tight mt-0.5">{{ number_format($principalsCount) }}</p>
                        </div>
                    </div>
                </div>

                {{-- Other Staff --}}
                <div class="group relative bg-white dark:bg-zinc-900/50 border border-slate-300 dark:border-zinc-700 rounded-2xl p-4 transition-all duration-300 hover:border-rose-500 hover:shadow-md">
                    <div class="absolute left-0 top-4 bottom-4 w-1 bg-rose-500 rounded-r-full opacity-40 group-hover:opacity-100 transition-opacity"></div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-slate-50 dark:bg-zinc-800 text-slate-500 dark:text-slate-500 rounded-xl flex items-center justify-center group-hover:bg-rose-50 dark:group-hover:bg-rose-500/10 group-hover:text-rose-600 transition-colors shrink-0">
                            <flux:icon.user-group class="size-5" />
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest leading-none">Other Staff</p>
                            <p class="text-xl font-black text-slate-900 dark:text-white leading-tight mt-0.5">{{ number_format($otherStaffCount) }}</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900/50 rounded-[2.5rem] shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-zinc-700 p-8 flex flex-col">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white">Weekly Schedule</h3>
                    <p class="text-sm text-slate-500 dark:text-zinc-400 font-medium">{{ $monthLabel }}</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('dashboard', ['week' => $prevWeek]) }}" class="p-2.5 hover:bg-slate-50 dark:hover:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-xl text-slate-500 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <a href="{{ route('dashboard', ['week' => $nextWeek]) }}" class="p-2.5 hover:bg-slate-50 dark:hover:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-xl text-slate-500 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <div class="flex justify-between items-center mb-10 bg-slate-50/50 dark:bg-zinc-800/50 p-2 rounded-2xl border border-slate-200 dark:border-zinc-700">
                @foreach($weekDays as $currentDay)
                <a href="{{ route('dashboard', [
                            'week' => $currentDay->toDateString(),
                            'date' => $currentDay->toDateString()
                        ]) }}"
                    class="flex flex-col items-center space-y-2">

                    <span class="text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-tighter">
                        {{ $currentDay->format('D') }}
                    </span>

                    <div class="
                            relative
                            w-10 h-10
                            flex items-center justify-center
                            rounded-xl
                            transition-all duration-300

                            {{-- Selected date (MAIN highlight) --}}
                            {{ $currentDay->isSameDay($selectedDate)
                                ? 'bg-indigo-600 text-white font-bold'
                                : 'text-slate-600 dark:text-slate-500 hover:bg-white dark:hover:bg-zinc-800 hover:shadow-md' }}
                        ">
                        {{ $currentDay->format('d') }}

                        {{-- Current date underline --}}
                        @if($currentDay->isToday())
                        <span class="
                                    absolute -bottom-1
                                    left-1/2 -translate-x-1/2
                                    w-6 h-0.5
                                    rounded-full
                                    bg-emerald-500
                                "></span>
                        @endif
                    </div>
                </a>
                @endforeach

            </div>

            <div class="space-y-4 flex-1">
                <p class="text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-[0.2em] mb-4">
                    {{ $selectedDate->isToday() ? "Today's Briefing" : $selectedDate->format('F d, Y') }}
                </p>

                <div class="space-y-4 max-h-72 overflow-y-auto pr-1">
                    @forelse($todayEvents as $event)
                    <div class="group cursor-pointer p-4 rounded-2xl bg-slate-50 dark:bg-zinc-800/50 hover:bg-indigo-600 transition-all duration-300">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[10px] font-black text-indigo-600 dark:text-indigo-400 group-hover:text-indigo-200 transition-colors uppercase">
                                {{ $event['time'] }}
                            </span>
                            <div class="h-1.5 w-1.5 rounded-full bg-indigo-600 group-hover:bg-white"></div>
                        </div>

                        <h4 class="text-sm font-bold text-slate-800 dark:text-white group-hover:text-white transition-colors">
                            {{ $event['title'] }}
                        </h4>

                        <p class="text-xs text-slate-500 dark:text-zinc-400 group-hover:text-indigo-100 transition-colors">
                            {{ $event['location'] }}
                        </p>
                    </div>
                    @empty
                    <div class="flex flex-col items-center justify-center py-12 px-4">
                        <h3 class="text-lg font-medium text-slate-600 dark:text-zinc-400">Quiet day ahead</h3>
                        <p class="text-sm text-slate-500 dark:text-zinc-400 text-center max-w-xs">
                            No scheduled events for this day. Check back later or create a new event.
                        </p>
                    </div>
                    @endforelse
                </div>


            </div>

            <button class="mt-8 w-full py-4 bg-indigo-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 dark:shadow-none">
                View Full Calendar
            </button>
        </div>
    </div>

    {{-- Analytics Dashboard Section --}}
    <div class="mt-6">
        <div class="flex items-center space-x-2 mb-8 px-2">
            <span class="px-2 py-0.5 rounded bg-indigo-100 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 text-[10px] font-black uppercase tracking-wider">Analytics</span>
            <h3 class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-400">System Distribution Overview</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Province by Teacher --}}
            <div class="bg-white dark:bg-zinc-900/50 border border-slate-200 dark:border-zinc-700 rounded-4xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-6 text-center">Teachers by Province</h4>
                <div id="chart-teachers" class="flex justify-center min-h-[250px]"></div>
            </div>

            {{-- Gender by Teacher --}}
            <div class="bg-white dark:bg-zinc-900/50 border border-slate-200 dark:border-zinc-700 rounded-4xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-6 text-center">Teachers by Gender</h4>
                <div id="chart-gender-teachers" class="flex justify-center min-h-[250px]"></div>
            </div>

            {{-- Province by Institution --}}
            <div class="bg-white dark:bg-zinc-900/50 border border-slate-200 dark:border-zinc-700 rounded-4xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-6 text-center">Institutions by Province</h4>
                <div id="chart-institutions" class="flex justify-center min-h-[250px]"></div>
            </div>

            {{-- Province by Student --}}
            <div class="bg-white dark:bg-zinc-900/50 border border-slate-200 dark:border-zinc-700 rounded-4xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-6 text-center">Students by Province</h4>
                <div id="chart-students" class="flex justify-center min-h-[250px]"></div>
            </div>
        </div>
    </div>

    @unless(auth()->user()->hasRole('teacher'))
    {{-- Geographic Distribution (with working search) --}}
    <div class="mt-16" x-data="{ search: '' }">

        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 px-2">
            <div>
                <div class="flex items-center space-x-2 mb-2">
                    <span class="px-2 py-0.5 rounded bg-indigo-100 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 text-[10px] font-black uppercase tracking-wider">Live Data</span>
                    <h3 class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-400">Office distribution analytics</h3>
                </div>
            </div>

            <div class="mt-6 md:mt-0 flex items-center gap-4">
                <div class="relative">
                    <input
                        type="text"
                        placeholder="Search ..."
                        x-model.debounce.300ms="search"
                        class="pl-12 pr-4 py-3 bg-white dark:bg-zinc-900 border-0 ring-1 ring-slate-200 dark:ring-zinc-800 rounded-2xl text-sm focus:ring-2 focus:ring-indigo-500 transition-all w-72 shadow-md dark:text-white">
                    <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                <button type="button" class="p-3 bg-white dark:bg-zinc-900 ring-1 ring-slate-200 dark:ring-zinc-800 rounded-2xl hover:bg-slate-50 dark:hover:bg-zinc-800 transition-all shadow-md">
                    <svg class="w-5 h-5 text-slate-600 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div
            x-show="search.trim().length > 0 && $el.querySelectorAll('[data-region]:not([style*=&quot;display: none&quot;])').length === 0"
            class="mb-6 px-2"
            style="display:none;">
            <div class="bg-slate-50 dark:bg-zinc-800/50 border border-slate-300 dark:border-zinc-700 rounded-2xl p-4 text-sm text-slate-600 dark:text-zinc-400">
                Search result found for: <span class="font-bold text-slate-900 dark:text-white" x-text="search"></span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($officeLists as $region)
            @php
            $regionName = strtolower($region->short_name ?? $region->name ?? '');
            @endphp

            <div
                data-region
                class="group bg-white dark:bg-zinc-900/50 border border-slate-300 dark:border-zinc-700 rounded-3xl transition-all duration-300 hover:shadow-xl hover:shadow-indigo-500/5 hover:border-indigo-300"
                x-show="('{{ $regionName }}').includes(search.toLowerCase().trim())"
                x-transition.opacity>
                <div class="p-6 border-b border-slate-200 dark:border-zinc-700">
                    <div class="flex items-center space-x-4">
                        <div class="shrink-0 w-12 h-12 bg-indigo-50 dark:bg-zinc-800 rounded-2xl flex items-center justify-center text-indigo-600 dark:text-indigo-400 group-hover:bg-indigo-600 dark:group-hover:bg-indigo-500 group-hover:text-white transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-slate-800 dark:text-white leading-tight">{{ $region->short_name ?? $region->name }}</h4>
                            <p class="text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">
                                Region ID #{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-slate-50/50 dark:bg-zinc-800/30">
                    @if(isset($region->total_zeo) || isset($region->total_deo))
                    <div class="grid grid-cols-{{ isset($region->total_zeo) && isset($region->total_deo) ? '2' : '1' }} gap-4">
                        @if(isset($region->total_zeo))
                        <div class="bg-white dark:bg-zinc-900/50 p-4 rounded-2xl border border-slate-200 dark:border-zinc-700 shadow-md group-hover:border-indigo-100 dark:group-hover:border-indigo-900 transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase">Zonal (ZEO)</span>
                                <div class="w-2 h-2 rounded-full bg-indigo-500"></div>
                            </div>
                            <div class="flex items-baseline space-x-1">
                                <span class="text-2xl font-black text-slate-800 dark:text-white">{{ $region->total_zeo }}</span>
                                <span class="text-[10px] font-bold text-slate-500 dark:text-zinc-400">Offices</span>
                            </div>
                        </div>
                        @endif

                        @if(isset($region->total_deo))
                        <div class="bg-white dark:bg-zinc-900/50 p-4 rounded-2xl border border-slate-200 dark:border-zinc-700 shadow-md group-hover:border-emerald-100 dark:group-hover:border-emerald-900 transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase">Divisional (DEO)</span>
                                <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                            </div>
                            <div class="flex items-baseline space-x-1">
                                <span class="text-2xl font-black text-slate-800 dark:text-white">{{ $region->total_deo }}</span>
                                <span class="text-[10px] font-bold text-slate-500 dark:text-zinc-400">Offices</span>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>

                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            @if(isset($region->total_institutions))
                            <p class="text-sm font-bold text-slate-700 dark:text-zinc-300">Total Institutions</p>
                            @else
                            <p class="text-sm font-bold text-slate-700 dark:text-zinc-300">Total Staff</p>
                            @endif
                            <p class="text-xs text-slate-500 dark:text-zinc-400">Registered across the region</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xl font-black text-indigo-600 dark:text-indigo-400">{{ number_format($region->total_institutions ?? $region->total_staff) }}</span>
                        </div>
                    </div>

                    <a @if($workplace_level=='OLID001' ) href="{{ route('offices.pmoe.profile.overview', $region->id) }}" @elseif($workplace_level=='OLID002' ) href="{{ route('offices.peo.profile.overview', $region->id) }}" @elseif($workplace_level=='OLID003' ) href="{{ route('offices.zeo.profile.overview', $region->id) }}" @elseif($workplace_level=='OLID004' ) href="{{ route('offices.deo.profile.overview', $region->id) }}" @elseif($workplace_level=='OLID005' || $workplace_level=='OLID006' ) href="{{ route('offices.institutions.profile.overview', $region->id) }}" @else href="#" @endif class="flex items-center justify-center w-full py-3 rounded-xl bg-indigo-600 text-white text-xs font-bold hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-100 dark:shadow-none hover:shadow-indigo-200">
                        View More Details
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>
            </div>
            @endforeach
        </div>

    </div>
    @endunless

    {{-- CRITICAL: DO NOT add line breaks inside {!! !!} tags - Blade parser will break --}}
    <script type="application/json" id="chart-data-teachers">{!! json_encode($teacherByProv ?? []) !!}</script>
    <script type="application/json" id="chart-data-institutions">{!! json_encode($instByProv ?? []) !!}</script>
    <script type="application/json" id="chart-data-students">{!! json_encode($studentByProv ?? []) !!}</script>
    <script type="application/json" id="chart-data-gender-teachers">{!! json_encode($teacherByGender ?? []) !!}</script>

    <script>
        document.addEventListener('livewire:navigated', function() {
            if (!document.querySelector("#chart-teachers")) return;

            // Clear previous instances in case of Livewire DOM persistence
            document.querySelector("#chart-teachers").innerHTML = '';
            document.querySelector("#chart-institutions").innerHTML = '';
            document.querySelector("#chart-students").innerHTML = '';
            if (document.querySelector("#chart-gender-teachers")) document.querySelector("#chart-gender-teachers").innerHTML = '';

            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#94a3b8' : '#64748b';

            const commonOptions = {
                chart: {
                    type: 'donut',
                    height: 280,
                    fontFamily: 'inherit',
                    background: 'transparent',
                },
                noData: {
                    text: 'Not enough data available',
                    align: 'center',
                    verticalAlign: 'middle',
                    style: {
                        color: textColor,
                        fontSize: '14px',
                        fontFamily: 'inherit'
                    }
                },
                stroke: {
                    show: true,
                    colors: isDark ? ['#18181b'] : ['#ffffff'],
                    width: 2
                },
                dataLabels: {
                    enabled: false
                },
                legend: {
                    position: 'bottom',
                    labels: {
                        colors: textColor
                    },
                    markers: {
                        width: 8,
                        height: 8,
                        radius: 12
                    }
                },
                tooltip: {
                    theme: isDark ? 'dark' : 'light'
                }
            };

            // Dynamic Data from Backend via JSON Script Tags
            const teacherData = JSON.parse(document.getElementById('chart-data-teachers').textContent || '[]');
            const instData = JSON.parse(document.getElementById('chart-data-institutions').textContent || '[]');
            const studentData = JSON.parse(document.getElementById('chart-data-students').textContent || '[]');

            const getLabels = (data) => data.map(item => item.name);
            const getSeries = (data) => data.map(item => Number(item.total));

            // Teachers Chart Data
            new ApexCharts(document.querySelector("#chart-teachers"), {
                ...commonOptions,
                series: getSeries(teacherData),
                labels: getLabels(teacherData),
                colors: ['#4f46e5', '#3b82f6', '#0ea5e9', '#06b6d4', '#14b8a6', '#10b981', '#84cc16', '#eab308', '#f59e0b']
            }).render();

            // Institutions Chart Data
            new ApexCharts(document.querySelector("#chart-institutions"), {
                ...commonOptions,
                series: getSeries(instData),
                labels: getLabels(instData),
                colors: ['#ec4899', '#f43f5e', '#ef4444', '#f97316', '#f59e0b', '#eab308', '#84cc16', '#22c55e', '#14b8a6']
            }).render();

            // Students Chart Data
            new ApexCharts(document.querySelector("#chart-students"), {
                ...commonOptions,
                series: getSeries(studentData),
                labels: getLabels(studentData),
                colors: ['#8b5cf6', '#a855f7', '#d946ef', '#ec4899', '#f43f5e', '#ef4444', '#f97316', '#f59e0b', '#eab308']
            }).render();

            // Gender Teachers Chart Data
            const genderTeacherData = JSON.parse(document.getElementById('chart-data-gender-teachers').textContent || '[]');
            if (document.querySelector("#chart-gender-teachers")) {
                new ApexCharts(document.querySelector("#chart-gender-teachers"), {
                    ...commonOptions,
                    series: getSeries(genderTeacherData),
                    labels: getLabels(genderTeacherData),
                    colors: ['#3b82f6', '#ec4899', '#64748b'] // Male: Blue, Female: Pink, Other: Grey
                }).render();
            }
        });
    </script>
</x-layouts.app>