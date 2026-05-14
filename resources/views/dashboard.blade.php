<x-layouts.app :title="__('Dashboard')">
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-10">

        <div class="xl:col-span-2 space-y-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">National Education Management System</h1>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="flex h-2 w-2 rounded-full bg-indigo-500"></span>
                        <p class="text-slate-500 dark:text-zinc-400 font-medium text-sm capitalize">{{ $user_roles->pluck('name')->implode(', ') }} • {{ now()->format('l, F j, Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-[3rem] bg-linear-to-br from-[#635BFF] via-[#564df0] to-[#4338ca] p-8 md:p-12 shadow-2xl shadow-indigo-200/50 dark:shadow-none text-white border border-white/10">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-8">
                    <div class="w-full md:w-3/5 space-y-8">
                        <div class="inline-flex items-center px-4 py-1.5 rounded-full bg-white/15 backdrop-blur-xl border border-white/10 text-[10px] font-black tracking-[0.2em] uppercase text-indigo-50">
                            <span class="mr-2 flex h-1.5 w-1.5 rounded-full bg-teal-400 animate-pulse"></span>
                            {{ now()->format('F j, Y') }}
                        </div>

                        <div class="space-y-4">
                            <h3 class="text-2xl md:text-4xl font-black tracking-tight leading-[1.05]">
                                Welcome Back,<br />
                                <span class="text-transparent bg-clip-text bg-linear-to-r from-white to-indigo-100 italic">{{ explode(' ', $user->name)[0] }}</span>!
                            </h3>
                            <p class="text-indigo-50/80 text-lg font-medium max-w-md leading-relaxed">
                                Your profile is <span class="text-white font-bold px-2 py-0.5 rounded-lg bg-white/10 border border-white/20 decoration-teal-400 underline-offset-4">{{ ($people?->appointment?->is_verified == 1) ? 'Verified' : 'Not Verified' }}</span>.
                                <span class="block mt-2 text-sm opacity-75 italic text-indigo-200">Keep your information up to date for official records.</span>
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-4 pt-2">
                            <a href="{{ route('my-profile.index') }}"
                                class="inline-flex items-center gap-2 bg-white text-indigo-600 px-8 py-4 rounded-2xl font-bold text-sm hover:scale-105 hover:bg-indigo-50 transition-all duration-300 shadow-[0_20px_40px_-15px_rgba(255,255,255,0.3)]">
                                <span>👤</span>
                                My Profile
                            </a>
                        </div>
                    </div>

                    <div class="relative w-full md:w-2/5 flex justify-center items-end self-stretch">
                        <div class="absolute inset-0 bg-linear-to-t from-indigo-900/20 to-transparent blur-3xl rounded-full"></div>
                        <img src="{{ asset('images/welcome.png') }}" alt="Dashboard Illustration"
                            class="relative z-10 w-4/5 md:w-full max-h-72 object-contain drop-shadow-[0_20px_50px_rgba(0,0,0,0.3)] transition-all duration-700 hover:-translate-y-2">
                    </div>
                </div>

                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-white/10 rounded-full blur-[80px] pointer-events-none"></div>
                <div class="absolute left-1/4 top-0 w-40 h-40 bg-indigo-300/20 rounded-full blur-[60px] pointer-events-none"></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="group relative bg-white dark:bg-zinc-900/50 border border-slate-200 dark:border-zinc-800 rounded-2xl p-4 transition-all duration-300 hover:border-indigo-500 hover:shadow-md">
                    <div class="absolute left-0 top-4 bottom-4 w-1 bg-indigo-500 rounded-r-full opacity-40 group-hover:opacity-100 transition-opacity"></div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-slate-50 dark:bg-zinc-800 text-slate-600 dark:text-slate-300 rounded-xl flex items-center justify-center group-hover:bg-indigo-50 dark:group-hover:bg-indigo-500/10 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-widest">Total Institutions</p>
                                <p class="text-2xl font-black text-slate-900 dark:text-white leading-none mt-1">
                                    {{ number_format($institutionCount) }}
                                </p>
                            </div>
                        </div>

                        <div class="text-right">
                            <span class="inline-block px-2 py-0.5 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[9px] font-bold rounded-md uppercase">Active</span>
                        </div>
                    </div>
                </div>

                <div class="group relative bg-white dark:bg-zinc-900/50 border border-slate-200 dark:border-zinc-800 rounded-2xl p-4 transition-all duration-300 hover:border-emerald-500 hover:shadow-md">
                    <div class="absolute left-0 top-4 bottom-4 w-1 bg-emerald-500 rounded-r-full opacity-40 group-hover:opacity-100 transition-opacity"></div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-slate-50 dark:bg-zinc-800 text-slate-600 dark:text-slate-300 rounded-xl flex items-center justify-center group-hover:bg-emerald-50 dark:group-hover:bg-emerald-500/10 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-widest">Total Teachers</p>
                                <p class="text-2xl font-black text-slate-900 dark:text-white leading-none mt-1">
                                    {{ number_format($teachersCount) }}
                                </p>
                            </div>
                        </div>

                        <div class="text-right">
                            <span class="inline-block px-2 py-0.5 bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-slate-400 text-[9px] font-bold rounded-md uppercase">Staff</span>
                        </div>
                    </div>
                </div>

                <div class="group relative bg-white dark:bg-zinc-900/50 border border-slate-200 dark:border-zinc-800 rounded-2xl p-4 transition-all duration-300 hover:border-blue-500 hover:shadow-md">
                    <div class="absolute left-0 top-4 bottom-4 w-1 bg-blue-500 rounded-r-full opacity-40 group-hover:opacity-100 transition-opacity"></div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-slate-50 dark:bg-zinc-800 text-slate-600 dark:text-slate-300 rounded-xl flex items-center justify-center group-hover:bg-blue-50 dark:group-hover:bg-blue-500/10 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21v-1a4 4 0 00-4-4H6a4 4 0 00-4 4v1" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12a4 4 0 100-8 4 4 0 000 8z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M22 21v-1a4 4 0 00-3-3.87" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 3.13a4 4 0 010 7.75" />
                                </svg>

                            </div>
                            <div class="ml-4">
                                <p class="text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-widest">Total Students</p>
                                <p class="text-2xl font-black text-slate-900 dark:text-white leading-none mt-1">
                                    {{ __('00') }}
                                </p>
                            </div>
                        </div>

                        <div class="text-right">
                            <span class="inline-block px-2 py-0.5 bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-slate-400 text-[9px] font-bold rounded-md uppercase">Students</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900/50 rounded-[2.5rem] shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-50 dark:border-zinc-800 p-8 flex flex-col">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white">Weekly Schedule</h3>
                    <p class="text-sm text-slate-400 dark:text-zinc-500 font-medium">{{ $monthLabel }}</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('dashboard', ['week' => $prevWeek]) }}" class="p-2.5 hover:bg-slate-50 dark:hover:bg-zinc-800 border border-slate-100 dark:border-zinc-800 rounded-xl text-slate-400 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <a href="{{ route('dashboard', ['week' => $nextWeek]) }}" class="p-2.5 hover:bg-slate-50 dark:hover:bg-zinc-800 border border-slate-100 dark:border-zinc-800 rounded-xl text-slate-400 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <div class="flex justify-between items-center mb-10 bg-slate-50/50 dark:bg-zinc-800/50 p-2 rounded-2xl border border-slate-50 dark:border-zinc-800">
                @foreach($weekDays as $currentDay)
                <a href="{{ route('dashboard', [
                            'week' => $currentDay->toDateString(),
                            'date' => $currentDay->toDateString()
                        ]) }}"
                    class="flex flex-col items-center space-y-2">

                    <span class="text-[10px] font-black text-slate-400 dark:text-zinc-500 uppercase tracking-tighter">
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
                                : 'text-slate-600 dark:text-slate-400 hover:bg-white dark:hover:bg-zinc-800 hover:shadow-sm' }}
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
                <p class="text-[10px] font-black text-slate-400 dark:text-zinc-500 uppercase tracking-[0.2em] mb-4">
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
                        <p class="text-sm text-slate-400 dark:text-zinc-500 text-center max-w-xs">
                            No scheduled events for this day. Check back later or create a new event.
                        </p>
                    </div>
                    @endforelse
                </div>


            </div>

            <button class="mt-8 w-full py-4 bg-slate-900 dark:bg-zinc-800 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-indigo-600 transition-all">
                View Full Calendar
            </button>
        </div>
    </div>

    {{-- Geographic Distribution (with working search) --}}
    <div class="mt-16" x-data="{ search: '' }">

        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 px-2">
            <div>
                <div class="flex items-center space-x-2 mb-2">
                    <span class="px-2 py-0.5 rounded bg-indigo-100 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 text-[10px] font-black uppercase">Live Data</span>
                    <h3 class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400 dark:text-zinc-500">Office distribution analytics</h3>
                </div>
                <h2 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight">Geographic Distribution</h2>
            </div>

            <div class="mt-6 md:mt-0 flex items-center gap-4">
                <div class="relative">
                    <input
                        type="text"
                        placeholder="Search ..."
                        x-model.debounce.300ms="search"
                        class="pl-12 pr-4 py-3 bg-white dark:bg-zinc-900 border-0 ring-1 ring-slate-200 dark:ring-zinc-800 rounded-2xl text-sm focus:ring-2 focus:ring-indigo-500 transition-all w-72 shadow-sm dark:text-white">
                    <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                <button type="button" class="p-3 bg-white dark:bg-zinc-900 ring-1 ring-slate-200 dark:ring-zinc-800 rounded-2xl hover:bg-slate-50 dark:hover:bg-zinc-800 transition-all shadow-sm">
                    <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div
            x-show="search.trim().length > 0 && $el.querySelectorAll('[data-region]:not([style*=&quot;display: none&quot;])').length === 0"
            class="mb-6 px-2"
            style="display:none;">
            <div class="bg-slate-50 dark:bg-zinc-800/50 border border-slate-200 dark:border-zinc-700 rounded-2xl p-4 text-sm text-slate-600 dark:text-zinc-400">
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
                class="group bg-white dark:bg-zinc-900/50 border border-slate-200 dark:border-zinc-800 rounded-3xl transition-all duration-300 hover:shadow-xl hover:shadow-indigo-500/5 hover:border-indigo-300"
                x-show="('{{ $regionName }}').includes(search.toLowerCase().trim())"
                x-transition.opacity>
                <div class="p-6 border-b border-slate-50 dark:border-zinc-800">
                    <div class="flex items-center space-x-4">
                        <div class="shrink-0 w-12 h-12 bg-indigo-50 dark:bg-zinc-800 rounded-2xl flex items-center justify-center text-indigo-600 dark:text-indigo-400 group-hover:bg-indigo-600 dark:group-hover:bg-indigo-500 group-hover:text-white transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-slate-800 dark:text-white leading-tight">{{ $region->short_name ?? $region->name }}</h4>
                            <p class="text-xs font-semibold text-slate-400 dark:text-zinc-500 uppercase tracking-wider">
                                Region ID #{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-slate-50/50 dark:bg-zinc-800/30">
                    @if(isset($region->total_zeo) || isset($region->total_deo))
                    <div class="grid grid-cols-{{ isset($region->total_zeo) && isset($region->total_deo) ? '2' : '1' }} gap-4">
                        @if(isset($region->total_zeo))
                        <div class="bg-white dark:bg-zinc-900/50 p-4 rounded-2xl border border-slate-100 dark:border-zinc-800 shadow-sm group-hover:border-indigo-100 dark:group-hover:border-indigo-900 transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-black text-slate-400 dark:text-zinc-500 uppercase">Zonal (ZEO)</span>
                                <div class="w-2 h-2 rounded-full bg-indigo-500"></div>
                            </div>
                            <div class="flex items-baseline space-x-1">
                                <span class="text-2xl font-black text-slate-800 dark:text-white">{{ $region->total_zeo }}</span>
                                <span class="text-[10px] font-bold text-slate-400 dark:text-zinc-500">Offices</span>
                            </div>
                        </div>
                        @endif

                        @if(isset($region->total_deo))
                        <div class="bg-white dark:bg-zinc-900/50 p-4 rounded-2xl border border-slate-100 dark:border-zinc-800 shadow-sm group-hover:border-emerald-100 dark:group-hover:border-emerald-900 transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-black text-slate-400 dark:text-zinc-500 uppercase">Divisional (DEO)</span>
                                <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                            </div>
                            <div class="flex items-baseline space-x-1">
                                <span class="text-2xl font-black text-slate-800 dark:text-white">{{ $region->total_deo }}</span>
                                <span class="text-[10px] font-bold text-slate-400 dark:text-zinc-500">Offices</span>
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
                            <p class="text-xs text-slate-500 dark:text-zinc-500">Registered across the region</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xl font-black text-indigo-600 dark:text-indigo-400">{{ number_format($region->total_institutions ?? $region->total_staff) }}</span>
                        </div>
                    </div>

                    <a @if($workplace_level=='OLID001' ) href="{{ route('offices.pmoe.profile.overview', $region->id) }}" @elseif($workplace_level=='OLID002' ) href="{{ route('offices.peo.profile.overview', $region->id) }}" @elseif($workplace_level=='OLID003' ) href="{{ route('offices.zeo.profile.overview', $region->id) }}" @elseif($workplace_level=='OLID004' ) href="{{ route('offices.deo.profile.overview', $region->id) }}" @elseif($workplace_level=='OLID005' || $workplace_level=='OLID006' ) href="{{ route('institutions.profile.overview', $region->id) }}" @else href="#" @endif class="flex items-center justify-center w-full py-3 rounded-xl bg-slate-900 dark:bg-zinc-800 text-white text-xs font-bold hover:bg-indigo-600 dark:hover:bg-indigo-500 transition-colors shadow-lg shadow-slate-200 dark:shadow-none hover:shadow-indigo-200">
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

</x-layouts.app>