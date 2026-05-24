<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <x-page-header
        title="Institution Groups - Principals"
        subtitle="Principals in groups assigned to your user account."
        icon="building-office-2"
    />

    <x-institution-groups.institution-groups-layout :hasAssignedGroups="$hasAssignedGroups">
        @if (!$hasAssignedGroups)
            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center dark:border-slate-700 dark:bg-slate-900/50">
                <p class="text-base font-semibold text-slate-700 dark:text-slate-200">
                    {{ __("You don't have any assigned institution group.") }}
                </p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-500">
                    {{ __('Principal list is available only when you are assigned to at least one institution group.') }}
                </p>
            </div>
        @else
            <div class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-800">
                        <thead class="bg-slate-50 dark:bg-slate-800/60">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700 dark:text-slate-200">{{ __('Principal Name') }}</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700 dark:text-slate-200">{{ __('Position') }}</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700 dark:text-slate-200">{{ __('Institution') }}</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700 dark:text-slate-200">{{ __('Group') }}</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700 dark:text-slate-200">{{ __('Appointed Date') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse ($principals as $principal)
                                <tr>
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $principal->employee?->name_with_initials ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-slate-500 dark:text-slate-500">{{ $principal->position?->position_name ?? 'Principal' }}</td>
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-200">
                                        {{ $principal->institution?->name ?? 'N/A' }}
                                        <p class="text-xs text-slate-500 dark:text-slate-500">
                                            {{ __('Census No') }}: {{ $principal->institution?->census_no ? str_pad($principal->institution->census_no, 5, '0', STR_PAD_LEFT) : 'N/A' }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-200">
                                        {{ implode(', ', $institutionGroupMap[$principal->workplace_id] ?? []) ?: 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-500 dark:text-slate-500">
                                        {{ optional($principal->appoint_date)->format('Y-m-d') ?? 'N/A' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-10 text-center text-slate-500 dark:text-slate-500">
                                        {{ __('No principals found for your assigned group(s).') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                {{ $principals->links() }}
            </div>
        @endif
    </x-institution-groups.institution-groups-layout>
</div>
