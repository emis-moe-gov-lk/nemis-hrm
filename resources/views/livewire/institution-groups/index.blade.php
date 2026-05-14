<section class="w-full">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Institution Groups') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">
            {{ __('Overview of institution groups assigned to your user account.') }}
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <x-institution-groups.institution-groups-layout :hasAssignedGroups="$hasAssignedGroups">
        @if (!$hasAssignedGroups)
            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center dark:border-slate-700 dark:bg-slate-900/50">
                <p class="text-base font-semibold text-slate-700 dark:text-slate-200">
                    {{ __("You don't have any assigned institution group.") }}
                </p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    {{ __('Please contact an administrator to assign you to an institution group.') }}
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                @foreach ($groups as $group)
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <div class="mb-4 flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                                    {{ $group['group_name'] }}
                                </h3>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                    {{ __('Group Code') }}: {{ $group['group_code'] }}
                                </p>
                            </div>
                        </div>

                        @if (!empty($group['description']))
                            <p class="mb-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ $group['description'] }}
                            </p>
                        @endif

                        <div class="space-y-2">
                            <div class="flex items-center justify-between rounded-lg bg-slate-50 p-3 dark:bg-slate-800/60">
                                <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('Assigned Officer') }}</span>
                                <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ $group['officer_name'] }}</span>
                            </div>

                            <div class="flex items-center justify-between rounded-lg bg-slate-50 p-3 dark:bg-slate-800/60">
                                <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('Officer Position') }}</span>
                                <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ $group['officer_position'] }}</span>
                            </div>

                            <div class="flex items-center justify-between rounded-lg bg-slate-50 p-3 dark:bg-slate-800/60">
                                <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('Number of Schools') }}</span>
                                <span class="text-base font-bold text-slate-900 dark:text-white">{{ number_format($group['total_schools']) }}</span>
                            </div>

                            <div class="flex items-center justify-between rounded-lg bg-slate-50 p-3 dark:bg-slate-800/60">
                                <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('Number of Teachers') }}</span>
                                <span class="text-base font-bold text-slate-900 dark:text-white">{{ number_format($group['total_teachers']) }}</span>
                            </div>

                            <div class="flex items-center justify-between rounded-lg bg-slate-50 p-3 dark:bg-slate-800/60">
                                <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('Number of Principals') }}</span>
                                <span class="text-base font-bold text-slate-900 dark:text-white">{{ number_format($group['total_principals']) }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-institution-groups.institution-groups-layout>
</section>
