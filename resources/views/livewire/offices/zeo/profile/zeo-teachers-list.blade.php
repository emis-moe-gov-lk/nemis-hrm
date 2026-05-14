<section class="w-full">
    {{-- Page Header --}}
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">
            {{ __('Zonal Education Office') }}
        </flux:heading>

        <flux:subheading size="lg" class="mb-6">
            {{ __('Statistics about Zonal Education Office structure and staff distribution.') }}
        </flux:subheading>

        <flux:separator variant="subtle" />
    </div>

    {{-- Layout --}}
    <x-offices.zeo.zeo-layout :officeId="$officeId">

        <h2 class="mb-4 text-xl font-semibold text-slate-900 dark:text-white">
            <flux:badge variant="pill" color="cyan" icon="user-group">{{ $teachersList->total() }}</flux:badge>
            <span>Teachers List</span>
        </h2>

        {{-- Table Wrapper --}}
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-slate-700 shadow-sm">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">

                {{-- TABLE HEADER --}}
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-slate-800 dark:to-slate-900">
                    <tr>
                        @foreach (['#', 'Name & NIC', 'Position & Service', 'Contact', 'Working Place', 'Service Duration'] as $head)
                        <th
                            class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider
                                       text-gray-700 dark:text-slate-300
                                       border-r border-gray-200 dark:border-slate-600 last:border-r-0">
                            {{ $head }}
                        </th>
                        @endforeach
                    </tr>
                </thead>

                {{-- TABLE BODY --}}
                <tbody class="divide-y divide-gray-200 bg-white dark:divide-slate-700 dark:bg-slate-800">

                    @forelse ($teachersList as $teacher)
                    @php
                    $start = \Carbon\Carbon::parse($teacher->appoint_date);
                    $duration = $start->diff(now());
                    @endphp

                    <tr class="group transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-slate-700/50">

                        {{-- ROW NUMBER --}}
                        <td
                            class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white
                                       border-r border-gray-200 dark:border-slate-600 last:border-r-0">
                            <span class=" text-gray-500">{{ $loop->iteration + ($teachersList->currentPage() - 1) * $teachersList->perPage() }}</span>
                        </td>

                        {{-- NAME & NIC --}}
                        <td
                            class="px-6 py-2 text-sm text-gray-700 dark:text-slate-300
                                       border-r border-gray-200 dark:border-slate-600 last:border-r-0">
                            <p>
                                <a href="{{ route('teacher.profile.index', $teacher->employee->id) }}" class=" hover:underline">
                                    <span class="font-semibold text-gray-900 dark:text-white">
                                        {{ $teacher->employee->name_with_initials ?? '-' }}
                                    </span>
                                </a><br>
                                <span class="text-xs text-gray-500 dark:text-slate-400">
                                    NIC:{{ $teacher->employee->nic ?? '-' }}
                                </span>
                            </p>
                        </td>

                        {{-- POSITION --}}
                        <td
                            class="px-6 py-2 text-sm text-gray-700 dark:text-slate-300
                                       border-r border-gray-200 dark:border-slate-600 last:border-r-0">

                            <span>
                                {{ $teacher->position->position_name ?? '-' }}
                            </span><br />
                            <span class=" text-xs text-gray-500 dark:text-slate-400">
                                {{ $teacher->service->service_name ?? '-' }} - {{ $teacher->rank->rank_name ?? '-' }}
                            </span>
                        </td>

                        {{-- CONTACT --}}
                        <td
                            class="px-6 py-2 text-sm text-gray-700 dark:text-slate-300
                                       border-r border-gray-200 dark:border-slate-600 last:border-r-0">
                            {{ $teacher->employee->phone ?? '-' }}
                        </td>

                        {{-- WORKING PLACE --}}
                        <td
                            class="px-6 py-2 text-sm text-gray-700 dark:text-slate-300
                                       border-r border-gray-200 dark:border-slate-600 last:border-r-0">
                            {{ str($teacher->workplace?->office_name)->title() ?? '-' }}
                        </td>

                        {{-- SERVICE DURATION --}}
                        <td class="px-6 py-2 text-sm text-gray-700 dark:text-slate-300">
                            <span
                                class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                                           bg-blue-100 text-blue-800 border border-blue-200
                                           dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800">
                                <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0" />
                                </svg>
                                {{ $duration->y }}y {{ $duration->m }}m {{ $duration->d }}d
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-6 text-center text-sm text-gray-500 dark:text-slate-400">
                            No staff found for this Zonal Education Office.
                        </td>
                    </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="mt-6">
            {{ $teachersList->links() }}
        </div>

    </x-offices.zeo.zeo-layout>
</section>