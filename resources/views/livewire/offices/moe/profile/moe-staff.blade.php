<section class="w-full">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Staff') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">
            {{ __('List of staff under this Ministry office.') }}
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <x-offices.moe.layout :officeId="$officeId">
        <div class="p-4">
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white mb-4">Staff List</h2>

            <div class="overflow-auto border rounded-md border-gray-200 dark:border-slate-700">
                <table class="min-w-full text-sm text-left text-slate-700 dark:text-slate-300">
                    <thead class="bg-gray-100 dark:bg-slate-800 text-slate-900 dark:text-slate-100 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3 border border-gray-200 dark:border-slate-700">#</th>
                            <th class="px-4 py-3 border border-gray-200 dark:border-slate-700">Employee ID</th>
                            <th class="px-4 py-3 border border-gray-200 dark:border-slate-700">Name</th>
                            <th class="px-4 py-3 border border-gray-200 dark:border-slate-700">Position</th>
                            <th class="px-4 py-3 border border-gray-200 dark:border-slate-700">Service</th>
                            <th class="px-4 py-3 border border-gray-200 dark:border-slate-700">Rank</th>
                            <th class="px-4 py-3 border border-gray-200 dark:border-slate-700">Service Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($staffList as $index => $staff)
                            @php
                                $start = \Carbon\Carbon::parse($staff->appoint_date);
                                $duration = $start->diff(now());
                            @endphp
                            <tr class="bg-white dark:bg-slate-900 border-t border-gray-100 dark:border-slate-700">
                                <td class="px-4 py-2 border border-gray-100 dark:border-slate-700">
                                    {{ $loop->iteration + ($staffList->currentPage() - 1) * $staffList->perPage() }}
                                </td>
                                <td class="px-4 py-2 border border-gray-100 dark:border-slate-700">{{ $staff->employee_id }}</td>
                                <td class="px-4 py-2 border border-gray-100 dark:border-slate-700">{{ $staff->employee->name_with_initials ?? '-' }}</td>
                                <td class="px-4 py-2 border border-gray-100 dark:border-slate-700">{{ $staff->position->name_en ?? '-' }}</td>
                                <td class="px-4 py-2 border border-gray-100 dark:border-slate-700">{{ $staff->service->name_en ?? '-' }}</td>
                                <td class="px-4 py-2 border border-gray-100 dark:border-slate-700">{{ $staff->rank->name_en ?? '-' }}</td>
                                <td class="px-4 py-2 border border-gray-100 dark:border-slate-700">
                                    {{ $duration->y }}y {{ $duration->m }}m {{ $duration->d }}d
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $staffList->links('pagination::tailwind') }}
            </div>
        </div>
    </x-offices.moe.layout>
</section>
