<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    {{-- Header Section --}}
    <x-page-header
        title="DMS Approved Cadre"
        subtitle="Review approved staff positions and institutional cadre allocation."
        icon="briefcase"
    >
        <x-slot:actions>
            @if ($activeCircular)
                <flux:button
                    wire:click="downloadPdf"
                    icon="printer"
                    variant="primary"
                    class="h-11 bg-emerald-600! hover:bg-emerald-700! text-white! shadow-lg shadow-emerald-200 border-none">
                    {{ __('Download PDF') }}
                </flux:button>

                @can('cadre-dms-approved.add')
                    <flux:button
                        href="{{ route('cadre-dms-approved.add', [$id, $activeCircular->id]) }}"
                        icon="pencil-square"
                        variant="primary"
                        class="h-11 bg-indigo-600! hover:bg-indigo-700! text-white! shadow-lg shadow-indigo-200 border-none">
                        {{ __('Add or Edit Approved Cadre') }}
                    </flux:button>
                @endcan
            @endif
        </x-slot:actions>
    </x-page-header>

    {{-- Metadata Cards --}}
    <div class="flex flex-col sm:flex-row gap-4">
        {{-- Workplace Card --}}
        <div class="flex-1 flex items-center gap-4 p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl shadow-sm transition-all hover:border-blue-300 dark:hover:border-blue-700/50 hover:shadow-md group">
            <div class="shrink-0 w-12 h-12 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/30 rounded-xl flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform duration-300">
                <flux:icon.briefcase variant="mini" class="text-blue-600 dark:text-blue-400 w-6 h-6" />
            </div>
            <div class="overflow-hidden">
                <span class="block text-[10px] font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-400 leading-none mb-1.5">
                    {{ __('Workplace Information') }}
                </span>
                <div class="flex flex-col">
                    <span class="text-sm font-bold text-zinc-900 dark:text-zinc-100 leading-tight truncate">
                        {{ $workplace->name }}
                    </span>
                    <span class="text-[11px] font-mono text-blue-600 dark:text-blue-400 font-bold mt-0.5">
                        {{ __('Census:') }} {{ $workplace->census_no }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Circular Card --}}
        @if ($activeCircular)
        <div class="flex-1 flex items-center gap-4 p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl shadow-sm transition-all hover:border-emerald-300 dark:hover:border-emerald-700/50 hover:shadow-md group">
            <div class="shrink-0 w-12 h-12 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/30 rounded-xl flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform duration-300">
                <flux:icon.document-text variant="mini" class="text-emerald-600 dark:text-emerald-400 w-6 h-6" />
            </div>
            <div class="overflow-hidden">
                <span class="block text-[10px] font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-400 leading-none mb-1.5">
                    {{ __('Circular Information') }}
                </span>
                <div class="flex flex-col">
                    <span class="text-sm font-bold text-zinc-900 dark:text-zinc-100 leading-tight">
                        {{ $activeCircular->circular_no }}
                    </span>
                    <span class="text-[11px] text-zinc-400 dark:text-zinc-400 mt-0.5">
                        {{ __('Issued Date:') }} <span class="text-zinc-700 dark:text-zinc-300 font-bold">{{ $activeCircular->issued_date }}</span>
                    </span>
                </div>
            </div>
        </div>
        @else
        <div class="flex-1 flex items-center gap-4 p-4 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 rounded-2xl shadow-sm">
            <div class="shrink-0 w-12 h-12 bg-amber-100 dark:bg-amber-500/20 rounded-xl flex items-center justify-center">
                <flux:icon.exclamation-triangle class="text-amber-700 dark:text-amber-200 w-6 h-6" />
            </div>
            <div>
                <span class="block text-[10px] font-black uppercase tracking-widest text-amber-700 dark:text-amber-100 leading-none mb-1.5">
                    {{ __('No Active DMS Circular') }}
                </span>
                <p class="text-xs text-amber-800 dark:text-amber-100/80">
                    {{ __('Create or activate a cadre circular before adding approved cadre.') }}
                </p>
            </div>
        </div>
        @endif
    </div>

    {{-- PHP Logic --}}
    @php
    $mediumSums = [];
    $grandTotal = 0;
    foreach ($mediums as $m) { $mediumSums[$m->medium_id] = 0; }
    foreach ($approvedCadreList as $row) {
    foreach ($mediums as $m) {
    $mediumSums[$m->medium_id] += $row['medium_totals'][$m->medium_id] ?? 0;
    }
    $grandTotal += $row['total'];
    }
    $grouped = $approvedCadreList->groupBy(fn($row) => $row['subject']->type ?? 0);
    $typeLabels = [
    1 => ['label' => 'Teacher / Subjects', 'color' => 'bg-blue-600 dark:bg-blue-500', 'light' => 'bg-blue-50 dark:bg-blue-900/10', 'text' => 'text-blue-600 dark:text-blue-400'],
    2 => ['label' => 'Principal / Designation', 'color' => 'bg-purple-600 dark:bg-purple-500', 'light' => 'bg-purple-50 dark:bg-purple-900/10', 'text' => 'text-purple-600 dark:text-purple-400'],
    3 => ['label' => 'Other', 'color' => 'bg-emerald-600 dark:bg-emerald-500', 'light' => 'bg-emerald-50 dark:bg-emerald-900/10', 'text' => 'text-emerald-600 dark:text-emerald-400'],
    ];
    @endphp

    {{-- 1. MOBILE VIEW --}}
    <div class="md:hidden space-y-8">
        @forelse($grouped as $type => $rows)
        <div class="space-y-3">
            <div class="flex items-center gap-2 px-1">
                <div class="h-1 w-4 rounded-full {{ $typeLabels[$type]['color'] ?? 'bg-zinc-400 dark:bg-zinc-600' }}"></div>
                <h3 class="text-[10px] font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-400">
                    {{ $typeLabels[$type]['label'] ?? 'Other' }}
                </h3>
            </div>

            @foreach ($rows as $row)
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl overflow-hidden shadow-sm">
                <div class="p-4 border-b border-zinc-50 dark:border-zinc-700/80">
                    <div class="text-sm font-bold text-zinc-900 dark:text-zinc-100 leading-tight">
                        {{ $row['subject']->name_en ?? $row['subject']->subject_name }}
                    </div>
                </div>
                <div class="p-4 grid grid-cols-2 gap-y-4 gap-x-2">
                    @foreach ($mediums as $medium)
                    <div>
                        <dt class="text-[9px] uppercase font-bold text-zinc-400 dark:text-zinc-400 tracking-tighter">{{ $medium->name }}</dt>
                        <dd class="text-sm font-bold text-zinc-700 dark:text-zinc-300">{{ $row['medium_totals'][$medium->medium_id] ?? 0 }}</dd>
                    </div>
                    @endforeach
                </div>
                <div class="px-4 py-3 bg-zinc-50 dark:bg-zinc-800/50 flex justify-between items-center border-t border-zinc-100 dark:border-zinc-700">
                    <span class="text-[10px] font-black text-zinc-400 dark:text-zinc-400 uppercase tracking-widest">{{ __('Total') }}</span>
                    <span class="text-base font-black text-zinc-900 dark:text-white">{{ $row['total'] }}</span>
                </div>
            </div>
            @endforeach
        </div>
        @empty
        <div class="text-center py-10 bg-white dark:bg-zinc-900/50 rounded-2xl border border-dashed border-zinc-300 dark:border-zinc-700 text-zinc-400 dark:text-zinc-400 text-sm">
            {{ __('No data available') }}
        </div>
        @endforelse

        {{-- MOBILE GRAND TOTAL CARD --}}
        @if ($grandTotal > 0)
        <div class="bg-zinc-900 dark:bg-black rounded-3xl p-6 text-white shadow-2xl ring-4 ring-zinc-900/10 dark:ring-white/5 relative overflow-hidden">
            <!-- Decorative gradient blob -->
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-blue-500/20 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-emerald-500/20 rounded-full blur-3xl"></div>

            <div class="relative z-10 flex items-center gap-2 mb-6">
                <div class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></div>
                <h4 class="text-[10px] font-black uppercase text-zinc-400 dark:text-zinc-400 tracking-[0.2em]">{{ __('Grand Summary') }}</h4>
            </div>

            <div class="relative z-10 grid grid-cols-2 gap-y-6 gap-x-4 mb-6">
                @foreach ($mediums as $medium)
                <div class="border-l-2 border-zinc-700 dark:border-zinc-700 pl-3">
                    <div class="text-[10px] uppercase text-zinc-400 dark:text-zinc-400 font-bold mb-1">{{ $medium->name }}</div>
                    <div class="text-xl font-bold tracking-tight">{{ $mediumSums[$medium->medium_id] }}</div>
                </div>
                @endforeach
            </div>

            <div class="relative z-10 pt-5 border-t border-zinc-800 dark:border-zinc-700/80 flex justify-between items-end">
                <div>
                    <span class="block text-[10px] font-black uppercase text-blue-400 dark:text-blue-500 mb-1 tracking-widest">{{ __('Total Cadre') }}</span>
                    <span class="text-4xl font-black leading-none tracking-tighter">{{ $grandTotal }}</span>
                </div>
                <flux:icon.chart-bar class="text-zinc-700 dark:text-zinc-800 w-10 h-10" />
            </div>
        </div>
        @endif
    </div>

    {{-- 2. WEB VIEW --}}
    <div class="hidden md:block bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-zinc-50/80 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-700">
                <tr>
                    <th class="px-5 py-4 text-[11px] font-black uppercase text-zinc-400 dark:text-zinc-400 border-r border-zinc-200 dark:border-zinc-700 tracking-widest">
                        {{ __('Subject Details') }}
                    </th>
                    @foreach ($mediums as $medium)
                    <th class="px-3 py-4 text-[10px] font-black uppercase text-center border-r border-zinc-100 dark:border-zinc-700 text-zinc-400 dark:text-zinc-400 last:border-r-0">
                        {{ $medium->name }}
                    </th>
                    @endforeach
                    <th class="px-5 py-4 text-[11px] font-black uppercase text-center bg-zinc-100/30 dark:bg-zinc-800/30 text-zinc-400 dark:text-zinc-400 tracking-widest">
                        {{ __('Total') }}
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                @forelse($grouped as $type => $rows)
                <tr class="{{ $typeLabels[$type]['light'] ?? 'bg-zinc-50 dark:bg-zinc-800/30' }}">
                    <td colspan="{{ count($mediums) + 3 }}" class="px-5 py-2.5 border-y border-zinc-200/50 dark:border-zinc-700/80 shadow-[inset_0_1px_0_rgba(255,255,255,0.1)]">
                        <span class="text-[10px] font-black uppercase tracking-[0.15em] {{ $typeLabels[$type]['text'] ?? 'text-zinc-600 dark:text-zinc-400' }}">
                            {{ $typeLabels[$type]['label'] ?? 'General' }}
                        </span>
                    </td>
                </tr>
                @foreach ($rows as $row)
                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors group">
                    <td class="px-5 py-4 border-r border-zinc-50 dark:border-zinc-700/50">
                        <div class="text-sm font-bold text-zinc-900 dark:text-zinc-100 leading-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $row['subject']->name_en ?? $row['subject']->subject_name }}</div>
                        <div class="text-[10px] text-zinc-400 dark:text-zinc-400 font-mono mt-1 uppercase tracking-tighter bg-zinc-100 dark:bg-zinc-800 inline-block px-1.5 py-0.5 rounded border border-zinc-200 dark:border-zinc-700">ID: {{ $row['subject']->subject_id }}</div>
                    </td>
                    @foreach ($mediums as $medium)
                    <td class="px-3 py-4 text-center text-sm font-semibold text-zinc-600 dark:text-zinc-300 border-r border-zinc-50 dark:border-zinc-700/50 last:border-r-0">
                        {{ $row['medium_totals'][$medium->medium_id] ?? 0 }}
                    </td>
                    @endforeach
                    <td class="px-5 py-4 text-center font-black text-zinc-900 dark:text-white bg-zinc-50/30 dark:bg-zinc-800/30">
                        {{ $row['total'] }}
                    </td>
                </tr>
                @endforeach
                @empty
                <tr>
                    <td colspan="{{ count($mediums) + 2 }}" class="px-5 py-10 text-center text-zinc-400 dark:text-zinc-400 text-sm bg-white dark:bg-zinc-900">
                        {{ __('No data available') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if ($grandTotal > 0)
            <tfoot class="bg-zinc-900 dark:bg-black text-white border-t-4 border-white dark:border-zinc-700 relative z-10 box-border border-b border-x border-r border-l">
                <tr class="font-bold relative">
                    <td class="px-5 py-5 text-xs font-black uppercase tracking-widest relative z-20">{{ __('Grand Total') }}</td>
                    @foreach ($mediums as $medium)
                    <td class="px-3 py-5 text-center text-sm border-r border-zinc-800 dark:border-zinc-900 last:border-r-0 font-bold relative z-20">
                        {{ $mediumSums[$medium->medium_id] }}
                    </td>
                    @endforeach
                    <td class="px-0 py-0 text-center relative pointer-events-none align-middle w-24">
                        <div class="absolute inset-0 bg-blue-600 dark:bg-blue-600/80 z-10 w-full h-full border-t flex items-center justify-center border-t-blue-500 border-x border-blue-600 dark:border-x-blue-600/80">
                            <span class="text-xl font-black leading-none text-white w-full h-full flex items-center justify-center">{{ $grandTotal }}</span>
                        </div>
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
