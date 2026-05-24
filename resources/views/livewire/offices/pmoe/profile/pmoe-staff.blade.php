<section class="w-full">
    <x-offices.pmoe.layout :officeid="$officeId">
        {{-- Section Title with Count Badge --}}
        <h2 class="mb-5 text-xl font-semibold text-slate-900 dark:text-white flex items-center gap-2">
            <flux:badge variant="pill" color="cyan" icon="user-group">
                {{ count($staffList) }}
            </flux:badge>
            <span> Office Staff List</span>
        </h2>

        {{-- Table Container --}}
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-slate-700 shadow-sm">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                {{-- Table Head --}}
                <thead class="bg-linear-to-r from-gray-50 to-gray-100 dark:from-slate-800 dark:to-slate-900">
                    <tr>
                        @foreach ([
                        '#',
                        'Name',
                        'Service & Position',
                        'Service Duration',
                        'Action'
                        ] as $head)
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-slate-300 border-r border-gray-200 dark:border-slate-600 last:border-r-0">
                            {{ $head }}
                        </th>
                        @endforeach
                    </tr>
                </thead>

                {{-- Table Body --}}
                <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">
                    @forelse ($staffList as $index => $staff)
                    @php
                    $start = \Carbon\Carbon::parse($staff->appoint_date);
                    $duration = $start->diff(now());
                    $teacher = \App\Models\Teacher::where('employee_id', $staff->employee_id)->first();
                    @endphp
                    <tr class="group transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-slate-700/50">
                        {{-- Index --}}
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white border-r border-gray-200 dark:border-slate-600 last:border-r-0">
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 text-xs font-medium text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                {{ $index + 1 }}
                            </span>
                        </td>

                        {{-- Name --}}
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-slate-300 border-r border-gray-200 dark:border-slate-600 last:border-r-0">
                            <span class="font-semibold text-gray-900 dark:text-white">
                                {{ $staff->employee->title->title_name ?? '' }} {{ $staff->employee->name_with_initials ?? '-' }}
                            </span><br>
                            <span class="text-xs text-gray-500 dark:text-slate-500">
                                NIC: {{ $staff->employee->nic ?? '-' }}
                            </span>
                        </td>

                        {{-- Position --}}
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-slate-300 border-r border-gray-200 dark:border-slate-600 last:border-r-0">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                {{ $staff->position->position_name ?? '-' }}
                            </span><br />
                            <span class="text-xs text-gray-500 dark:text-slate-500 font-medium mt-1 inline-block">
                                {{ $staff->service->service_name ?? '-' }} • Rank {{ $staff->rank->rank_name ?? '-' }}
                            </span>
                        </td>

                        {{-- Service Duration --}}
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-slate-300 border-r border-gray-200 dark:border-slate-600 last:border-r-0">
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800">
                                <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0" />
                                </svg>
                                {{ $duration->y }}y {{ $duration->m }}m {{ $duration->d }}d
                            </span>
                        </td>

                        {{-- Action/Profile View --}}
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-slate-300">
                            <flux:button href="{{ route('teacher.profile.index', $staff->employee->id) }}" size="sm" variant="subtle" icon="eye" class="hover:shadow-sm">
                                View
                            </flux:button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-6 text-center text-sm text-gray-500 dark:text-slate-500">
                            No staff found for this office.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-offices.pmoe.layout>
</section>