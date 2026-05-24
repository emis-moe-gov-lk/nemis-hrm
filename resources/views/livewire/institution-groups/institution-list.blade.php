<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <x-page-header
        title="Institution Groups"
        subtitle="Institutions in groups assigned to your user account."
        icon="building-office-2"
    />

    <x-institution-groups.institution-groups-layout :hasAssignedGroups="$hasAssignedGroups">
        @if (!$hasAssignedGroups)
            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center dark:border-slate-700 dark:bg-slate-900/50">
                <p class="text-base font-semibold text-slate-700 dark:text-slate-200">
                    {{ __("You don't have any assigned institution group.") }}
                </p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-500">
                    {{ __('Institutions list is available only when you are assigned to at least one institution group.') }}
                </p>
            </div>
        @else
            <div class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-800">
                        <thead class="bg-slate-50 dark:bg-slate-800/60">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700 dark:text-slate-200">{{ __('Group') }}</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700 dark:text-slate-200">{{ __('Institution') }}</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700 dark:text-slate-200">{{ __('Census No') }}</th>
                                <th class="px-4 py-3 text-right font-semibold text-slate-700 dark:text-slate-200">{{ __('Teachers') }}</th>
                                <th class="px-4 py-3 text-right font-semibold text-slate-700 dark:text-slate-200">{{ __('Principals') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse ($institutions as $institution)
                                <tr>
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $institution->group_name }}</td>
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $institution->name }}</td>
                                    <td class="px-4 py-3 text-slate-500 dark:text-slate-500">{{ $institution->census_no ? str_pad($institution->census_no, 5, '0', STR_PAD_LEFT) : 'N/A' }}</td>
                                    <td class="px-4 py-3 text-right font-semibold text-slate-700 dark:text-slate-200">{{ number_format($institution->total_teachers) }}</td>
                                    <td class="px-4 py-3 text-right font-semibold text-slate-700 dark:text-slate-200">{{ number_format($institution->total_principals) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-10 text-center text-slate-500 dark:text-slate-500">
                                        {{ __('No institutions found for your assigned group(s).') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                {{ $institutions->links() }}
            </div>
        @endif
    </x-institution-groups.institution-groups-layout>
</div>
