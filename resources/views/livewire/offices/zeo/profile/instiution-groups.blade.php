<section class="w-full">
    <x-offices.zeo.zeo-layout :officeId="$officeId">
        {{-- 1. Header Section --}}
        <header class="mb-10">
            <flux:heading size="xl" level="1" class="text-3xl! font-black tracking-tight text-slate-900 dark:text-white leading-none mb-3">
                {{ __('Institution Groups') }}
            </flux:heading>
            <flux:subheading size="lg" class="text-slate-500 dark:text-slate-500 font-medium max-w-2xl">
                {{ __('Manage educational institution groups and assigned monitoring officers within this zonal education office.') }}
            </flux:subheading>
        </header>
        <div class="mt-8 space-y-8">
            {{-- Catalog Header --}}
            <div class="flex items-center justify-between">
                <flux:heading size="lg" class="font-black text-slate-900 dark:text-white uppercase tracking-tight">
                    {{ __('Groups Catalog') }}
                </flux:heading>
                <a href="{{ route('offices.zeo.profile.institution-group.create', $officeId) }}" wire:navigate>
                    <flux:button variant="primary" icon="plus" class="shadow-sm">
                        {{ __('Create Group') }}
                    </flux:button>
                </a>
            </div>

            {{-- Grid of Groups --}}
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($groups as $group)
                <div class="group relative bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-3xl p-7 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl flex flex-col justify-between">
                    <div>
                        {{-- Top Row: Icon & Status Badge --}}
                        <div class="mb-6 flex items-start justify-between">
                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl border border-indigo-100 dark:border-slate-800 bg-indigo-50/50 dark:bg-slate-800/50 text-indigo-600 dark:text-indigo-400 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300 shadow-xs">
                                <flux:icon name="building-office-2" class="w-7 h-7" />
                            </div>

                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 dark:bg-emerald-500/10 px-3 py-1.5 text-[10px] font-black uppercase tracking-widest text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20">
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                {{ __('Active') }}
                            </span>
                        </div>

                        {{-- Title & Description --}}
                        <div class="mb-6">
                            <h3 class="text-2xl font-black leading-tight text-slate-900 dark:text-white tracking-tight group-hover:text-indigo-600 transition-colors">
                                {{ $group['group_name'] }}
                            </h3>
                            @if (!empty($group['description']))
                            <p class="mt-2 line-clamp-2 text-xs font-semibold text-slate-500 dark:text-slate-500 leading-relaxed">
                                {{ $group['description'] }}
                            </p>
                            @endif
                        </div>

                        {{-- Statistics Rows --}}
                        <div class="space-y-3">
                            <div class="flex items-center justify-between rounded-2xl border border-slate-200 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-800/50 p-3.5 transition-colors">
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-500">{{ __('Officer Name') }}</span>
                                <span class="max-w-[60%] truncate text-right text-xs font-black text-slate-700 dark:text-slate-300">
                                    {{ $group['officer_name'] }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between rounded-2xl border border-slate-200 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-800/50 p-3.5 transition-colors">
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-500">{{ __('Total Schools') }}</span>
                                <span class="text-lg font-black text-slate-800 dark:text-slate-100">
                                    {{ number_format($group['total_schools']) }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between rounded-2xl border border-slate-200 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-800/50 p-3.5 transition-colors">
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-500">{{ __('Total Teachers') }}</span>
                                <span class="text-lg font-black text-slate-800 dark:text-slate-100">
                                    {{ number_format($group['total_teachers']) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Footer Actions --}}
                    <div class="mt-8 flex items-center justify-between pt-5 border-t border-slate-100 dark:border-slate-800/60">
                        <div class="flex -space-x-2">
                            @foreach ($group['institution_short_labels'] as $shortLabel)
                            <div class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-white dark:border-slate-900 bg-indigo-50 dark:bg-slate-800 text-[10px] font-black text-indigo-600 dark:text-indigo-400 shadow-sm">
                                {{ $shortLabel }}
                            </div>
                            @endforeach

                            @if ($group['remaining_institution_count'] > 0)
                            <div class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-white dark:border-slate-900 bg-slate-100 dark:bg-slate-800 text-[10px] font-black text-slate-600 dark:text-slate-500 shadow-sm">
                                +{{ $group['remaining_institution_count'] }}
                            </div>
                            @endif
                        </div>

                        <a href="{{ route('offices.zeo.profile.institution-groups.view', ['id' => $officeId, 'groupCode' => $group['group_code']]) }}" wire:navigate>
                            <flux:button variant="subtle" size="sm" icon="arrow-right" class="rounded-lg!" />
                        </a>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-24 flex flex-col items-center justify-center bg-slate-50 dark:bg-slate-900/50 rounded-[3rem] border-2 border-dashed border-slate-300 dark:border-slate-700">
                    <div class="relative mb-6">
                        <div class="absolute inset-0 bg-indigo-100 dark:bg-indigo-900/20 rounded-full scale-150 blur-2xl opacity-50"></div>
                        <div class="relative p-6 bg-linear-to-b from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900 rounded-full shadow-inner">
                            <flux:icon name="building-office-2" variant="solid" class="w-16 h-16 text-slate-300 dark:text-slate-600" />
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 dark:text-slate-200 uppercase tracking-tight">{{ __('No institution groups found') }}</h3>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-500 max-w-xs text-center font-medium">
                        {{ __('No school groups are currently registered. Create a group to start categorizing schools.') }}
                    </p>
                </div>
                @endforelse
            </div>
        </div>
    </x-offices.zeo.zeo-layout>
</section>