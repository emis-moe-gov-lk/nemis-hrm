<section class="w-full">
    {{-- Header Section --}}
    <div class="relative mb-8 w-full">
        <flux:heading size="xl" level="1" class="mb-1">{{ __('Ministry of Education Overview') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">
            {{ __('Statistics about education offices and student totals.') }}
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <x-offices.moe.layout :officeId="$officeId">
        {{-- Ministry Profile Summary --}}
        @if ($EducationMinistry)
        <div class="flex flex-col sm:flex-row items-center gap-6 mb-8 p-6 bg-white dark:bg-[#151518] border border-slate-200 dark:border-slate-800/60 rounded-[1.25rem] shadow-sm">
            <div class="relative w-24 h-24 rounded-2xl overflow-hidden border-2 border-slate-100 dark:border-slate-800 shadow-inner bg-slate-50 dark:bg-slate-900 shrink-0">
                @if ($EducationMinistry->logo)
                <img src="{{ asset('storage/' . $EducationMinistry->logo) }}"
                    alt="{{ $EducationMinistry->name }}"
                    class="w-full h-full object-cover {{ $EducationMinistry->active_status ? '' : 'grayscale opacity-60' }}">
                @else
                <div class="w-full h-full flex items-center justify-center text-slate-300 dark:text-slate-700">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                @endif

                @unless ($EducationMinistry->active_status)
                <div class="absolute inset-0 flex items-center justify-center bg-red-500/20 backdrop-blur-[1px]">
                    <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-red-600 text-white shadow-sm">Inactive</span>
                </div>
                @endunless
            </div>

            <div class="flex-1 text-center sm:text-left">
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mb-1">
                    {{ $EducationMinistry->name }}
                </h1>
                @if ($EducationMinistry->short_name)
                <div class="inline-flex px-2.5 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">
                    {{ $EducationMinistry->short_name }}
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- GRAND SUMMARY --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
            {{-- Students Card --}}
            <div class="relative overflow-hidden bg-white dark:bg-[#151518] border border-slate-200 dark:border-slate-800/60 rounded-[1.25rem] p-6 shadow-sm group hover:border-blue-500/30 transition-all">
                <div class="absolute -right-4 -top-6 opacity-10 dark:opacity-5 text-blue-600 dark:text-blue-400 pointer-events-none transform rotate-12 group-hover:scale-110 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-32 h-32">
                        <path d="M11.7 2.805a.75.75 0 01.6 0A60.65 60.65 0 0122.83 8.72a.75.75 0 01-.231 1.337 49.942 49.942 0 00-9.9 2.856.75.75 0 01-.598 0 49.944 49.944 0 00-9.9-2.856.75.75 0 01-.231-1.337A60.626 60.626 0 0111.7 2.805z" />
                        <path d="M13.06 15.473a48.45 48.45 0 017.666-2.475c.15-.035.302.054.385.202a.75.75 0 01-.051.75c-.644.906-1.23 1.844-1.751 2.812a46.751 46.751 0 01-7.04 1.341.75.75 0 01-.597 0 46.716 46.716 0 01-7.042-1.341 48.71 48.71 0 01-1.752-2.812.75.75 0 01-.05-.75c.082-.148.235-.237.384-.202a48.458 48.458 0 017.668 2.475l.138.046c.198.066.413.066.611 0l.138-.046z" />
                    </svg>
                </div>
                <div class="relative z-10 flex flex-col justify-center h-full">
                    <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">{{ __('Total Students') }}</div>
                    <div class="text-4xl font-black text-slate-900 dark:text-white tracking-tighter">{{ number_format($studentCount) }}</div>
                    <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-tight mt-1">{{ __('Total Students Nationwide') }}</div>
                </div>
            </div>

            {{-- Ministries Card --}}
            <div class="relative overflow-hidden bg-white dark:bg-[#151518] border border-slate-200 dark:border-slate-800/60 rounded-[1.25rem] p-6 shadow-sm group hover:border-purple-500/30 transition-all">
                <div class="absolute -right-4 -top-6 opacity-10 dark:opacity-5 text-purple-600 dark:text-purple-400 pointer-events-none transform rotate-12 group-hover:scale-110 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-32 h-32">
                        <path d="M11.58 2.387a.75.75 0 0 1 .84 0l8.25 5.25a.75.75 0 0 1 0 1.253l-8.25 5.25a.75.75 0 0 1-.84 0l-8.25-5.25a.75.75 0 0 1 0-1.253l8.25-5.25Z" />
                        <path d="m3.33 11.234 7.57 4.817a1.5 1.5 0 0 0 1.6 0l7.57-4.817A.75.75 0 0 1 21 11.871v4.571a.75.75 0 0 1-.368.648l-8.25 5.25a.75.75 0 0 1-.764 0l-8.25-5.25A.75.75 0 0 1 3 16.442v-4.571a.75.75 0 0 1 .33-.637Z" />
                    </svg>
                </div>
                <div class="relative z-10 flex flex-col justify-center h-full">
                    <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">{{ __('Provincial Ministries') }}</div>
                    <div class="text-4xl font-black text-slate-900 dark:text-white tracking-tighter">{{ $provincialMinistryCount }}</div>
                    <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-tight mt-1">{{ __('Ministry Offices Count') }}</div>
                </div>
            </div>

            {{-- Departments Card --}}
            <div class="relative overflow-hidden bg-white dark:bg-[#151518] border border-slate-200 dark:border-slate-800/60 rounded-[1.25rem] p-6 shadow-sm group hover:border-emerald-500/30 transition-all">
                <div class="absolute -right-4 -top-6 opacity-10 dark:opacity-5 text-emerald-600 dark:text-emerald-400 pointer-events-none transform rotate-12 group-hover:scale-110 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-32 h-32">
                        <path fill-rule="evenodd" d="M3 2.25a.75.75 0 0 0 0 1.5v16.5h-.75a.75.75 0 0 0 0 1.5h15a.75.75 0 0 0 0-1.5h-.75V3.75a.75.75 0 0 0 0-1.5H3ZM9 6a.75.75 0 0 0 0 1.5h1.5a.75.75 0 0 0 0-1.5H9ZM9 9a.75.75 0 0 0 0 1.5h1.5a.75.75 0 0 0 0-1.5H9ZM9 12a.75.75 0 0 0 0 1.5h1.5a.75.75 0 0 0 0-1.5H9ZM9 15a.75.75 0 0 0 0 1.5h1.5a.75.75 0 0 0 0-1.5H9ZM5.25 6a.75.75 0 0 0 0 1.5H6.75A.75.75 0 0 0 6.75 6H5.25ZM5.25 9a.75.75 0 0 0 0 1.5H6.75a.75.75 0 0 0 0-1.5H5.25ZM5.25 12a.75.75 0 0 0 0 1.5H6.75a.75.75 0 0 0 0-1.5H5.25ZM5.25 15a.75.75 0 0 0 0 1.5H6.75a.75.75 0 0 0 0-1.5H5.25ZM12.75 6a.75.75 0 0 0 0 1.5h1.5a.75.75 0 0 0 0-1.5h-1.5ZM12.75 9a.75.75 0 0 0 0 1.5h1.5a.75.75 0 0 0 0-1.5h-1.5ZM12.75 12a.75.75 0 0 0 0 1.5h1.5a.75.75 0 0 0 0-1.5h-1.5ZM12.75 15a.75.75 0 0 0 0 1.5h1.5a.75.75 0 0 0 0-1.5h-1.5Z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="relative z-10 flex flex-col justify-center h-full">
                    <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">{{ __('Provincial Departments') }}</div>
                    <div class="text-4xl font-black text-slate-900 dark:text-white tracking-tighter">{{ $provincialDeptCount }}</div>
                    <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-tight mt-1">{{ __('Education Departments') }}</div>
                </div>
            </div>

            {{-- Zonal Cards --}}
            <div class="relative overflow-hidden bg-white dark:bg-[#151518] border border-slate-200 dark:border-slate-800/60 rounded-[1.25rem] p-6 shadow-sm group hover:border-amber-500/30 transition-all">
                <div class="absolute -right-4 -top-6 opacity-10 dark:opacity-5 text-amber-600 dark:text-amber-400 pointer-events-none transform rotate-12 group-hover:scale-110 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-32 h-32">
                        <path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.154-.742 15.363 15.363 0 003.06-2.637c1.19-1.33 2.129-2.697 2.734-4.06.633-1.425.962-2.822.962-4.183 0-3.103-2.488-5.776-5.638-5.904a11.03 11.03 0 00-4.942 0C6.126 1.01 3.638 3.682 3.638 6.785c0 1.361.33 2.758.962 4.183.605 1.363 1.544 2.73 2.734 4.06a15.36 15.36 0 003.06 2.637c.467.366.849.614 1.154.742zM12 9a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="relative z-10 flex flex-col justify-center h-full">
                    <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">{{ __('Zonal Offices') }}</div>
                    <div class="text-4xl font-black text-slate-900 dark:text-white tracking-tighter">{{ $zonalOfficeCount }}</div>
                    <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-tight mt-1">{{ __('Zonal Education Offices') }}</div>
                </div>
            </div>

            {{-- Divisions Card --}}
            <div class="relative overflow-hidden bg-white dark:bg-[#151518] border border-slate-200 dark:border-slate-800/60 rounded-[1.25rem] p-6 shadow-sm group hover:border-rose-500/30 transition-all">
                <div class="absolute -right-4 -top-6 opacity-10 dark:opacity-5 text-rose-600 dark:text-rose-400 pointer-events-none transform rotate-12 group-hover:scale-110 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-32 h-32">
                        <path fill-rule="evenodd" d="M8.25 6.75a3.75 3.75 0 117.5 0 3.75 3.75 0 01-7.5 0zM15.75 9.75a3 3 0 116 0 3 3 0 01-6 0zM2.25 9.75a3 3 0 116 0 3 3 0 01-6 0zM6.31 15.117A6.745 6.745 0 0112 12a6.745 6.745 0 016.709 7.498.75.75 0 01-.372.568A12.696 12.696 0 0112 21.75c-2.305 0-4.47-.612-6.337-1.684a.75.75 0 01-.372-.568 6.787 6.787 0 011.019-4.38z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="relative z-10 flex flex-col justify-center h-full">
                    <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">{{ __('Divisional Offices') }}</div>
                    <div class="text-4xl font-black text-slate-900 dark:text-white tracking-tighter">{{ $divisionCount }}</div>
                    <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-tight mt-1">{{ __('Divisions under Zones') }}</div>
                </div>
            </div>
        </div>

        {{-- Staff by Service Section --}}
        <div class="mt-12 mb-6">
            <div class="flex items-center gap-3 mb-8">
                <div class="h-6 w-1 bg-blue-600 rounded-full"></div>
                <h2 class="text-lg font-black text-slate-900 dark:text-white tracking-tight uppercase">{{ __('Staff by Service') }}</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($serviceCounts as $service)
                <div class="relative group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm hover:shadow-md transition-all">
                    <div class="flex items-start justify-between mb-4">
                        <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800 group-hover:bg-blue-50 dark:group-hover:bg-blue-500/10 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-slate-400 dark:text-slate-500 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                        </div>
                        <div class="text-[10px] font-black px-2 py-0.5 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20 uppercase tracking-wider">
                            {{ __('Active') }}
                        </div>
                    </div>

                    <div>
                        <div class="text-4xl font-black text-slate-900 dark:text-white tracking-tighter mb-1">{{ number_format($service->staff_count) }}</div>
                        <div class="text-sm font-bold text-slate-700 dark:text-slate-300 leading-tight mb-2">{{ $service->service_name }}</div>
                        <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">{{ __('Total Staff in this Service') }}</div>
                    </div>

                    {{-- Subtle background decoration --}}
                    <div class="absolute right-4 bottom-4 opacity-10 pointer-events-none">
                        <span class="text-xs font-black text-slate-100 dark:text-slate-800 uppercase tracking-tighter">SVC-{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </x-offices.moe.layout>
</section>