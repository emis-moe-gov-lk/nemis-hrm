<div>
    <div class="bg-white dark:bg-slate-800 rounded-xl max-w-7xl m-auto shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
        <!-- Header with School Info -->
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
            <div class="flex flex-col md:flex-row md:items-start gap-6">
                <!-- School Logo and Basic Info -->
                <div class="shrink-0">
                    <div class="flex flex-col items-center md:items-start gap-4">
                        <!-- Logo -->
                        <div class="relative">
                            <div class="h-24 w-24 rounded-full border-4 border-white dark:border-slate-700 shadow-lg overflow-hidden bg-linear-to-br from-blue-50 to-indigo-50 dark:from-slate-900 dark:to-slate-800">
                                <img
                                    src="{{ asset('storage/images/institution/'. $institution->logo) }}"
                                    alt="{{ $institution->name }}"
                                    class="w-full h-full object-cover" />
                            </div>
                            <!-- Status Badge -->
                            <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full shadow-md
                                {{ $institution->active_status 
                                    ? 'bg-green-500 text-white border-2 border-white dark:border-slate-800' 
                                    : 'bg-red-500 text-white border-2 border-white dark:border-slate-800' }}">
                                    {{ $institution->active_status ? 'ACTIVE' : 'INACTIVE' }}
                                </span>
                            </div>
                        </div>

                        <!-- Established Date -->
                        <div class="w-full text-center mt-1">
                            <span class="inline-block px-3 py-1 bg-slate-100 dark:bg-slate-700/50 text-slate-600 dark:text-slate-400 text-xs font-bold rounded-md shadow-sm border border-slate-200 dark:border-slate-600">
                                EST. 1995
                            </span>
                        </div>
                    </div>
                </div>

                <!-- School Details -->
                <div class="flex-1">
                    <!-- School Name and Census -->
                    <div class="mb-4">
                        <div class="flex items-center gap-3 mb-2">
                            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                                {{ $institution->name }}
                            </h1>
                            <span class="px-4 py-1.5 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-base font-bold font-mono rounded-lg shadow-sm">
                                #{{ str_pad($institution->census_no, 5, '0', STR_PAD_LEFT) }}
                            </span>
                        </div>

                        <!-- School Type and Grades -->
                        <div class="flex flex-wrap gap-2 mb-3">
                            @if($institution->institutionType)
                            <span class="px-3 py-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-sm font-medium rounded-full">
                                {{ $institution->institutionType->institution_types_name }}
                            </span>
                            @endif

                            @if($institution->gradeSpan)
                            <span class="px-3 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 text-sm font-medium rounded-full">
                                {{ $institution->gradeSpan->grade_span_name }}
                            </span>
                            @endif

                            @if($institution->typeByGender)
                            <span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 text-sm font-medium rounded-full">
                                {{ $institution->typeByGender->name }}
                            </span>
                            @endif

                            @if($institution->authority)
                            <span class="px-3 py-1 bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300 text-sm font-medium rounded-full flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                {{ $institution->authority->authority_name }}
                            </span>
                            @endif

                            @if($institution->institutionLanguages)
                            <span class="px-3 py-1 bg-cyan-100 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-300 text-sm font-medium rounded-full flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                                </svg>
                                {{ $institution->institutionLanguages->name }}
                            </span>
                            @endif

                            @if($institution->facilities)
                            <span class="px-3 py-1 bg-fuchsia-100 dark:bg-fuchsia-900/30 text-fuchsia-700 dark:text-fuchsia-300 text-sm font-medium rounded-full flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                {{ $institution->facilities->name }}
                            </span>
                            @endif

                            @if($institution->ethnicity)
                            <span class="px-3 py-1 bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 text-sm font-medium rounded-full flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                {{ $institution->ethnicity->ethnicity_name }}
                            </span>
                            @endif

                            @if($institution->sport_s==0 || $institution->sport_s==1)
                            <span class="px-3 py-1 bg-lime-100 dark:bg-lime-900/30 text-lime-700 dark:text-lime-300 text-sm font-medium rounded-full flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                @if($institution->sport_s)
                                <span>Sport School</span>
                                @else
                                <span>Not a Sport School</span>
                                @endif
                            </span>
                            @endif
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-slate-100 dark:bg-slate-700 rounded-lg">
                                <svg class="w-5 h-5 text-slate-600 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm text-slate-500 dark:text-slate-400">Phone</div>
                                <div class="font-medium text-slate-900 dark:text-white">{{ $institution->phone ?? 'Not available' }}</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-slate-100 dark:bg-slate-700 rounded-lg">
                                <svg class="w-5 h-5 text-slate-600 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89-3.46a2 2 0 012.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm text-slate-500 dark:text-slate-400">Email</div>
                                <div class="font-medium text-slate-900 dark:text-white">{{ $institution->email ?? 'Not available' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Education Office Hierarchy -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <!-- Provincial Office -->
                        <div class="bg-linear-to-r from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm text-emerald-600 dark:text-emerald-400 font-medium mb-1">Provincial Education Office</div>
                                    <div class="text-lg font-bold text-emerald-800 dark:text-emerald-300 leading-tight">
                                        {{ $institution->zonalEducationOffice?->provincialEducationOffice?->short_name ?? 'Not Assigned' }}
                                    </div>
                                </div>
                                <div class="text-emerald-500 dark:text-emerald-400 shrink-0 ml-2">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Zonal Office -->
                        <div class="bg-linear-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm text-blue-600 dark:text-blue-400 font-medium mb-1">Zonal Education Office</div>
                                    <div class="text-lg font-bold text-blue-800 dark:text-blue-300 leading-tight">
                                        {{ $institution->zonalEducationOffice?->short_name ?? 'Not Assigned' }}
                                    </div>
                                </div>
                                <div class="text-blue-500 dark:text-blue-400 shrink-0 ml-2">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Divisional Office -->
                        <div class="bg-linear-to-r from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm text-purple-600 dark:text-purple-400 font-medium mb-1">Divisional Education Office</div>
                                    <div class="text-lg font-bold text-purple-800 dark:text-purple-300 leading-tight">
                                        {{ $institution->divisionalEducationOffice?->short_name ?? 'Not Assigned' }}
                                    </div>
                                </div>
                                <div class="text-purple-500 dark:text-purple-400 shrink-0 ml-2">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map and Address Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6">
            <!-- Address Details -->
            <div class="lg:col-span-1">
                <div class="bg-slate-50 dark:bg-slate-900/50 rounded-xl p-5 h-full">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Location Details
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <div class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Full Address</div>
                            <div class="text-slate-900 dark:text-white leading-relaxed">
                                {{ $institution->address }}
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Province</div>
                                <div class="text-slate-900 dark:text-white">{{ $institution->district?->province?->province_name ?? 'Not Recorded' }}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">District</div>
                                <div class="text-slate-900 dark:text-white">{{ $institution->district?->district_name ?? 'Not Recorded' }}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Divisional Secretariat</div>
                                <div class="text-slate-500 dark:text-slate-500 italic">{{ $institution->divisionalSecretariatOffice?->name ?? 'Not Recorded' }}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Grama Niladhari Division</div>
                                <div class="text-slate-500 dark:text-slate-500 italic">{{ $institution->gnDivision?->name ?? 'Not Recorded' }}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">MOH Area</div>
                                <div class="text-slate-500 dark:text-slate-500 italic">{{ $institution->mohArea?->name ?? 'Not Recorded' }}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Police Station</div>
                                <div class="text-slate-500 dark:text-slate-500 italic">{{ $institution->policeStation?->name ?? 'Not Recorded' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Map Container -->
            <div class="lg:col-span-2">
                <div class="bg-slate-100 dark:bg-slate-900 rounded-xl overflow-hidden h-full border border-slate-300 dark:border-slate-700">
                    <!-- Map Header -->
                    <div class="bg-white dark:bg-slate-800 px-4 py-3 border-b border-slate-300 dark:border-slate-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                                <span class="font-medium text-slate-900 dark:text-white">School Location on Map</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button class="p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg">
                                    <svg class="w-4 h-4 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Interactive Map Component -->
                    <div class="w-full -mt-px relative z-10 border-t border-slate-200 dark:border-slate-700">
                        <x-maps.leaflet-display lat="{{ $institution->latitude }}" lng="{{ $institution->longitude }}" height="h-96" mapClass="rounded-b-xl border-none" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/30 border-t border-slate-200 dark:border-slate-700">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="text-sm text-slate-500 dark:text-slate-400">
                    Last updated: {{ $institution->updated_at->format('M d, Y') }}
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('institutions.basic-profile.pdf', $institution->id) }}" target="_blank"
                        class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg transition-colors flex items-center gap-2 relative z-10">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Generate PDF Report
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>