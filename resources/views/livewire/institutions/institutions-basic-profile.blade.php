<div>
    <div class="bg-white dark:bg-slate-800 rounded-xl max-w-7xl m-auto shadow-lg border border-slate-300 dark:border-slate-700 overflow-hidden">
        <!-- Header with School Info -->
        <div class="p-6 border-b border-slate-300 dark:border-slate-700">
            <div class="flex flex-col md:flex-row md:items-start gap-6">
                <!-- School Logo and Basic Info -->
                <div class="shrink-0">
                    <div class="flex flex-col items-center md:items-start gap-4">
                        <!-- Logo -->
                        <div class="relative">
                            <div class="h-24 w-24 rounded-full border-4 border-white dark:border-slate-700 shadow-lg overflow-hidden bg-linear-to-br from-blue-50 to-indigo-50 dark:from-slate-900 dark:to-slate-800">
                                <div class="w-full h-full flex items-center justify-center">
                                    <flux:icon.academic-cap variant="mini" class="size-12 text-indigo-600 dark:text-indigo-400" />
                                </div>
                            </div>
                            <!-- Status Badge -->
                            <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full shadow-md
                                {{ $institution->active_status 
                                    ? 'bg-green-500 text-white border-2 border-white dark:border-slate-700' 
                                    : 'bg-red-500 text-white border-2 border-white dark:border-slate-700' }}">
                                    {{ $institution->active_status ? 'ACTIVE' : 'INACTIVE' }}
                                </span>
                            </div>
                        </div>

                        <!-- Established Date -->
                        <div class="w-full text-center mt-1">
                            <span class="inline-block px-3 py-1 bg-slate-100 dark:bg-slate-700/50 text-slate-600 dark:text-slate-500 text-xs font-bold rounded-md shadow-sm border border-slate-300 dark:border-slate-600">
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
                                <flux:icon.building-office-2 variant="micro" class="size-3.5" />
                                {{ $institution->authority->authority_name }}
                            </span>
                            @endif

                            @if($institution->institutionLanguages)
                            <span class="px-3 py-1 bg-cyan-100 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-300 text-sm font-medium rounded-full flex items-center gap-1.5">
                                <flux:icon.language variant="micro" class="size-3.5" />
                                {{ $institution->institutionLanguages->name }}
                            </span>
                            @endif

                            @if($institution->facilities)
                            <span class="px-3 py-1 bg-fuchsia-100 dark:bg-fuchsia-900/30 text-fuchsia-700 dark:text-fuchsia-300 text-sm font-medium rounded-full flex items-center gap-1.5">
                                <flux:icon.building-library variant="micro" class="size-3.5" />
                                {{ $institution->facilities->name }}
                            </span>
                            @endif

                            @if($institution->ethnicity)
                            <span class="px-3 py-1 bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 text-sm font-medium rounded-full flex items-center gap-1.5">
                                <flux:icon.users variant="micro" class="size-3.5" />
                                {{ $institution->ethnicity->ethnicity_name }}
                            </span>
                            @endif

                            @if($institution->sport_s==0 || $institution->sport_s==1)
                            <span class="px-3 py-1 bg-lime-100 dark:bg-lime-900/30 text-lime-700 dark:text-lime-300 text-sm font-medium rounded-full flex items-center gap-1.5">
                                <flux:icon.trophy variant="micro" class="size-3.5" />
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
                                <flux:icon.phone variant="micro" />
                            </div>
                            <div>
                                <div class="text-sm text-slate-500 dark:text-slate-500">Phone</div>
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
                                <div class="text-sm text-slate-500 dark:text-slate-500">Email</div>
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
                                    <flux:icon.building-office variant="mini" class="size-8" />
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
                                    <flux:icon.building-office-2 variant="mini" class="size-8" />
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
                                    <flux:icon.building-library variant="mini" class="size-8" />
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
                        <flux:icon.map-pin variant="micro" class="size-5 text-slate-600 dark:text-slate-500" />
                        Location Details
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <div class="text-sm font-medium text-slate-600 dark:text-slate-500 mb-1">Full Address</div>
                            <div class="text-slate-900 dark:text-white leading-relaxed">
                                {{ $institution->address }}
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm font-medium text-slate-600 dark:text-slate-500 mb-1">Province</div>
                                <div class="text-slate-900 dark:text-white">{{ $institution->district?->province?->province_name ?? 'Not Recorded' }}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-slate-600 dark:text-slate-500 mb-1">District</div>
                                <div class="text-slate-900 dark:text-white">{{ $institution->district?->district_name ?? 'Not Recorded' }}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-slate-600 dark:text-slate-500 mb-1">Divisional Secretariat</div>
                                <div class="text-slate-500 dark:text-slate-500 italic">{{ $institution->divisionalSecretariatOffice?->name ?? 'Not Recorded' }}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-slate-600 dark:text-slate-500 mb-1">Grama Niladhari Division</div>
                                <div class="text-slate-500 dark:text-slate-500 italic">{{ $institution->gnDivision?->name ?? 'Not Recorded' }}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-slate-600 dark:text-slate-500 mb-1">MOH Area</div>
                                <div class="text-slate-500 dark:text-slate-500 italic">{{ $institution->mohArea?->name ?? 'Not Recorded' }}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-slate-600 dark:text-slate-500 mb-1">Police Station</div>
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
                                <flux:icon.map-pin variant="mini" class="size-5 text-red-500" />
                                <span class="font-medium text-slate-900 dark:text-white">School Location on Map</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button class="p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg">
                                    <flux:icon.arrows-pointing-out variant="micro" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Interactive Map Component -->
                    <div class="w-full -mt-px relative z-10 border-t border-slate-300 dark:border-slate-700">
                        <x-maps.leaflet-display lat="{{ $institution->latitude }}" lng="{{ $institution->longitude }}" height="h-96" mapClass="rounded-b-xl border-none" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/30 border-t border-slate-300 dark:border-slate-700">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="text-sm text-slate-500 dark:text-slate-500">
                    Last updated: {{ $institution->updated_at->format('M d, Y') }}
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('offices.institutions.basic-profile.pdf', $institution->id) }}" target="_blank"
                        class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg transition-colors flex items-center gap-2 relative z-10">
                        <flux:icon.document-text variant="micro" />
                        Generate PDF Report
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>