<section class="w-full">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('PMOE Staff List') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('List of staff under the selected PMOE.') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <x-offices.deo.deo-layout :officeId="$officeId">
        <div class="p-4">
            <h2 class="text-xl font-semibold mb-4">Staff List</h2>
            <table class="min-w-full bg-white dark:bg-slate-800 border dark:border-slate-700">
                <thead class="bg-gray-200 dark:bg-slate-700 text-sm text-left text-slate-800 dark:text-slate-200">
                    <tr>
                        <th class="px-4 py-2 border">#</th>
                        <th class="px-4 py-2 border">Employee ID</th>
                        <th class="px-4 py-2 border">Name</th>
                        <th class="px-4 py-2 border">Position</th>
                        <th class="px-4 py-2 border">Service</th>
                        <th class="px-4 py-2 border">Rank</th>
                        <th class="px-4 py-2 border">Service Duration</th>
                        <th class="px-4 py-2 border">Appointment Subject</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-slate-800 dark:text-slate-100">
                    @foreach ($staffList as $index => $staff)
                        @php
                            $start = \Carbon\Carbon::parse($staff->appoint_date);
                            $duration = $start->diff(now());
                            $teacher = \App\Models\Teacher::where('employee_id', $staff->employee_id)->first();
                        @endphp
                        <tr class="border-t dark:border-slate-700">
                            <td class="px-4 py-2 border">{{ $index + 1 }}</td>
                            <td class="px-4 py-2 border">{{ $staff->employee_id }}</td>
                            <td class="px-4 py-2 border">{{ $staff->employee->name_with_initials ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $staff->position->name_en ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $staff->service->service_name ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $staff->rank->name_en ?? '-' }}</td>
                            <td class="px-4 py-2 border">
                                {{ $duration->y }}y {{ $duration->m }}m {{ $duration->d }}d
                            </td>
                            <td class="px-4 py-2 border">
                                {{ $teacher->appointment_subject ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-offices.deo.deo-layout>
</section>