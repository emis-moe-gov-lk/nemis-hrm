<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8 antialiased">
    {{-- Professional Header --}}
    <x-page-header
        title="Institution Groups"
        subtitle="Overview of institution clusters assigned to your jurisdiction. Monitor leadership, institutional records, and workforce distribution."
        icon="briefcase" />

    <x-institution-groups.institution-groups-layout :hasAssignedGroups="$hasAssignedGroups">
        @if (!$hasAssignedGroups)
        <div class="relative overflow-hidden rounded-[3rem] border border-dashed border-slate-300 dark:border-zinc-700 bg-slate-50/50 dark:bg-zinc-900/30 p-16 text-center group">
            <div class="absolute inset-0 bg-linear-to-br from-indigo-500/5 to-purple-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
            <div class="relative z-10 flex flex-col items-center">
                <div class="h-20 w-20 rounded-3xl bg-white dark:bg-zinc-800 shadow-xl flex items-center justify-center text-slate-500 mb-6">
                    <flux:icon.briefcase variant="mini" class="h-10 w-10" />
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">
                    {{ __("No Groups Assigned") }}
                </h3>
                <p class="text-slate-500 dark:text-slate-500 max-w-md mx-auto leading-relaxed">
                    {{ __('You currently do not have any institution groups assigned to your profile. Please reach out to your administrator to manage your portfolio.') }}
                </p>
            </div>
        </div>
        @else
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
            @foreach ($groups as $group)
            <div class="group relative overflow-hidden bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 rounded-[2.5rem] p-8 lg:p-10 transition-all duration-500 hover:shadow-[0_30px_70px_-15px_rgba(0,0,0,0.12)] hover:border-transparent">

                {{-- Interactive Gradient Border --}}
                <div class="absolute inset-0 p-[1px] rounded-[2.5rem] bg-linear-to-br from-emerald-500 via-teal-500 to-blue-500 opacity-0 group-hover:opacity-100 transition-opacity duration-500 -z-10"></div>
                <div class="absolute inset-[1px] rounded-[2.45rem] bg-white dark:bg-zinc-900 -z-10"></div>

                <div class="mb-10 flex items-start justify-between relative z-10">
                    <div class="space-y-2">
                        <h3 class="text-2xl font-black text-slate-900 dark:text-white leading-tight group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                            {{ $group['group_name'] }}
                        </h3>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-md bg-slate-50 dark:bg-zinc-800 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">
                                ID: {{ $group['group_code'] }}
                            </span>
                        </div>
                    </div>
                    <div class="h-12 w-12 rounded-2xl bg-linear-to-br from-emerald-50 to-teal-50 dark:from-zinc-800 dark:to-zinc-800/50 flex items-center justify-center text-emerald-500 shadow-inner group-hover:scale-110 transition-transform duration-500">
                        <flux:icon.building-office-2 variant="mini" />
                    </div>
                </div>

                @if (!empty($group['description']))
                <p class="mb-10 text-[15px] font-medium text-slate-500 dark:text-zinc-400 leading-relaxed group-hover:text-slate-600 dark:group-hover:text-zinc-300 transition-colors">
                    {{ $group['description'] }}
                </p>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 relative z-10">
                    <div class="p-4 rounded-2xl bg-slate-50/50 dark:bg-zinc-800/40 border border-slate-200 dark:border-zinc-700 flex flex-col gap-1 transition-all group-hover:border-emerald-100 dark:group-hover:border-emerald-900/30 group-hover:bg-white dark:group-hover:bg-zinc-900">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-500">{{ __('Primary Officer') }}</span>
                        <span class="text-sm font-black text-slate-900 dark:text-white">{{ $group['officer_name'] }}</span>
                        <span class="text-[10px] font-bold text-emerald-600/70 dark:text-emerald-400/70">{{ $group['officer_position'] }}</span>
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <div class="p-3 rounded-2xl bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 flex flex-col items-center justify-center transition-all group-hover:shadow-sm">
                            <span class="text-lg font-black text-slate-900 dark:text-white leading-none">{{ number_format($group['total_schools']) }}</span>
                            <span class="text-[8px] font-black uppercase tracking-tighter text-slate-500 mt-1">Institutions</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 flex flex-col items-center justify-center transition-all group-hover:shadow-sm">
                            <span class="text-lg font-black text-slate-900 dark:text-white leading-none">{{ number_format($group['total_teachers']) }}</span>
                            <span class="text-[8px] font-black uppercase tracking-tighter text-slate-500 mt-1">Teachers</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 flex flex-col items-center justify-center transition-all group-hover:shadow-sm">
                            <span class="text-lg font-black text-slate-900 dark:text-white leading-none">{{ number_format($group['total_principals']) }}</span>
                            <span class="text-[8px] font-black uppercase tracking-tighter text-slate-500 mt-1">Principals</span>
                        </div>
                    </div>
                </div>

                {{-- Action Indicator --}}
                <div class="mt-10 pt-6 border-t border-slate-200 dark:border-zinc-700 flex items-center justify-between opacity-0 group-hover:opacity-100 translate-y-4 group-hover:translate-y-0 transition-all duration-500">
                    <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600">Active Jurisdiction</span>
                    <div class="flex items-center gap-2 text-emerald-600">
                        <span class="text-xs font-bold">Open Portfolio</span>
                        <flux:icon.arrow-right variant="micro" />
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </x-institution-groups.institution-groups-layout>
</div>