<section class="w-full">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1" class="text-slate-900 dark:text-white">{{ __('Zonal Education Office') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6 text-slate-500 dark:text-slate-400">
            {{ __('Statistics about Zonal Education Office structure and staff distribution.') }}
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <x-offices.zeo.zeo-layout :officeId="$officeId">

        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white">
                {{ __('Institution Groups') }}
            </h2>
            <a href="{{ route('offices.zeo.profile.institution-group.create', $officeId) }}" wire:navigate>
                <flux:button variant="primary" icon="plus" class="shadow-sm">
                    {{ __('Create Group') }}
                </flux:button>
            </a>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($groups as $group)
                <div class="relative overflow-hidden rounded-3xl border border-blue-900/40 bg-linear-to-b from-[#071a43] to-[#051332] p-7 text-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                    <div class="absolute right-0 top-0 -z-10 h-32 w-32 rounded-bl-full bg-linear-to-bl from-blue-500/20 to-transparent opacity-80"></div>

                    <div class="mb-6 flex items-start justify-between">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl border border-blue-500/30 bg-blue-500/10">
                            <svg class="h-8 w-8 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                            </svg>
                        </div>

                        <span class="inline-flex items-center gap-2 rounded-full border border-emerald-400/40 bg-emerald-400/10 px-4 py-2 text-sm font-semibold text-emerald-200">
                            <span class="h-2 w-2 rounded-full bg-emerald-300"></span>
                            {{ __('Active') }}
                        </span>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-3xl font-bold leading-tight text-white">
                            {{ $group['group_name'] }}
                        </h3>
                        @if (!empty($group['description']))
                            <p class="mt-1 line-clamp-2 text-sm text-blue-200/85">
                                {{ $group['description'] }}
                            </p>
                        @endif
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between rounded-2xl border border-white/5 bg-white/5 p-3">
                            <span class="text-sm text-blue-100/80">{{ __('Officer Name') }}</span>
                            <span class="max-w-[52%] truncate text-right text-sm font-semibold text-white">
                                {{ $group['officer_name'] }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between rounded-2xl border border-white/5 bg-white/5 p-3">
                            <span class="text-sm text-blue-100/80">{{ __('Total Schools') }}</span>
                            <span class="text-2xl font-bold text-white">
                                {{ number_format($group['total_schools']) }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between rounded-2xl border border-white/5 bg-white/5 p-3">
                            <span class="text-sm text-blue-100/80">{{ __('Total Teachers') }}</span>
                            <span class="text-2xl font-bold text-white">
                                {{ number_format($group['total_teachers']) }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-8 flex items-center justify-between">
                        <div class="flex -space-x-2">
                            @foreach ($group['institution_short_labels'] as $shortLabel)
                                <div class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-[#051332] bg-cyan-50 text-xs font-bold text-cyan-700">
                                    {{ $shortLabel }}
                                </div>
                            @endforeach

                            @if ($group['remaining_institution_count'] > 0)
                                <div class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-[#051332] bg-slate-800 text-xs font-bold text-slate-200">
                                    +{{ $group['remaining_institution_count'] }}
                                </div>
                            @endif
                        </div>

                        <a href="{{ route('offices.zeo.profile.institution-groups.view', ['id' => $officeId, 'groupCode' => $group['group_code']]) }}" wire:navigate>
                            <flux:button variant="ghost" size="sm" icon="arrow-right" class="border border-blue-300/20 bg-white/5 text-blue-100 hover:bg-white/10">
                                {{ __('View Group') }}
                            </flux:button>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center dark:border-slate-700 dark:bg-slate-900/50">
                    <p class="text-base font-semibold text-slate-700 dark:text-slate-200">{{ __('No institution groups found.') }}</p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('Create a group to see it listed here.') }}</p>
                </div>
            @endforelse
        </div>

    </x-offices.zeo.zeo-layout>
</section>
