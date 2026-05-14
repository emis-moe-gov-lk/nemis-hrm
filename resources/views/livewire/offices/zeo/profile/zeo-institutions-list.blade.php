<section class="w-full">
    {{-- Page Header --}}
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Zonal Education Office') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">
            {{ __('Statistics about Zonal Education Office structure and staff distribution.') }}
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <x-offices.zeo.zeo-layout :officeId="$officeId">

        <h2 class="mb-4 text-xl font-semibold text-slate-900 dark:text-white">
            <flux:badge variant="pill" color="cyan" icon="building-office-2">{{ $institutionList->total() }}</flux:badge>
            <span>Institution List</span>
        </h2>

        {{-- Table --}}
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-slate-700 shadow-sm">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">

                {{-- Table Head --}}
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-slate-800 dark:to-slate-900">
                    <tr>
                        @foreach (['#', 'Name & Census No.', 'Contact', 'Staff List', 'Action'] as $head)
                        <th
                            class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider
                                       text-gray-700 dark:text-slate-300
                                       border-r border-gray-200 dark:border-slate-600 last:border-r-0">
                            {{ $head }}
                        </th>
                        @endforeach
                    </tr>
                </thead>

                {{-- Table Body --}}
                <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">

                    @forelse ($institutionList as $institution)
                    @php
                    $start = \Carbon\Carbon::parse($institution->established_year);
                    $duration = $start->diff(now('y'));
                    @endphp

                    <tr class="group transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-slate-700/50">

                        {{-- Row Number (Pagination-safe) --}}
                        <td
                            class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white
                                       border-r border-gray-200 dark:border-slate-600 last:border-r-0">
                            <span class="text-gray-500">{{ $loop->iteration + ($institutionList->currentPage() - 1) * $institutionList->perPage() }}</span>
                        </td>

                        <td
                            class="px-6 py-2 text-sm text-gray-700 dark:text-slate-300
                                       border-r border-gray-200 dark:border-slate-600 last:border-r-0">
                            <p>
                                <span class="font-semibold text-gray-900 dark:text-white">
                                    {{ $institution->name }}
                                </span><br>
                                <span class="text-xs text-gray-500 dark:text-slate-400">
                                    Census No.: {{ str_pad($institution->census_no, 5, '0', STR_PAD_LEFT) }}

                                </span>
                            </p>
                        </td>

                        {{-- Position --}}
                        <td
                            class="px-6 py-4 text-sm text-gray-700 dark:text-slate-300
                                       border-r border-gray-200 dark:border-slate-600 last:border-r-0 text-center">
                            <span>
                                {{ $institution->phone ?? '--' }}
                            </span>
                        </td>

                        {{-- Service --}}
                        <td
                            class="px-6 py-4 text-center text-sm text-gray-700 dark:text-slate-300
                                       border-r border-gray-200 dark:border-slate-600 last:border-r-0">
                            {{ $institution->staffList->count() }}
                        </td>

                        {{-- Service Duration --}}
                        <td class="px-6 py-4 text-center text-sm text-gray-700 dark:text-slate-300">
                            <flux:link href="{{ route('pdf.institutions-teachers-list', $institution->id) }}" target="_blank">Download</flux:link>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-6 text-center text-sm text-gray-500 dark:text-slate-400">
                            No staff found for this office.
                        </td>
                    </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $institutionList->links() }}
        </div>

    </x-offices.zeo.zeo-layout>
</section>