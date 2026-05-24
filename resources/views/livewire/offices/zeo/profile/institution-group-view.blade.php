<section class="w-full">
    <x-offices.zeo.zeo-layout :officeId="$officeId">
        <div class="relative mb-6 w-full">
            <flux:heading size="xl" level="1" class="text-slate-900 dark:text-white">{{ __('Zonal Education Office') }}</flux:heading>
            <flux:subheading size="lg" class="mb-6 text-slate-500 dark:text-slate-500">
                {{ __('Statistics about Zonal Education Office structure and staff distribution.') }}
            </flux:subheading>
            <flux:separator variant="subtle" />
        </div>
        <div class="mb-5 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-900 dark:text-white">
                    {{ $group->group_name }}
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-500">
                    {{ __('Group Code') }}: {{ $group->group_code }}
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('offices.zeo.profile.institution-groups.edit', ['id' => $officeId, 'groupCode' => $group->group_code]) }}" wire:navigate>
                    <flux:button variant="primary" icon="pencil-square">
                        {{ __('Edit Group') }}
                    </flux:button>
                </a>

                <flux:button
                    type="button"
                    variant="danger"
                    icon="trash"
                    wire:click="deleteGroup"
                    onclick="if (!confirm('Are you sure you want to delete this institution group? This action cannot be undone.')) { event.stopImmediatePropagation(); }">
                    {{ __('Delete Group') }}
                </flux:button>

                <a href="{{ route('offices.zeo.profile.institution-groups', $officeId) }}" wire:navigate>
                    <flux:button variant="ghost" icon="arrow-left">
                        {{ __('Back to Groups') }}
                    </flux:button>
                </a>
            </div>
        </div>

        <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="rounded-2xl border border-slate-300 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-500">{{ __('Officer Name') }}</p>
                <p class="mt-1 text-base font-semibold text-slate-900 dark:text-white">
                    {{ $group->assignedPerson?->name_with_initials ?? 'Not assigned' }}
                </p>
            </div>

            <div class="rounded-2xl border border-slate-300 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-500">{{ __('Total Schools in the group') }}</p>
                <p class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($totalSchools) }}</p>
            </div>

            <div class="rounded-2xl border border-slate-300 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-500">{{ __('Total Teachers in the group') }}</p>
                <p class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($totalTeachers) }}</p>
            </div>
        </div>

        @if (!empty($group->group_description))
        <div class="mb-6 rounded-2xl border border-slate-300 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-500">{{ __('Description') }}</p>
            <p class="mt-1 text-sm text-slate-700 dark:text-slate-300">{{ $group->group_description }}</p>
        </div>
        @endif

        <div class="rounded-2xl border border-slate-300 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <h3 class="mb-3 text-lg font-semibold text-slate-900 dark:text-white">{{ __('Institutions in this group') }}</h3>

            <div class="space-y-3">
                @forelse ($institutions as $institution)
                <div class="flex flex-col gap-2 rounded-xl border border-slate-300 bg-slate-50 p-3 md:flex-row md:items-center md:justify-between dark:border-slate-700 dark:bg-slate-900/60">
                    <div>
                        <p class="font-semibold text-slate-900 dark:text-white">{{ $institution->name }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-500">
                            {{ __('Census No') }}:
                            {{ $institution->census_no ? str_pad($institution->census_no, 5, '0', STR_PAD_LEFT) : 'N/A' }}
                        </p>
                    </div>

                    <div class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                        {{ number_format($institution->total_teachers) }} {{ __('Teachers') }}
                    </div>
                </div>
                @empty
                <div class="rounded-xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-500">
                    {{ __('No institutions found in this group.') }}
                </div>
                @endforelse
            </div>
        </div>
    </x-offices.zeo.zeo-layout>
</section>