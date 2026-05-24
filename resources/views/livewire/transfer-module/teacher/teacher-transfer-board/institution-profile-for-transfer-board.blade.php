<div class="p-6 lg:p-8">
    @php
        $activeTransferCount = $inboundApplications->whereIn('status', ['submitted', 'processing'])->count()
            + $outboundApplications->whereIn('status', ['submitted', 'processing'])->count();
    @endphp

    <div class="flex flex-col md:flex-row md:items-start justify-between mb-8 gap-4">
        <div class="max-w-4xl">
            <flux:heading size="xl" level="1">{{ __('Institution Transfer Insight') }}</flux:heading>
            <flux:subheading size="lg">{{ __('Review staffing, cadre balance, and inbound/outbound transfer pressure for this school using the same board workspace.') }}</flux:subheading>

            <div class="mt-5">
                <p class="text-2xl font-black text-slate-900 dark:text-white">{{ $institution->name }}</p>
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    @if($selectedPreference)
                        <flux:badge color="blue" size="sm" class="uppercase tracking-tighter font-black">{{ __('Preference #') }}{{ $selectedPreference->preference_order }}</flux:badge>
                    @else
                        <flux:badge color="rose" size="sm" class="uppercase tracking-tighter font-black">{{ __('Need-Based School') }}</flux:badge>
                    @endif
                    <flux:badge variant="neutral" size="sm" class="uppercase tracking-tighter font-black">{{ $application->transfer_application_id }}</flux:badge>
                    <flux:badge variant="neutral" size="sm" class="uppercase tracking-tighter font-black">{{ $institution->authority?->authority_name ?? __('Authority N/A') }}</flux:badge>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <flux:button href="{{ route('transfer-board.teacher-profile-for-transfer-board', ['id' => $application->transfer_application_id, 'board' => $board, 'selectedBoardId' => $selectedBoardId]) }}" variant="ghost" icon="chevron-left" size="sm">{{ __('Back to Application') }}</flux:button>
            <flux:button href="{{ $this->backRoute }}" variant="ghost" icon="squares-2x2" size="sm">{{ __($this->backLabel) }}</flux:button>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
        <div class="bg-white dark:bg-zinc-900 p-4 rounded-2xl border border-slate-300 dark:border-zinc-700 shadow-sm relative overflow-hidden group hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400 mb-1">{{ __('Current Staff') }}</p>
                    <h3 class="text-3xl font-black text-slate-900 dark:text-white">{{ number_format($staffRows->count()) }}</h3>
                </div>
                <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                    <flux:icon name="users" size="lg" />
                </div>
            </div>
            <div class="mt-2 relative z-10 text-xs font-medium text-slate-500 dark:text-zinc-400">{{ __('Permanent and attached staff combined') }}</div>
            <div class="absolute -right-2 -bottom-2 opacity-[0.03] dark:opacity-[0.05] group-hover:scale-110 transition-transform duration-500">
                <flux:icon name="users" class="w-24 h-24" />
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 p-4 rounded-2xl border border-slate-300 dark:border-zinc-700 shadow-sm relative overflow-hidden group hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400 mb-1">{{ __('Active Transfer Pressure') }}</p>
                    <h3 class="text-3xl font-black text-slate-900 dark:text-white">{{ number_format($activeTransferCount) }}</h3>
                </div>
                <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400">
                    <flux:icon name="arrows-right-left" size="lg" />
                </div>
            </div>
            <div class="mt-2 relative z-10 text-xs font-medium text-slate-500 dark:text-zinc-400">{{ __('Pending inbound and outbound movements') }}</div>
            <div class="absolute -right-2 -bottom-2 opacity-[0.03] dark:opacity-[0.05] group-hover:scale-110 transition-transform duration-500">
                <flux:icon name="arrows-right-left" class="w-24 h-24" />
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 p-4 rounded-2xl border border-slate-300 dark:border-zinc-700 shadow-sm relative overflow-hidden group hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400 mb-1">{{ __('Cadre Shortage') }}</p>
                    <h3 class="text-3xl font-black text-slate-900 dark:text-white">{{ number_format($cadreStats['shortage']) }}</h3>
                </div>
                <div class="p-3 rounded-xl bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400">
                    <flux:icon name="arrow-trending-down" size="lg" />
                </div>
            </div>
            <div class="mt-2 relative z-10 text-xs font-medium text-slate-500 dark:text-zinc-400">{{ __('Approved posts currently below required strength') }}</div>
            <div class="absolute -right-2 -bottom-2 opacity-[0.03] dark:opacity-[0.05] group-hover:scale-110 transition-transform duration-500">
                <flux:icon name="arrow-trending-down" class="w-24 h-24" />
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 p-4 rounded-2xl border border-slate-300 dark:border-zinc-700 shadow-sm relative overflow-hidden group hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400 mb-1">{{ __('Cadre Excess') }}</p>
                    <h3 class="text-3xl font-black text-slate-900 dark:text-white">{{ number_format($cadreStats['excess']) }}</h3>
                </div>
                <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400">
                    <flux:icon name="arrow-trending-up" size="lg" />
                </div>
            </div>
            <div class="mt-2 relative z-10 text-xs font-medium text-slate-500 dark:text-zinc-400">{{ __('Approved strength exceeded in current staffing') }}</div>
            <div class="absolute -right-2 -bottom-2 opacity-[0.03] dark:opacity-[0.05] group-hover:scale-110 transition-transform duration-500">
                <flux:icon name="arrow-trending-up" class="w-24 h-24" />
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
        <div class="xl:col-span-4 space-y-8">
            <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-300 dark:border-zinc-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/50 dark:bg-zinc-800/30 border-b border-slate-200 dark:border-zinc-700">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Institution Snapshot') }}</h3>
                </div>

                <div class="p-6 space-y-5">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Census / Workplace') }}</p>
                        <p class="mt-1 text-sm font-semibold text-slate-700 dark:text-zinc-200">{{ $institution->census_no ?? __('N/A') }} <span class="text-slate-300 dark:text-zinc-700 mx-2">|</span>{{ $institution->workplace_id }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Category') }}</p>
                        <p class="mt-1 text-sm font-semibold text-slate-700 dark:text-zinc-200">{{ $institution->institutionCategory?->institution_category_name ?? __('N/A') }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Type / Grade Span') }}</p>
                        <p class="mt-1 text-sm font-semibold text-slate-700 dark:text-zinc-200">{{ $institution->institutionType?->institution_types_name ?? __('N/A') }}</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">{{ $institution->gradeSpan?->grade_span_name ?? __('N/A') }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Language / Gender') }}</p>
                        <p class="mt-1 text-sm font-semibold text-slate-700 dark:text-zinc-200">{{ $institution->institutionLanguages?->name ?? __('N/A') }}</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">{{ $institution->typeByGender?->name ?? __('N/A') }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Zonal / Divisional') }}</p>
                        <p class="mt-1 text-sm font-semibold text-slate-700 dark:text-zinc-200">{{ $institution->zonalEducationOffice?->name ?? __('N/A') }}</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">{{ $institution->divisionalEducationOffice?->name ?? __('N/A') }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Contact') }}</p>
                        <p class="mt-1 text-sm font-semibold text-slate-700 dark:text-zinc-200">{{ $institution->phone ?? __('No phone recorded') }}</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">{{ $institution->email ?? __('No email recorded') }}</p>
                    </div>
                    <div class="pt-4 border-t border-slate-300 dark:border-zinc-700">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Address') }}</p>
                        <p class="mt-1 text-sm leading-relaxed text-slate-600 dark:text-zinc-300">{{ $institution->address ?? __('No address recorded') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-300 dark:border-zinc-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/50 dark:bg-zinc-800/30 border-b border-slate-200 dark:border-zinc-700">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Cadre Summary') }}</h3>
                </div>

                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="rounded-2xl border border-slate-300 dark:border-zinc-700 bg-slate-50/50 dark:bg-zinc-800/20 p-4">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Approved') }}</p>
                            <p class="mt-2 text-2xl font-black text-slate-900 dark:text-white">{{ $cadreStats['approved'] }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-300 dark:border-zinc-700 bg-slate-50/50 dark:bg-zinc-800/20 p-4">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Filled') }}</p>
                            <p class="mt-2 text-2xl font-black text-slate-900 dark:text-white">{{ $cadreStats['filled'] }}</p>
                        </div>
                    </div>

                    @if($activeCircular)
                        <div class="rounded-2xl border border-slate-300 dark:border-zinc-700 bg-slate-50/50 dark:bg-zinc-800/20 p-4">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Active Circular') }}</p>
                            <p class="mt-2 text-sm font-black text-slate-900 dark:text-white">{{ $activeCircular->circular_no }}</p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">{{ $activeCircular->issued_date }}</p>
                        </div>
                    @else
                        <div class="rounded-2xl border border-dashed border-slate-300 dark:border-zinc-700 p-4 text-sm text-slate-500 dark:text-zinc-400">
                            {{ __('No active DMS cadre circular is available for this institution.') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="xl:col-span-8 space-y-8">
            <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-300 dark:border-zinc-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/50 dark:bg-zinc-800/30 border-b border-slate-200 dark:border-zinc-700 flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Existing Staff At This Station') }}</h3>
                        <p class="mt-1 text-sm text-slate-600 dark:text-zinc-300">{{ __('Includes permanent and attached staff, with transfer-application visibility for the board.') }}</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-zinc-800/50">
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Staff Member') }}</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Assignment') }}</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Subject / Service') }}</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Working Period') }}</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Transfer Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
                            @forelse($staffRows as $staff)
                                @php
                                    $staffApplication = $staff['transfer_application'];
                                    $staffStatus = $staffApplication ? $this->statusBadge($staffApplication->status) : null;
                                @endphp
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/20 transition-all duration-200">
                                    <td class="px-6 py-4 align-top">
                                        <p class="text-sm font-black text-slate-900 dark:text-white">{{ $staff['name'] }}</p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">{{ $staff['nic'] }}</p>
                                        @if($staff['rank_label'])
                                            <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">{{ $staff['rank_label'] }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 align-top">
                                        <flux:badge :color="$staff['assignment_color']" size="sm" class="uppercase tracking-tighter font-black">{{ $staff['assignment_type'] }}</flux:badge>
                                        @if($staff['assignment_type'] === 'Attached')
                                            <p class="mt-2 text-xs text-slate-500 dark:text-zinc-400">{{ __('Home station') }}: {{ $staff['home_station'] }}</p>
                                        @endif
                                        <p class="mt-2 text-xs text-slate-500 dark:text-zinc-400">{{ $staff['position_label'] }}</p>
                                    </td>
                                    <td class="px-6 py-4 align-top">
                                        <p class="text-sm font-semibold text-slate-800 dark:text-zinc-200">{{ $staff['subject'] }}</p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">{{ $staff['service_label'] }}</p>
                                        @if($staff['medium'])
                                            <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">{{ $staff['medium'] }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 align-top">
                                        <p class="text-sm font-semibold text-slate-800 dark:text-zinc-200">{{ $staff['station_period'] }}</p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">{{ $staff['station_start_date']?->format('M d, Y') ?? __('N/A') }}</p>
                                    </td>
                                    <td class="px-6 py-4 align-top">
                                        @if($staffApplication)
                                            <flux:badge :color="$staffStatus['color']" size="sm" class="uppercase tracking-tighter font-black">{{ $staffStatus['label'] }}</flux:badge>
                                            <p class="mt-2 text-xs font-semibold text-slate-500 dark:text-zinc-400">{{ $staffApplication->transfer_application_id }}</p>
                                        @else
                                            <span class="text-sm text-slate-500 dark:text-zinc-400">{{ __('No active transfer request') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-16 h-16 rounded-full bg-slate-50 dark:bg-zinc-800/50 flex items-center justify-center mb-4">
                                                <flux:icon name="users" size="lg" class="text-slate-300 dark:text-zinc-600" />
                                            </div>
                                            <h3 class="text-lg font-bold text-slate-800 dark:text-zinc-200 mb-1">{{ __('No staff records found') }}</h3>
                                            <p class="text-sm text-slate-500 dark:text-zinc-400">{{ __('There are no staff records available for this institution right now.') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-300 dark:border-zinc-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/50 dark:bg-zinc-800/30 border-b border-slate-200 dark:border-zinc-700">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Cadre Detail By Subject') }}</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-zinc-800/50">
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Subject / Medium') }}</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400 text-right">{{ __('Approved') }}</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400 text-right">{{ __('Filled') }}</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400 text-right">{{ __('Gap') }}</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
                            @forelse($cadreRows as $row)
                                @php
                                    $gapClass = $row['diff'] < 0 ? 'text-rose-600 dark:text-rose-400' : ($row['diff'] > 0 ? 'text-blue-600 dark:text-blue-400' : 'text-slate-900 dark:text-white');
                                    $statusColor = $row['status'] === 'Deficit' ? 'rose' : ($row['status'] === 'Excess' ? 'blue' : 'green');
                                @endphp
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/20 transition-all duration-200">
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-black text-slate-900 dark:text-white">{{ $row['subject_name'] }}</p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">{{ $row['medium_name'] }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-semibold text-slate-800 dark:text-zinc-200">{{ $row['approved_posts'] }}</td>
                                    <td class="px-6 py-4 text-right text-sm font-semibold text-slate-800 dark:text-zinc-200">{{ $row['filled_posts'] }}</td>
                                    <td class="px-6 py-4 text-right text-sm font-black {{ $gapClass }}">{{ $row['diff'] > 0 ? '+' . $row['diff'] : $row['diff'] }}</td>
                                    <td class="px-6 py-4">
                                        <flux:badge :color="$statusColor" size="sm" class="uppercase tracking-tighter font-black">{{ $row['status'] }}</flux:badge>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-14 text-center text-slate-500 dark:text-zinc-400">{{ __('Cadre details are not available for this institution yet.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-300 dark:border-zinc-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/50 dark:bg-zinc-800/30 border-b border-slate-200 dark:border-zinc-700">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Inbound Transfer Applications') }}</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-zinc-800/50">
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Application') }}</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Teacher') }}</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Subject / Medium') }}</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Current Station Experience') }}</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
                            @forelse($inboundApplications as $inbound)
                                @php $badge = $this->statusBadge($inbound->status); @endphp
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/20 transition-all duration-200">
                                    <td class="px-6 py-4 align-top">
                                        <a href="{{ route('transfer-board.teacher-profile-for-transfer-board', ['id' => $inbound->transfer_application_id, 'board' => $board]) }}" class="text-sm font-black text-slate-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                            {{ $inbound->transfer_application_id }}
                                        </a>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">{{ $inbound->created_at?->format('M d, Y') }}</p>
                                    </td>
                                    <td class="px-6 py-4 align-top">
                                        <p class="text-sm font-black text-slate-900 dark:text-white">{{ ($inbound->employee?->title?->title_name ?? '') . ' ' . ($inbound->employee?->name_with_initials ?? $inbound->employee?->full_name ?? __('N/A')) }}</p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">{{ $inbound->currentWorkplace?->office_name ?? __('N/A') }}</p>
                                    </td>
                                    <td class="px-6 py-4 align-top">
                                        <p class="text-sm font-semibold text-slate-800 dark:text-zinc-200">{{ $inbound->teacher?->currentTeachingSubject?->name_en ?? $inbound->teacher?->appointmentSubject?->name_en ?? $inbound->teacher?->mainSubject?->name_en ?? __('Subject N/A') }}</p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">{{ $inbound->teacher?->medium?->name ?? __('Medium N/A') }}</p>
                                    </td>
                                    <td class="px-6 py-4 align-top">
                                        <p class="text-sm font-semibold text-slate-800 dark:text-zinc-200">{{ $inbound->current_workplace_service_years }}</p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">{{ $inbound->current_workplace_join_date?->format('M d, Y') ?? __('N/A') }}</p>
                                    </td>
                                    <td class="px-6 py-4 align-top">
                                        <flux:badge :color="$badge['color']" size="sm" class="uppercase tracking-tighter font-black">{{ $badge['label'] }}</flux:badge>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-14 text-center text-slate-500 dark:text-zinc-400">{{ __('No active inbound transfer applications target this institution right now.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-slate-300 dark:border-zinc-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/50 dark:bg-zinc-800/30 border-b border-slate-200 dark:border-zinc-700">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Outbound Transfer Applications') }}</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-zinc-800/50">
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Application') }}</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Teacher') }}</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Subject / Medium') }}</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Current Station Experience') }}</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
                            @forelse($outboundApplications as $outbound)
                                @php $badge = $this->statusBadge($outbound->status); @endphp
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/20 transition-all duration-200">
                                    <td class="px-6 py-4 align-top">
                                        <a href="{{ route('transfer-board.teacher-profile-for-transfer-board', ['id' => $outbound->transfer_application_id, 'board' => $board]) }}" class="text-sm font-black text-slate-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                            {{ $outbound->transfer_application_id }}
                                        </a>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">{{ $outbound->created_at?->format('M d, Y') }}</p>
                                    </td>
                                    <td class="px-6 py-4 align-top">
                                        <p class="text-sm font-black text-slate-900 dark:text-white">{{ ($outbound->employee?->title?->title_name ?? '') . ' ' . ($outbound->employee?->name_with_initials ?? $outbound->employee?->full_name ?? __('N/A')) }}</p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">{{ __('Preference count') }}: {{ $outbound->preferences?->count() ?? 0 }}</p>
                                    </td>
                                    <td class="px-6 py-4 align-top">
                                        <p class="text-sm font-semibold text-slate-800 dark:text-zinc-200">{{ $outbound->teacher?->currentTeachingSubject?->name_en ?? $outbound->teacher?->appointmentSubject?->name_en ?? $outbound->teacher?->mainSubject?->name_en ?? __('Subject N/A') }}</p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">{{ $outbound->teacher?->medium?->name ?? __('Medium N/A') }}</p>
                                    </td>
                                    <td class="px-6 py-4 align-top">
                                        <p class="text-sm font-semibold text-slate-800 dark:text-zinc-200">{{ $outbound->current_workplace_service_years }}</p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">{{ $outbound->current_workplace_join_date?->format('M d, Y') ?? __('N/A') }}</p>
                                    </td>
                                    <td class="px-6 py-4 align-top">
                                        <flux:badge :color="$badge['color']" size="sm" class="uppercase tracking-tighter font-black">{{ $badge['label'] }}</flux:badge>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-14 text-center text-slate-500 dark:text-zinc-400">{{ __('No active outbound transfer applications originate from this institution right now.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
