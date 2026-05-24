<section class="w-full">
    <x-offices.zeo.zeo-layout :officeId="$officeId">
        {{-- 1. Header Section --}}
        <header class="mb-10">
            <flux:heading size="xl" level="1" class="text-3xl! font-black tracking-tight text-slate-900 dark:text-white leading-none mb-3">
                {{ __('Staff Directory') }}
            </flux:heading>
            <flux:subheading size="lg" class="text-slate-500 dark:text-slate-500 font-medium max-w-2xl">
                {{ __('Manage and view the list of staff members, their roles, and professional history within this zonal education office.') }}
            </flux:subheading>
        </header>
        <div class="mt-8 space-y-8">
            {{-- Service Tabs / Filters --}}
            @if(isset($availableServices) && $availableServices->count() > 0)
            <div class="p-2 bg-slate-100 dark:bg-slate-800/50 rounded-2xl w-fit flex flex-wrap gap-1">
                <button wire:click="$set('selectedService', null)"
                    class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ empty($selectedService) ? 'bg-white dark:bg-slate-700 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:text-slate-500 dark:hover:text-slate-300' }}">
                    {{ __('All Staff') }}
                </button>
                @foreach($availableServices as $srv)
                <button wire:click="$set('selectedService', '{{ $srv->service_id }}')"
                    class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ $selectedService == $srv->service_id ? 'bg-white dark:bg-slate-700 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:text-slate-500 dark:hover:text-slate-300' }}">
                    {{ $srv->service->service_name ?? 'Unknown' }}
                </button>
                @endforeach
            </div>
            @endif

            {{-- Table Container --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-4xl shadow-sm overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-500 w-16 text-center">#</th>
                            <th class="px-6 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-500">Identity</th>
                            <th class="px-6 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-500">Position</th>
                            <th class="px-6 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-500">Service & Rank</th>
                            <th class="px-6 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-500 text-center">Experience</th>
                            <th class="px-6 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-500 text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                        @foreach ($staffList as $staff)
                        @php
                        $start = \Carbon\Carbon::parse($staff->appoint_date);
                        $duration = $start->diff(now());
                        @endphp
                        <tr class="hover:bg-indigo-50/30 dark:hover:bg-indigo-500/5 transition-all group">
                            <td class="px-6 py-5 text-center">
                                <span class="inline-flex items-center justify-center w-7 h-7 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-500 rounded-lg text-[10px] font-black">
                                    {{ $loop->iteration + ($staffList->currentPage() - 1) * $staffList->perPage() }}
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div>
                                        <div class="text-sm font-bold text-slate-900 dark:text-white leading-tight group-hover:text-indigo-600 transition-colors">
                                            {{$staff->employee->title->title_name ?? ''}} {{ $staff->employee->name_with_initials ?? '-' }}
                                        </div>
                                        <div class="text-[10px] font-mono text-slate-500 dark:text-slate-500 mt-1 uppercase tracking-widest">
                                            {{ $staff->employee->nic }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="inline-block whitespace-nowrap px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20 text-[10px] font-black uppercase tracking-widest">
                                    {{ $staff->position->position_name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <div class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ $staff->service->service_name ?? '-' }}</div>
                                <div class="text-[10px] font-black text-indigo-400 dark:text-indigo-500 uppercase tracking-tight mt-0.5">{{ $staff->rank->rank_name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-inner">
                                    <flux:icon name="clock" variant="micro" class="text-slate-300" />
                                    <span class="text-xs font-black text-slate-600 dark:text-slate-500 tracking-tighter">
                                        {{ $duration->y }}Y {{ $duration->m }}M
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-right">
                                @php
                                $serviceId = $staff->appointment?->service_id;
                                $routeName = match($serviceId) {
                                'SER001' => 'teacher.profile.index',
                                'SER002' => 'sltes.profile.index',
                                'SER003' => 'sltas.profile.index',
                                'SER004' => 'principal.profile.index',
                                'SER005' => 'sleas.profile.index',
                                'SER006' => 'slas.profile.index',
                                'SER007' => 'dos.profile.index',
                                'SER008' => 'mso.profile.index',
                                default => 'teacher.profile.index',
                                };
                                @endphp
                                <flux:button href="{{ route($routeName, $staff->employee->id) }}" icon="user" size="sm" variant="subtle" class="rounded-lg!" />
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $staffList->links() }}
            </div>

            {{-- Empty State --}}
            @if($staffList->count() === 0)
            <div class="py-24 flex flex-col items-center justify-center bg-slate-50 dark:bg-slate-900/50 rounded-[3rem] border-2 border-dashed border-slate-300 dark:border-slate-700">
                <div class="relative mb-6">
                    <div class="absolute inset-0 bg-indigo-100 dark:bg-indigo-900/20 rounded-full scale-150 blur-2xl opacity-50"></div>
                    <div class="relative p-6 bg-linear-to-b from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900 rounded-full shadow-inner">
                        <flux:icon name="user-group" variant="solid" class="w-16 h-16 text-slate-300 dark:text-slate-600" />
                    </div>
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-slate-200 uppercase tracking-tight">{{ __('No staff members found') }}</h3>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-500 max-w-xs text-center font-medium">
                    {{ __('No staff members are currently assigned to this zonal office under the selected criteria.') }}
                </p>
            </div>
            @endif
        </div>
    </x-offices.zeo.zeo-layout>
</section>