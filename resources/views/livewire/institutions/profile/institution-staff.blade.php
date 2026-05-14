<section class="w-full">
    <div class="relative mb-8 w-full">
        <flux:heading size="xl" level="1" class="text-gray-900 dark:text-white font-bold">
            {{ __('Staff') }}
        </flux:heading>
        <flux:subheading size="lg" class="mb-6 text-gray-600 dark:text-gray-300">
            {{ __('View the list of staff members and their appointment details.') }}
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <x-institutions.institution-layout :institutionId="$id">
        <div class="bg-white dark:bg-slate-900">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Staff List
                </h2>

                <!-- Optional: Add filters or actions here -->
                <div class="flex items-center space-x-3">
                    <span class="text-sm text-gray-500 dark:text-gray-400 mx-4">
                        Total: {{ $staffList->total() }} staff members
                    </span>
                </div>
            </div>

            <!-- Service Tabs -->
            @if(isset($availableServices) && $availableServices->count() > 0)
            <div class="mb-6 border-b border-gray-200 dark:border-slate-700 overflow-x-auto scrollbar-hide">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button wire:click="$set('selectedService', null)" 
                        class="{{ empty($selectedService) ? 'border-indigo-500 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400 border-b-2' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-slate-400 dark:hover:text-slate-300 border-b-2' }} whitespace-nowrap pb-4 px-1 font-medium text-sm transition-colors cursor-pointer">
                        All Staff
                    </button>
                    @foreach($availableServices as $srv)
                    <button wire:click="$set('selectedService', '{{ $srv->service_id }}')" 
                        class="{{ $selectedService == $srv->service_id ? 'border-indigo-500 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400 border-b-2' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-slate-400 dark:hover:text-slate-300 border-b-2' }} whitespace-nowrap pb-4 px-1 font-medium text-sm transition-colors cursor-pointer">
                        {{ $srv->service->service_name ?? 'Unknown Service' }}
                    </button>
                    @endforeach
                </nav>
            </div>
            @endif

            <!-- Table Container -->
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-slate-700 shadow-sm">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                    <thead class="bg-linear-to-r from-gray-50 to-gray-100 dark:from-slate-800 dark:to-slate-900">
                        <tr>
                            @foreach (['#', 'Name & ID', 'Contact Number', 'Position', 'Service & Rank', 'Service Duration'] as $head)
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-slate-300 uppercase tracking-wider border-r border-gray-200 dark:border-slate-600 last:border-r-0">
                                <div class="flex items-center space-x-1">
                                    <span>{{ $head }}</span>
                                    <!-- Optional: Add sort icons -->
                                </div>
                            </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">
                        @foreach ($staffList as $staff)
                        @php
                        $start = \Carbon\Carbon::parse($staff->appoint_date);
                        $duration = $start->diff(now());
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors duration-200 group">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white border-r border-gray-200 dark:border-slate-600 last:border-r-0">
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 rounded-full text-xs font-medium">
                                    {{ $loop->iteration + ($staffList->currentPage() - 1) * $staffList->perPage() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-slate-300 border-r border-gray-200 dark:border-slate-600 last:border-r-0">
                                <p>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{$staff->employee->title->title_name}} {{ $staff->employee->name_with_initials ?? '-' }}</span><br />
                                    <span class="text-xs text-gray-500 dark:text-white">
                                        NIC: {{ $staff->employee->nic }}
                                    </span>
                                </p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-slate-300 border-r border-gray-200 dark:border-slate-600 last:border-r-0">
                                {{ $staff->employee->phone ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-slate-300 border-r border-gray-200 dark:border-slate-600 last:border-r-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-green-800 dark:text-green-300">
                                    {{ $staff->position->position_name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-slate-300 border-r border-gray-200 dark:border-slate-600 last:border-r-0">
                                {{ $staff->service->service_name ?? '-' }} ({{ $staff->rank->rank_name ?? '-' }})
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-slate-300 border-r border-gray-200 dark:border-slate-600 last:border-r-0">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $duration->y }}y {{ $duration->m }}m
                                </span>
                            </td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $staffList->links() }}
            </div>

            <!-- Empty State -->
            @if($staffList->count() === 0)
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No staff members found</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No staff members are currently assigned to this institution.</p>
            </div>
            @endif
        </div>
    </x-institutions.institution-layout>
</section>