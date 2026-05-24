<section class="w-full">
    <x-offices.zeo.zeo-layout :officeId="$officeId">
        {{-- 1. Header Section --}}
        <header class="mb-10">
            <flux:heading size="xl" level="1" class="text-3xl! font-black tracking-tight text-slate-900 dark:text-white leading-none mb-3">
                {{ __('Teachers Directory') }}
            </flux:heading>
            <flux:subheading size="lg" class="text-slate-500 dark:text-slate-500 font-medium max-w-2xl">
                {{ __('Manage and view the list of teachers, their roles, and professional history within this zonal education office region.') }}
            </flux:subheading>
        </header>
        <div class="mt-8 space-y-8">
            {{-- Table Container --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-4xl shadow-sm overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-500 w-16 text-center">#</th>
                            <th class="px-6 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-500">Identity</th>
                            <th class="px-6 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-500">Position & Service</th>

                            <th class="px-6 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-500">Working Place</th>
                            <th class="px-6 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-500 text-center">Experience</th>
                            <th class="px-6 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-500 text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                        @foreach ($teachersList as $teacher)
                        @php
                        $start = \Carbon\Carbon::parse($teacher->appoint_date);
                        $duration = $start->diff(now());
                        @endphp
                        <tr class="hover:bg-indigo-50/30 dark:hover:bg-indigo-500/5 transition-all group">
                            <td class="px-6 py-5 text-center">
                                <span class="inline-flex items-center justify-center w-7 h-7 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-500 rounded-lg text-[10px] font-black">
                                    {{ $loop->iteration + ($teachersList->currentPage() - 1) * $teachersList->perPage() }}
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div>
                                        <div class="text-sm font-bold text-slate-900 dark:text-white leading-tight group-hover:text-indigo-600 transition-colors">
                                            {{$teacher->employee->title->title_name ?? ''}} {{ $teacher->employee->name_with_initials ?? '-' }}
                                        </div>
                                        <div class="text-[10px] font-mono text-slate-500 dark:text-slate-500 mt-1 uppercase tracking-widest">
                                            {{ $teacher->employee->nic }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20 text-[10px] font-black uppercase tracking-widest block w-fit mb-1">
                                    {{ $teacher->position->position_name ?? '-' }}
                                </span>
                                <div class="text-[10px] font-black text-indigo-400 dark:text-indigo-500 uppercase tracking-tight">{{ $teacher->service->service_name ?? '-' }} - {{ $teacher->rank->rank_name ?? '-' }}</div>
                            </td>

                            <td class="px-6 py-5 text-slate-700 dark:text-slate-300 text-sm font-bold">
                                {{ str($teacher->workplace?->office_name ?? ($teacher->workplace?->name ?? '-'))->title() }}
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
                                <flux:button href="{{ route('teacher.profile.index', $teacher->employee->id) }}" icon="user" size="sm" variant="subtle" class="rounded-lg!" />
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $teachersList->links() }}
            </div>

            {{-- Empty State --}}
            @if($teachersList->count() === 0)
            <div class="py-24 flex flex-col items-center justify-center bg-slate-50 dark:bg-slate-900/50 rounded-[3rem] border-2 border-dashed border-slate-300 dark:border-slate-700">
                <div class="relative mb-6">
                    <div class="absolute inset-0 bg-indigo-100 dark:bg-indigo-900/20 rounded-full scale-150 blur-2xl opacity-50"></div>
                    <div class="relative p-6 bg-linear-to-b from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900 rounded-full shadow-inner">
                        <flux:icon name="user-group" variant="solid" class="w-16 h-16 text-slate-300 dark:text-slate-600" />
                    </div>
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-slate-200 uppercase tracking-tight">{{ __('No teachers found') }}</h3>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-500 max-w-xs text-center font-medium">
                    {{ __('No teachers are currently assigned within this zonal education office region.') }}
                </p>
            </div>
            @endif
        </div>
    </x-offices.zeo.zeo-layout>
</section>