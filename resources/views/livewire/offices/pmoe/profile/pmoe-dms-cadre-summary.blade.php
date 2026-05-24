<section class="w-full">
    <x-offices.pmoe.layout :officeid="$officeId">
        {{-- 1. Header Section --}}
        <header class="mb-10">
            <flux:heading size="xl" level="1">
                {{ __('Cadre of the Provincial Ministry of Education') }}
            </flux:heading>
            <flux:subheading size="lg" class="text-slate-500 dark:text-slate-500 font-medium max-w-2xl">
                {{ __('Manage and view the cadre of the provincial ministry of education, their roles, and professional history within this region.') }}
            </flux:subheading>
        </header>
        @if($circular)
        <div class="flex justify-end w-full mb-6">
            <div class="flex items-center gap-3 px-4 py-3 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-2xl shadow-sm w-full sm:w-fit">
                <div class="flex-1 sm:flex-none text-right">
                    <span class="text-[9px] font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider block mb-0.5">{{ __('Circular') }}</span>
                    <span class="text-sm font-extrabold text-blue-600 dark:text-blue-400 tracking-tight">{{ $circular->circular_no }}</span>
                </div>

                <flux:separator vertical class="h-8" />

                <div class="flex-1 sm:flex-none text-right">
                    <span class="text-[9px] font-bold text-slate-500 dark:text-slate-500 uppercase tracking-wider block mb-0.5">{{ __('Date') }}</span>
                    <span class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ $circular->issued_date }}</span>
                </div>
            </div>
        </div>
        @else
        <div class="flex justify-end w-full mb-6">
            <div class="flex items-center gap-3 px-4 py-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50 rounded-2xl shadow-sm w-full sm:w-fit">
                <span class="text-sm font-bold text-amber-700 dark:text-amber-400">{{ __('No active cadre circular found.') }}</span>
            </div>
        </div>
        @endif

        {{-- Filter Section --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-2 items-center w-full mb-8">
            <flux:select wire:model.live="authority" class="w-full">
                <flux:select.option value="null">All authorities</flux:select.option>
                @foreach ($authorityOption as $authority)
                <flux:select.option value="{{ $authority->authority_id }}">
                    {{ $authority->authority_name }}
                </flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="zonal" class="w-full">
                <flux:select.option value="null">All zonal</flux:select.option>
                @foreach ($zonalOption as $zonal)
                <flux:select.option value="{{ $zonal->workplace_id }}">
                    {{ $zonal->short_name }}
                </flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="division" class="w-full">
                <flux:select.option value="null">All division</flux:select.option>
                @foreach ($divisionOption as $division)
                <flux:select.option value="{{ $division->workplace_id }}">
                    {{ $division->short_name }}
                </flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="institution" class="w-full">
                <flux:select.option value="null">All institutions</flux:select.option>
                @foreach ($institutionOption as $inst)
                <flux:select.option value="{{ $inst->workplace_id }}">
                    {{ $inst->name }}
                </flux:select.option>
                @endforeach
            </flux:select>
        </div>

        @php
        $typeLabels = [
        1 => ['label' => 'Teacher / Subjects', 'class' => 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border-blue-100 dark:border-blue-500/20'],
        2 => ['label' => 'Principal / Designation', 'class' => 'bg-purple-50 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400 border-purple-100 dark:border-purple-500/20'],
        3 => ['label' => 'Other', 'class' => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-500/20'],
        ];

        $groupedRows = collect($rows)->groupBy('subject_type');
        $grandApproved = collect($rows)->sum('approved_posts');
        $grandFilled = collect($rows)->sum('filled_posts');
        $grandDiff = collect($rows)->sum('diff');
        @endphp

        {{-- GRAND SUMMARY (Top Section) --}}
        @if($rows)
        <div class="w-full mb-8 grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-6">
            {{-- Total Approved Card --}}
            <div class="relative overflow-hidden bg-white dark:bg-[#151518] border border-slate-300 dark:border-slate-700/60 rounded-[1.25rem] p-6 shadow-sm">
                <!-- Background Icon -->
                <div class="absolute -right-4 -top-6 opacity-30 dark:opacity-20 text-slate-300 dark:text-slate-700 pointer-events-none transform rotate-12">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-28 h-28">
                        <path fill-rule="evenodd" d="M1.5 5.625c0-1.036.84-1.875 1.875-1.875h17.25c1.035 0 1.875.84 1.875 1.875v12.75c0 1.035-.84 1.875-1.875 1.875H3.375A1.875 1.875 0 011.5 18.375V5.625zM21 9.375A.375.375 0 0020.625 9h-7.5a.375.375 0 00-.375.375v1.5c0 .207.168.375.375.375h7.5a.375.375 0 00.375-.375v-1.5zm0 3.75a.375.375 0 00-.375-.375h-7.5a.375.375 0 00-.375.375v1.5c0 .207.168.375.375.375h7.5a.375.375 0 00.375-.375v-1.5zm0 3.75a.375.375 0 00-.375-.375h-7.5a.375.375 0 00-.375.375v1.5c0 .207.168.375.375.375h7.5a.375.375 0 00.375-.375v-1.5zM10.875 18.75a.375.375 0 00.375-.375v-1.5a.375.375 0 00-.375-.375h-7.5a.375.375 0 00-.375.375v1.5c0 .207.168.375.375.375h7.5zM3.375 15h7.5a.375.375 0 00.375-.375v-1.5a.375.375 0 00-.375-.375h-7.5a.375.375 0 00-.375.375v1.5c0 .207.168.375.375.375zm0-3.75h7.5a.375.375 0 00.375-.375v-1.5a.375.375 0 00-.375-.375h-7.5a.375.375 0 00-.375.375v1.5c0 .207.168.375.375.375z" clip-rule="evenodd" />
                    </svg>
                </div>
                <!-- Content -->
                <div class="relative z-10 flex flex-col justify-center h-full">
                    <div class="text-[10px] font-bold text-slate-500 dark:text-slate-500 uppercase tracking-widest mb-1">{{ __('Total Approved') }}</div>
                    <div class="text-4xl font-black text-slate-900 dark:text-white tracking-tighter">{{ $grandApproved }}</div>
                </div>
            </div>

            {{-- Total Filled Card --}}
            <div class="relative overflow-hidden bg-white dark:bg-[#151518] border border-slate-300 dark:border-slate-700/60 rounded-[1.25rem] p-6 shadow-sm">
                <!-- Background Icon -->
                <div class="absolute -right-4 -top-6 opacity-20 dark:opacity-10 text-purple-400 dark:text-purple-400 pointer-events-none transform -rotate-12">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-32 h-32">
                        <path fill-rule="evenodd" d="M8.25 6.75a3.75 3.75 0 117.5 0 3.75 3.75 0 01-7.5 0zM15.75 9.75a3 3 0 116 0 3 3 0 01-6 0zM2.25 9.75a3 3 0 116 0 3 3 0 01-6 0zM6.31 15.117A6.745 6.745 0 0112 12a6.745 6.745 0 016.709 7.498.75.75 0 01-.372.568A12.696 12.696 0 0112 21.75c-2.305 0-4.47-.612-6.337-1.684a.75.75 0 01-.372-.568 6.787 6.787 0 011.019-4.38z" clip-rule="evenodd" />
                        <path d="M5.082 14.254a8.287 8.287 0 00-1.308 5.135 9.687 9.687 0 01-1.764-.44l-.115-.04a.563.563 0 01-.373-.487l-.01-.121a3.75 3.75 0 016.576-3.036c.32.493.619 1.013.882 1.559a4.49 4.49 0 00-3.888-2.61zM18.918 14.254a8.287 8.287 0 011.308 5.135 9.687 9.687 0 001.764-.44l.115-.04a.563.563 0 00.373-.487l.01-.121a3.75 3.75 0 00-6.576-3.036c-.32.493-.619 1.013-.882 1.559a4.49 4.49 0 013.888-2.61z" />
                    </svg>
                </div>
                <!-- Content -->
                <div class="relative z-10 flex flex-col justify-center h-full">
                    <div class="text-[10px] font-bold text-slate-500 dark:text-slate-500 uppercase tracking-widest mb-1">{{ __('Total Filled') }}</div>
                    <div class="text-4xl font-black text-slate-600 dark:text-slate-300 tracking-tighter">{{ $grandFilled }}</div>
                </div>
            </div>

            {{-- Overall Gap Card --}}
            <div class="relative overflow-hidden bg-white dark:bg-[#151518] border border-slate-300 dark:border-slate-700/60 rounded-[1.25rem] p-6 shadow-sm">
                <!-- Background Icon -->
                <div class="absolute -right-2 -top-2 opacity-20 dark:opacity-[0.15] text-emerald-500 dark:text-emerald-500 pointer-events-none transform rotate-12">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-24 h-24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
                    </svg>
                </div>
                <!-- Content -->
                <div class="relative z-10 flex flex-col justify-center h-full">
                    <div class="text-[10px] font-bold text-slate-500 dark:text-slate-500 uppercase tracking-widest mb-1">{{ __('Overall Gap') }}</div>
                    <div class="text-4xl font-black tracking-tighter {{ $grandDiff < 0 ? 'text-red-500' : ($grandDiff > 0 ? 'text-blue-500' : 'text-slate-900 dark:text-white') }}">
                        {{ $grandDiff > 0 ? '+' . $grandDiff : $grandDiff }}
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- 2. MOBILE VIEW (Enhanced Card Layout) --}}
        <div class="md:hidden space-y-8">
            @forelse($groupedRows as $typeId => $items)
            @php $currentType = $typeLabels[$typeId] ?? ['label' => 'General', 'class' => 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700']; @endphp

            <div class="space-y-4">
                {{-- Category Sticky Header --}}
                <div class="sticky top-0 z-10 py-2 bg-slate-50/95 dark:bg-[#0B1120]/95 backdrop-blur-sm -mx-4 px-4 mb-2 border-b border-transparent dark:border-slate-700/50">
                    <div class="inline-flex px-3 py-1 rounded-lg border text-[10px] font-black uppercase tracking-widest shadow-sm {{ $currentType['class'] }}">
                        {{ $currentType['label'] }}
                    </div>
                </div>

                @foreach($items as $row)
                <div class="bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-3xl overflow-hidden shadow-sm active:scale-[0.98] transition-transform">
                    <div class="p-5 border-b border-slate-200 dark:border-slate-700/50 flex justify-between items-start gap-4">
                        <div class="flex-1">
                            <div class="text-base font-bold text-slate-900 dark:text-white leading-tight mb-1">{{ $row['subject_name'] }}</div>
                            <div class="text-[10px] text-slate-500 dark:text-slate-500 font-bold uppercase tracking-tight">{{ $row['medium_name'] }}</div>
                        </div>

                        @php
                        $statusClasses = [
                        'Balanced' => 'bg-green-100 dark:bg-green-500/10 text-green-700 dark:text-green-400 border-green-200 dark:border-green-500/20',
                        'Excess' => 'bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border-blue-200 dark:border-blue-500/20',
                        'Deficit' => 'bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-400 border-red-200 dark:border-red-500/20',
                        ][$row['status']] ?? 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-300 dark:border-slate-700';
                        @endphp
                        <span class="shrink-0 px-3 py-1 rounded-full border text-[10px] font-black uppercase {{ $statusClasses }}">
                            {{ $row['status'] }}
                        </span>
                    </div>

                    <div class="px-5 py-4 grid grid-cols-3 gap-4 bg-slate-50/30 dark:bg-slate-800/30 text-center">
                        <div class="space-y-1">
                            <dt class="text-[9px] uppercase font-bold text-slate-500 dark:text-slate-500 tracking-tighter">{{ __('Approved') }}</dt>
                            <dd class="text-base font-extrabold text-slate-700 dark:text-slate-300">{{ $row['approved_posts'] }}</dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-[9px] uppercase font-bold text-slate-500 dark:text-slate-500 tracking-tighter">{{ __('Filled') }}</dt>
                            <dd class="text-base font-extrabold text-slate-700 dark:text-slate-300">{{ $row['filled_posts'] }}</dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-[9px] uppercase font-bold text-slate-500 dark:text-slate-500 tracking-tighter">{{ __('Gap') }}</dt>
                            <dd class="text-base font-black {{ $row['diff'] < 0 ? 'text-red-600 dark:text-red-400' : ($row['diff'] > 0 ? 'text-blue-600 dark:text-blue-400' : 'text-slate-900 dark:text-white') }}">
                                {{ $row['diff'] > 0 ? '+' . $row['diff'] : $row['diff'] }}
                            </dd>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @empty
            <div class="text-center py-16 bg-white dark:bg-slate-900 rounded-3xl border-2 border-dashed border-slate-300 dark:border-slate-700 text-slate-500 dark:text-slate-500 text-sm">
                <div class="mb-2">📭</div>
                {{ __('No staff records found') }}
            </div>
            @endforelse
        </div>

        {{-- 3. WEB VIEW (Polished Table) --}}
        <div class="hidden md:block bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-3xl shadow-sm overflow-hidden mt-6">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50/80 dark:bg-slate-800/80 border-b border-slate-300 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-5 text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-500 border-r border-slate-200 dark:border-slate-700/50">Subject / Medium</th>
                        <th class="px-4 py-5 text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-500 text-right">Approved</th>
                        <th class="px-4 py-5 text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-500 text-right">Filled</th>
                        <th class="px-4 py-5 text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-500 text-right">Gap</th>
                        <th class="px-6 py-5 text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-500 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                    @forelse($groupedRows as $typeId => $items)
                    @php $currentType = $typeLabels[$typeId] ?? ['label' => 'Other', 'class' => 'bg-slate-50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300']; @endphp
                    <tr class="{{ $currentType['class'] }} border-y border-slate-200/50 dark:border-slate-700/50">
                        <td colspan="5" class="px-6 py-2.5">
                            <span class="text-[10px] font-black uppercase tracking-[0.25em]">{{ $currentType['label'] }}</span>
                        </td>
                    </tr>

                    @foreach($items as $row)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-indigo-700/50 transition-colors group">
                        <td class="px-6 py-4 border-r border-slate-200 dark:border-slate-700/50">
                            <div class="text-sm font-bold text-slate-900 dark:text-slate-100 leading-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $row['subject_name'] }}</div>
                            <div class="text-[10px] text-slate-500 dark:text-slate-500 font-bold uppercase mt-1 tracking-tight">{{ $row['medium_name'] }}</div>
                        </td>
                        <td class="px-4 py-4 text-right text-sm font-bold text-slate-600 dark:text-slate-300 tracking-tight">{{ $row['approved_posts'] }}</td>
                        <td class="px-4 py-4 text-right text-sm font-bold text-slate-600 dark:text-slate-300 tracking-tight">{{ $row['filled_posts'] }}</td>
                        <td class="px-4 py-4 text-right">
                            <span class="text-sm font-black {{ $row['diff'] < 0 ? 'text-red-600 dark:text-red-400' : ($row['diff'] > 0 ? 'text-blue-600 dark:text-blue-400' : 'text-slate-900 dark:text-white') }}">
                                {{ $row['diff'] > 0 ? '+' . $row['diff'] : $row['diff'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                            $statusBadge = [
                            'Balanced' => 'bg-green-100 dark:bg-green-500/10 text-green-700 dark:text-green-400 border-green-200 dark:border-green-500/20',
                            'Excess' => 'bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border-blue-200 dark:border-blue-500/20',
                            'Deficit' => 'bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-400 border-red-200 dark:border-red-500/20',
                            ][$row['status']] ?? 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-300 dark:border-slate-700';
                            @endphp
                            <span class="inline-block px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $statusBadge }} border">
                                {{ $row['status'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center text-slate-500 dark:text-slate-500 italic text-sm">
                            {{ __('No staff data available.') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-offices.pmoe.layout>
</section>