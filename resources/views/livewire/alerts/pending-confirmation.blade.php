<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <x-page-header
        title="Pending Confirmation List"
        subtitle="Manage pending profile and account confirmations for {{ Auth::user()->workplace?->office_name ?? 'All Workplaces' }}."
        icon="bell"
    />

    <x-alerts.layout>
        <div>
            <div class="mb-4">
                @if (session('success'))
                <x-alert type="success" dismissible class="mb-4">
                    {{ session('success') }}
                </x-alert>
                @endif

                @if (session('error'))
                <x-alert type="error" dismissible class="mb-4">
                    {{ session('error') }}
                </x-alert>
                @endif

                @if (session('warning'))
                <x-alert type="warning" dismissible class="mb-4">
                    {{ session('warning') }}
                </x-alert>
                @endif

                @if (session('info'))
                <x-alert type="info" dismissible class="mb-4">
                    {{ session('info') }}
                </x-alert>
                @endif
            </div>

            <div class="my-6 flex items-center justify-end gap-3">

                {{-- Search Button (Modal Trigger) --}}
                <flux:modal.trigger name="search-profile">
                    <flux:input as="button" placeholder="Search Profile..." icon="magnifying-glass" kbd="⌘K"
                        class="w-48 md:w-60 cursor-pointer transition-all hover:shadow-sm focus:ring-2 focus:ring-blue-500" />
                </flux:modal.trigger>

                {{-- Search Modal --}}
                <flux:modal name="search-profile" class="md:w-md rounded-xl shadow-lg dark:bg-zinc-900 border dark:border-zinc-700">
                    <div class="space-y-6 p-4">
                        {{-- Header --}}
                        <div class="text-center">
                            <flux:heading size="lg" class="text-zinc-800 dark:text-zinc-100">
                                Search Pending Confirmation Profile
                            </flux:heading>
                            <flux:text class="mt-2 text-zinc-400 dark:text-zinc-400 text-sm">
                                Search for a pending confirmation profile by
                                <span class="font-semibold text-zinc-700 dark:text-zinc-300">NIC or email or contact number </span>.
                            </flux:text>
                        </div>

                        {{-- Search Input --}}
                        <div>
                            <flux:input icon="magnifying-glass" wire:model.live="query" placeholder="Type NIC or email or contact number..."
                                class="w-full focus:ring-2 focus:ring-blue-500 dark:bg-zinc-800 dark:border-zinc-700 dark:text-white" />
                        </div>

                        {{-- Results --}}
                        @if (!empty($results) && count($results) > 0)
                        <ul class="divide-y divide-zinc-200 dark:divide-zinc-700/50 mt-2 border-t border-zinc-200 dark:border-zinc-700/50 pt-2">
                            @foreach ($results as $employee)
                            <li
                                class="py-2.5 px-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/80 rounded-lg cursor-pointer transition-colors duration-200">
                                @php
                                // Determine route based on service
                                $routeName = match($employee->appointment?->service_id) {
                                'SER001' => 'teacher.profile.index',
                                'SER002' => 'sltes.profile.index',
                                'SER003' => 'sltas.profile.index',
                                'SER004' => 'principal.profile.index',
                                'SER005' => 'sleas.profile.index',
                                'SER006' => 'slas.profile.index',
                                'SER007' => 'dos.profile.index',
                                'SER008' => 'mso.profile.index',
                                default => 'employees.index',
                                };
                                @endphp

                                <a href="{{ route($routeName, $employee->id) }}" class="block w-full">
                                    <div class="flex justify-between items-center">
                                        <span class="font-semibold text-zinc-800 dark:text-zinc-100">
                                            {{ $employee->name_with_initials }}
                                        </span>
                                        <span class="text-sm font-medium text-zinc-400 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded-md">
                                            {{ $employee->nic }}
                                        </span>
                                    </div>
                                </a>

                            </li>
                            @endforeach
                        </ul>
                        @elseif(strlen($query) >= 10)
                        <p class="text-sm text-zinc-400 dark:text-zinc-400 text-center py-6 flex flex-col items-center gap-2">
                            <svg class="w-8 h-8 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            No pending confirmation profile found.
                        </p>
                        @endif


                    </div>
                </flux:modal>


            </div>

            <div class="overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm bg-white dark:bg-zinc-900/50">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider">
                                Name
                            </th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider">
                                Designation
                            </th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider">
                                Last update
                            </th>
                            <th class="px-6 py-3.5 text-right text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800/80 bg-white dark:bg-transparent">
                        @forelse($employees as $employee)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors duration-150 group">
                            <!-- Name Section -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-4">
                                    <div class="h-11 w-11 rounded-full overflow-hidden border border-zinc-200 dark:border-zinc-700 shadow-sm group-hover:ring-2 group-hover:ring-zinc-100 dark:group-hover:ring-zinc-700 transition-all">
                                        <img class="h-full w-full object-cover"
                                            src="{{ $employee->gender_id == 'G02' ? asset('images/profile_f.png') : asset('images/profile_m.png') }}"
                                            alt="Profile">
                                    </div>

                                    <div>
                                        <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 flex items-center gap-1">
                                            <flux:link href="{{ route('sltas.profile.index', $employee->id) }}" variant="ghost" class="text-zinc-900 dark:text-zinc-100 hover:text-blue-600 dark:hover:text-blue-400">
                                                {{ $employee->title->title_name ?? '' }} {{ $employee->name_with_initials }}
                                            </flux:link>
                                        </div>
                                        <div class="text-xs text-zinc-400 dark:text-zinc-400 mt-0.5">
                                            NIC: <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $employee->nic }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Designation Section -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $employee->currentAppointment?->position?->position_name ?? 'N/A' }}
                                </div>
                                <div class="text-xs text-zinc-400 dark:text-zinc-400 mt-0.5">
                                    {{ $employee->currentAppointment?->service?->service_name ?? 'N/A' }}
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($employee->currentAppointment?->appointment?->is_confirmed)
                                <span class="px-2.5 py-1 rounded-full bg-green-100 dark:bg-green-500/10 text-green-700 dark:text-green-400 text-xs font-semibold inline-flex items-center ring-1 ring-inset ring-green-600/20 dark:ring-green-500/20">
                                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Confirmed
                                </span>
                                @else
                                <span class="px-2.5 py-1 rounded-full bg-yellow-100 dark:bg-yellow-500/10 text-yellow-700 dark:text-yellow-400 text-xs font-semibold inline-flex items-center ring-1 ring-inset ring-yellow-600/20 dark:ring-yellow-500/20">
                                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Not Confirmed
                                </span>
                                @endif
                            </td>

                            <!-- Last Update -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $employee->updated_at->format('Y-m-d') }}
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">

                                    @php
                                    // Determine route name based on service
                                    $routeName = match($employee->appointment?->service_id) {
                                    'SER001' => 'teacher.profile.index',
                                    'SER002' => 'sltes.profile.index',
                                    'SER003' => 'sltas.profile.index',
                                    'SER004' => 'principal.profile.index',
                                    'SER005' => 'sleas.profile.index',
                                    'SER006' => 'slas.profile.index',
                                    'SER007' => 'dos.profile.index',
                                    'SER008' => 'mso.profile.index',
                                    default => 'employees.index',
                                    };
                                    @endphp

                                    <a href="{{ route($routeName, $employee->id) }}">
                                        <flux:button size="sm" icon="eye" variant="ghost" class="hover:bg-zinc-100 dark:hover:bg-zinc-800">View</flux:button>
                                    </a>

                                </div>
                            </td>
                        </tr>

                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="p-3 bg-zinc-100 dark:bg-zinc-800 rounded-full mb-4">
                                        <svg class="w-8 h-8 text-zinc-400 dark:text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">No employees found</h3>
                                    <p class="text-sm text-zinc-400 dark:text-zinc-400 mt-1">There are currently no employees pending confirmation.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $employees->links() }}
            </div>

        </div>

    </x-alerts.layout>
</div>