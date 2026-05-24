<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    {{-- Header Section --}}
    <x-page-header
        title="Institution Cadre Summary"
        subtitle="National Schools Jurisdiction"
        icon="table-cells"
        :breadcrumbs="[
            'Institutions' => '#',
            'National Schools' => route('national-school.overview'),
            'Cadre Summary' => '#'
        ]"
    >
        <x-slot:actions>
            <div class="flex items-center gap-3">
                <flux:button wire:click="downloadPdf" icon="printer" variant="primary" class="h-11 px-6 font-bold bg-indigo-600! hover:bg-indigo-700! border-none shadow-lg shadow-indigo-200">
                    {{ __('Download Report') }}
                </flux:button>
            </div>
        </x-slot:actions>
    </x-page-header>

    {{-- Circular Info & Filters Combined --}}
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6 items-start">
        {{-- Left: Filters --}}
        <div class="xl:col-span-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 p-6 bg-white dark:bg-zinc-900 border border-slate-300 dark:border-zinc-700 rounded-3xl shadow-sm">
            <flux:select wire:model.live="province" label="Province">
                <flux:select.option value="null">All Provinces</flux:select.option>
                @foreach ($provinceOption as $province)
                    <flux:select.option value="{{ $province->workplace_id }}">{{ $province->short_name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="zonal" label="Zonal Office" :disabled="empty($zonalOption)">
                <flux:select.option value="null">All Zonal</flux:select.option>
                @foreach ($zonalOption as $zonal)
                    <flux:select.option value="{{ $zonal->workplace_id }}">{{ $zonal->short_name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="division" label="Divisional Office" :disabled="empty($divisionOption)">
                <flux:select.option value="null">All Divisions</flux:select.option>
                @foreach ($divisionOption as $division)
                    <flux:select.option value="{{ $division->workplace_id }}">{{ $division->short_name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="institution" label="Institution" :disabled="empty($institutionOption)">
                <flux:select.option value="null">All Institutions</flux:select.option>
                @foreach ($institutionOption as $inst)
                    <flux:select.option value="{{ $inst->workplace_id }}">{{ $inst->name }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        {{-- Right: Circular Stats --}}
        <div class="p-6 bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/50 rounded-3xl space-y-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-600 rounded-lg text-white">
                    <flux:icon name="document-text" variant="mini" />
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-indigo-600/60 dark:text-indigo-400/60">{{ __('Active Circular') }}</p>
                    <p class="text-sm font-bold text-slate-900 dark:text-zinc-100">{{ $circular->circular_no ?? __('No Active Circular') }}</p>
                </div>
            </div>
            <div class="pt-4 border-t border-indigo-100 dark:border-indigo-900/30">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1">{{ __('Issued Date') }}</p>
                <p class="text-sm font-bold text-slate-700 dark:text-zinc-300">{{ $circular->issued_date ?? 'N/A' }}</p>
            </div>
        </div>
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

    {{-- Grand Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
        <div class="bg-white dark:bg-zinc-900 border border-slate-300 dark:border-zinc-700 rounded-[2.5rem] p-8 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 text-slate-100 dark:text-zinc-800 transition-colors group-hover:text-indigo-500/10">
                <flux:icon name="table-cells" variant="solid" class="w-32 h-32" />
            </div>
            <p class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-500 mb-2 relative z-10">{{ __('Total Approved') }}</p>
            <p class="text-5xl font-black text-slate-900 dark:text-white relative z-10 tracking-tighter">{{ number_format($grandApproved) }}</p>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-slate-300 dark:border-zinc-700 rounded-[2.5rem] p-8 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 text-slate-100 dark:text-zinc-800 transition-colors group-hover:text-blue-500/10">
                <flux:icon name="users" variant="solid" class="w-32 h-32" />
            </div>
            <p class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-500 mb-2 relative z-10">{{ __('Total Filled') }}</p>
            <p class="text-5xl font-black text-slate-600 dark:text-zinc-400 relative z-10 tracking-tighter">{{ number_format($grandFilled) }}</p>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-slate-300 dark:border-zinc-700 rounded-[2.5rem] p-8 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 {{ $grandDiff < 0 ? 'text-red-500/5' : 'text-emerald-500/5' }} transition-colors">
                <flux:icon name="arrows-up-down" variant="solid" class="w-32 h-32" />
            </div>
            <p class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-500 mb-2 relative z-10">{{ __('Overall Gap') }}</p>
            <p class="text-5xl font-black {{ $grandDiff < 0 ? 'text-red-600 dark:text-red-400' : ($grandDiff > 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-900 dark:text-white') }} relative z-10 tracking-tighter">
                {{ $grandDiff > 0 ? '+' . number_format($grandDiff) : number_format($grandDiff) }}
            </p>
        </div>
    </div>

    {{-- Data Display --}}
    <div class="bg-white dark:bg-zinc-900 border border-slate-300 dark:border-zinc-700 rounded-[2.5rem] overflow-hidden shadow-sm">
        {{-- Mobile View --}}
        <div class="md:hidden divide-y divide-slate-100 dark:divide-zinc-800">
            @forelse($groupedRows as $typeId => $items)
                @php $currentType = $typeLabels[$typeId] ?? ['label' => 'General', 'class' => 'bg-slate-50 dark:bg-zinc-800 text-slate-700 dark:text-zinc-300']; @endphp
                <div class="px-6 py-3 {{ $currentType['class'] }} border-b">
                    <span class="text-[10px] font-black uppercase tracking-widest">{{ $currentType['label'] }}</span>
                </div>
                @foreach($items as $row)
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-base font-bold text-slate-900 dark:text-white leading-tight">{{ $row['subject_name'] }}</p>
                                <p class="text-[10px] text-slate-500 dark:text-zinc-400 font-bold uppercase tracking-tight">{{ $row['medium_name'] }}</p>
                            </div>
                            @php
                                $statusClasses = [
                                    'Balanced' => 'bg-green-100 text-green-700 border-green-200',
                                    'Excess' => 'bg-blue-100 text-blue-700 border-blue-200',
                                    'Deficit' => 'bg-red-100 text-red-700 border-red-200',
                                ][$row['status']] ?? 'bg-slate-100 text-slate-700 border-slate-300';
                            @endphp
                            <span class="px-3 py-1 rounded-full border text-[10px] font-black uppercase {{ $statusClasses }}">
                                {{ $row['status'] }}
                            </span>
                        </div>
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div>
                                <p class="text-[9px] uppercase font-bold text-slate-500">{{ __('Approved') }}</p>
                                <p class="text-sm font-extrabold">{{ $row['approved_posts'] }}</p>
                            </div>
                            <div>
                                <p class="text-[9px] uppercase font-bold text-slate-500">{{ __('Filled') }}</p>
                                <p class="text-sm font-extrabold">{{ $row['filled_posts'] }}</p>
                            </div>
                            <div>
                                <p class="text-[9px] uppercase font-bold text-slate-500">{{ __('Gap') }}</p>
                                <p class="text-sm font-black {{ $row['diff'] < 0 ? 'text-red-600' : ($row['diff'] > 0 ? 'text-blue-600' : '') }}">
                                    {{ $row['diff'] > 0 ? '+' . $row['diff'] : $row['diff'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            @empty
                <div class="p-20 text-center text-slate-500">
                    <flux:icon name="document-magnifying-glass" size="lg" class="mx-auto mb-4 opacity-20" />
                    <p class="font-medium italic">{{ __('No staff records found matching your filters.') }}</p>
                </div>
            @endforelse
        </div>

        {{-- Desktop View --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50/50 dark:bg-zinc-950/50 border-b border-slate-300 dark:border-zinc-700">
                    <tr>
                        <th class="px-8 py-5 text-[11px] font-black uppercase tracking-widest text-slate-500">{{ __('Subject / Medium') }}</th>
                        <th class="px-6 py-5 text-[11px] font-black uppercase tracking-widest text-slate-500 text-right">{{ __('Approved') }}</th>
                        <th class="px-6 py-5 text-[11px] font-black uppercase tracking-widest text-slate-500 text-right">{{ __('Filled') }}</th>
                        <th class="px-6 py-5 text-[11px] font-black uppercase tracking-widest text-slate-500 text-right">{{ __('Gap') }}</th>
                        <th class="px-8 py-5 text-[11px] font-black uppercase tracking-widest text-slate-500 text-center">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
                    @forelse($groupedRows as $typeId => $items)
                        @php $currentType = $typeLabels[$typeId] ?? ['label' => 'Other', 'class' => 'bg-slate-50/50 dark:bg-zinc-800/30 text-slate-500']; @endphp
                        <tr class="{{ $currentType['class'] }}">
                            <td colspan="5" class="px-8 py-3">
                                <span class="text-[10px] font-black uppercase tracking-[0.25em]">{{ $currentType['label'] }}</span>
                            </td>
                        </tr>
                        @foreach($items as $row)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/50 transition-colors group">
                                <td class="px-8 py-5">
                                    <p class="text-sm font-bold text-slate-900 dark:text-white transition-colors group-hover:text-indigo-600">{{ $row['subject_name'] }}</p>
                                    <p class="text-[10px] text-slate-500 dark:text-zinc-400 font-bold uppercase mt-1 tracking-tight">{{ $row['medium_name'] }}</p>
                                </td>
                                <td class="px-6 py-5 text-right text-sm font-bold text-slate-600 dark:text-zinc-400">{{ number_format($row['approved_posts']) }}</td>
                                <td class="px-6 py-5 text-right text-sm font-bold text-slate-600 dark:text-zinc-400">{{ number_format($row['filled_posts']) }}</td>
                                <td class="px-6 py-5 text-right">
                                    <span class="text-sm font-black {{ $row['diff'] < 0 ? 'text-red-600 dark:text-red-400' : ($row['diff'] > 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-900 dark:text-zinc-100') }}">
                                        {{ $row['diff'] > 0 ? '+' . $row['diff'] : $row['diff'] }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    @php
                                        $statusBadge = [
                                            'Balanced' => 'bg-green-100 dark:bg-green-500/10 text-green-700 dark:text-green-400 border-green-200 dark:border-green-500/20',
                                            'Excess' => 'bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border-blue-200 dark:border-blue-500/20',
                                            'Deficit' => 'bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-400 border-red-200 dark:border-red-500/20',
                                        ][$row['status']] ?? 'bg-slate-100 dark:bg-zinc-800 text-slate-700 dark:text-zinc-300 border-slate-300 dark:border-zinc-700';
                                    @endphp
                                    <span class="inline-block px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border {{ $statusBadge }}">
                                        {{ $row['status'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center">
                                <flux:icon name="document-magnifying-glass" size="lg" class="mx-auto mb-4 opacity-20" />
                                <p class="text-slate-500 font-medium italic">{{ __('No staff data available.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($rows)
                    <tfoot class="bg-indigo-600 dark:bg-black text-white">
                        <tr>
                            <td class="px-8 py-8 text-xs font-black uppercase tracking-[0.3em] text-slate-500">{{ __('Grand Summary') }}</td>
                            <td class="px-6 py-8 text-right text-2xl font-black tracking-tighter">{{ number_format($grandApproved) }}</td>
                            <td class="px-6 py-8 text-right text-2xl font-black text-slate-500 tracking-tighter">{{ number_format($grandFilled) }}</td>
                            <td class="px-6 py-8 text-right text-2xl font-black tracking-tighter {{ $grandDiff < 0 ? 'text-red-400' : ($grandDiff > 0 ? 'text-indigo-400' : 'text-white') }}">
                                {{ $grandDiff > 0 ? '+' . number_format($grandDiff) : number_format($grandDiff) }}
                            </td>
                            <td class="px-8 py-8 text-center">
                                <span class="px-3 py-1.5 rounded-lg bg-white/10 text-[10px] font-black uppercase tracking-widest border border-white/10">{{ __('System Finalized') }}</span>
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>