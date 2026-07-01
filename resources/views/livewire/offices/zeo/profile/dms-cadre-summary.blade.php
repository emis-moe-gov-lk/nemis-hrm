<section class="w-full">

    <x-offices.zeo.zeo-layout :officeId="$officeId">
        {{-- Header Section --}}
        <div class="relative mb-8 w-full">
            <flux:heading size="xl" level="1" class="mb-1 text-slate-900 dark:text-white">{{ __('Institution Cadre Summary for ZEO') }}</flux:heading>
            <flux:subheading size="lg" class="mb-6 text-slate-500 dark:text-slate-500">{{ __('List of Cadre Summary under the selected ZEO.') }}</flux:subheading>
            <flux:separator variant="subtle" />
        </div>

        @if($circular)
        <div class="flex justify-end w-full mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 px-4 py-3 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-2xl shadow-sm w-full sm:w-fit">
                <flux:button
                    wire:click="downloadPdf"
                    wire:loading.attr="disabled"
                    wire:target="downloadPdf"
                    icon="printer"
                    variant="primary"
                    class="h-10 justify-center bg-blue-600! hover:bg-blue-700! text-white! border-none shadow-sm">
                    <span wire:loading.remove wire:target="downloadPdf">{{ __('Download PDF') }}</span>
                    <span wire:loading wire:target="downloadPdf">{{ __('Preparing...') }}</span>
                </flux:button>

                <flux:separator vertical class="hidden sm:block h-8" />

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
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 items-center w-full mb-8">
            <flux:select wire:model.live="authority" class="w-full">
                <flux:select.option value="null">All authorities</flux:select.option>
                @foreach ($authorityOption as $authority)
                <flux:select.option value="{{ $authority->authority_id }}">
                    {{ $authority->authority_name }}
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
        1 => ['label' => 'Teacher / Subjects', 'class' => 'bg-blue-50 text-blue-700 border-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800/30'],
        2 => ['label' => 'Principal / Designation', 'class' => 'bg-purple-50 text-purple-700 border-purple-100 dark:bg-purple-900/20 dark:text-purple-400 dark:border-purple-800/30'],
        3 => ['label' => 'Other', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800/30'],
        ];

        $groupedRows = collect($rows)->groupBy('subject_type');
        $grandApproved = collect($rows)->sum('approved_posts');
        $grandFilled = collect($rows)->sum('filled_posts');
        $grandDiff = collect($rows)->sum('diff');
        @endphp

        @if($rows)
        <div class="mb-8 grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">{{ __('Approved') }}</span>
                    <span class="rounded-full bg-blue-50 px-2 py-1 text-[10px] font-black text-blue-700 dark:bg-blue-500/10 dark:text-blue-400">{{ __('Cadre') }}</span>
                </div>
                <div class="mt-2 text-3xl font-black tracking-tight text-slate-900 dark:text-white">{{ number_format($grandApproved) }}</div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">{{ __('Filled') }}</span>
                    <span class="rounded-full bg-emerald-50 px-2 py-1 text-[10px] font-black text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">{{ __('Staff') }}</span>
                </div>
                <div class="mt-2 text-3xl font-black tracking-tight text-slate-900 dark:text-white">{{ number_format($grandFilled) }}</div>
            </div>

            <div class="rounded-2xl border {{ $grandDiff < 0 ? 'border-red-200 bg-red-50/60 dark:border-red-500/20 dark:bg-red-500/10' : ($grandDiff > 0 ? 'border-blue-200 bg-blue-50/60 dark:border-blue-500/20 dark:bg-blue-500/10' : 'border-emerald-200 bg-emerald-50/60 dark:border-emerald-500/20 dark:bg-emerald-500/10') }} p-4 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-[10px] font-black uppercase tracking-widest {{ $grandDiff < 0 ? 'text-red-600 dark:text-red-400' : ($grandDiff > 0 ? 'text-blue-600 dark:text-blue-400' : 'text-emerald-600 dark:text-emerald-400') }}">{{ __('Gap') }}</span>
                    <span class="rounded-full bg-white px-2 py-1 text-[10px] font-black dark:bg-slate-900 {{ $grandDiff < 0 ? 'text-red-700 dark:text-red-400' : ($grandDiff > 0 ? 'text-blue-700 dark:text-blue-400' : 'text-emerald-700 dark:text-emerald-400') }}">
                        {{ $grandDiff < 0 ? __('Deficit') : ($grandDiff > 0 ? __('Excess') : __('Balanced')) }}
                    </span>
                </div>
                <div class="mt-2 text-3xl font-black tracking-tight {{ $grandDiff < 0 ? 'text-red-700 dark:text-red-400' : ($grandDiff > 0 ? 'text-blue-700 dark:text-blue-400' : 'text-emerald-700 dark:text-emerald-400') }}">
                    {{ $grandDiff > 0 ? '+' . number_format($grandDiff) : number_format($grandDiff) }}
                </div>
            </div>
        </div>
        @endif

        {{-- 2. MOBILE VIEW (Enhanced Card Layout) --}}
        <div class="md:hidden space-y-8">
            @forelse($groupedRows as $typeId => $items)
            @php $currentType = $typeLabels[$typeId] ?? ['label' => 'General', 'class' => 'bg-slate-50 text-slate-700 border-slate-200 dark:bg-slate-800/50 dark:text-slate-300 dark:border-slate-700']; @endphp

            <div class="space-y-4">
                {{-- Category Sticky Header --}}
                <div class="sticky top-0 z-10 py-2 bg-slate-50/95 dark:bg-slate-900/95 backdrop-blur-sm -mx-4 px-4 mb-2">
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
                        'Balanced' => 'bg-green-100 text-green-700 border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800/30',
                        'Excess' => 'bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800/30',
                        'Deficit' => 'bg-red-100 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800/30',
                        ][$row['status']] ?? 'bg-slate-100 text-slate-700 border-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700';
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
                <div class="mb-2 opacity-50 dark:opacity-40 text-4xl">📭</div>
                {{ __('No staff records found') }}
            </div>
            @endforelse

            {{-- MOBILE GRAND TOTAL --}}
            @if($rows)
            <div class="p-5 bg-white rounded-3xl text-slate-900 shadow-sm border border-slate-200 mt-8 dark:bg-slate-900 dark:text-white dark:border-slate-700">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">{{ __('Grand Summary') }}</span>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[9px] font-black uppercase text-slate-600 dark:bg-slate-800 dark:text-slate-400">{{ __('Finalized') }}</span>
                </div>
                <div class="grid grid-cols-3 gap-6 text-center relative z-10">
                    <div>
                        <div class="text-[10px] font-bold text-slate-500 dark:text-slate-500 uppercase mb-1">{{ __('Approved') }}</div>
                        <div class="text-2xl font-black tracking-tighter text-slate-900 dark:text-white">{{ number_format($grandApproved) }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-slate-500 dark:text-slate-500 uppercase mb-1">{{ __('Filled') }}</div>
                        <div class="text-2xl font-black text-slate-700 dark:text-slate-300 tracking-tighter">{{ number_format($grandFilled) }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-slate-500 dark:text-slate-500 uppercase mb-1">{{ __('Gap') }}</div>
                        <div class="text-2xl font-black tracking-tighter {{ $grandDiff < 0 ? 'text-red-600 dark:text-red-400' : ($grandDiff > 0 ? 'text-blue-600 dark:text-blue-400' : 'text-emerald-600 dark:text-emerald-400') }}">
                            {{ $grandDiff > 0 ? '+' . number_format($grandDiff) : number_format($grandDiff) }}
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- 3. WEB VIEW (Polished Table) --}}
        <div class="hidden md:block bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-3xl shadow-sm overflow-hidden mt-6">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50/80 dark:bg-slate-800/50 border-b border-slate-300 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-5 text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-500 border-r border-slate-200 dark:border-slate-700/50">Subject / Medium</th>
                        <th class="px-4 py-5 text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-500 text-right">Approved</th>
                        <th class="px-4 py-5 text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-500 text-right">Filled</th>
                        <th class="px-4 py-5 text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-500 text-right">Gap</th>
                        <th class="px-6 py-5 text-[11px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-500 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($groupedRows as $typeId => $items)
                    @php $currentType = $typeLabels[$typeId] ?? ['label' => 'Other', 'class' => 'bg-slate-50 text-slate-700 dark:bg-slate-800/30 dark:text-slate-300']; @endphp
                    <tr class="{{ $currentType['class'] }} border-y border-slate-200/50 dark:border-slate-700/30">
                        <td colspan="5" class="px-6 py-2.5">
                            <span class="text-[10px] font-black uppercase tracking-[0.25em]">{{ $currentType['label'] }}</span>
                        </td>
                    </tr>

                    @foreach($items as $row)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-indigo-700/30 transition-colors group">
                        <td class="px-6 py-4 border-r border-slate-200 dark:border-slate-700/50">
                            <div class="text-sm font-bold text-slate-900 dark:text-white leading-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $row['subject_name'] }}</div>
                            <div class="text-[10px] text-slate-500 dark:text-slate-500 font-bold uppercase mt-1 tracking-tight">{{ $row['medium_name'] }}</div>
                        </td>
                        <td class="px-4 py-4 text-right text-sm font-bold text-slate-600 dark:text-slate-500 tracking-tight">{{ $row['approved_posts'] }}</td>
                        <td class="px-4 py-4 text-right text-sm font-bold text-slate-600 dark:text-slate-500 tracking-tight">{{ $row['filled_posts'] }}</td>
                        <td class="px-4 py-4 text-right">
                            <span class="text-sm font-black {{ $row['diff'] < 0 ? 'text-red-600 dark:text-red-400' : ($row['diff'] > 0 ? 'text-blue-600 dark:text-blue-400' : 'text-slate-900 dark:text-white') }}">
                                {{ $row['diff'] > 0 ? '+' . $row['diff'] : $row['diff'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                            $statusBadge = [
                            'Balanced' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                            'Excess' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                            'Deficit' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                            ][$row['status']] ?? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300';
                            @endphp
                            <span class="inline-block px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $statusBadge }}">
                                {{ $row['status'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center text-slate-500 dark:text-slate-500 italic text-sm">
                            <div class="mb-2 opacity-50 dark:opacity-40 text-4xl">📭</div>
                            {{ __('No staff data available.') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>

                @if($rows)
                <tfoot class="bg-slate-50 border-t border-slate-200 dark:bg-slate-800/50 dark:border-slate-700">
                    <tr>
                        <td class="px-6 py-5">
                            <div class="text-xs font-black uppercase tracking-[0.2em] text-slate-600 dark:text-slate-300">{{ __('Grand Summary') }}</div>
                            <div class="mt-1 text-[10px] font-bold uppercase text-slate-400">{{ __('All selected institutions') }}</div>
                        </td>
                        <td class="px-4 py-5 text-right text-xl font-black tracking-tight text-slate-900 dark:text-white">
                            {{ number_format($grandApproved) }}
                        </td>
                        <td class="px-4 py-5 text-right text-xl font-black text-slate-700 dark:text-slate-300 tracking-tight">
                            {{ number_format($grandFilled) }}
                        </td>
                        <td class="px-4 py-5 text-right text-xl font-black tracking-tight {{ $grandDiff < 0 ? 'text-red-600 dark:text-red-400' : ($grandDiff > 0 ? 'text-blue-600 dark:text-blue-400' : 'text-emerald-600 dark:text-emerald-400') }}">
                            {{ $grandDiff > 0 ? '+' . number_format($grandDiff) : number_format($grandDiff) }}
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="inline-flex items-center rounded-full bg-white px-3 py-1.5 text-[10px] font-black uppercase tracking-wide text-slate-600 ring-1 ring-slate-200 dark:bg-slate-900 dark:text-slate-400 dark:ring-slate-700">
                                {{ __('Finalized') }}
                            </span>
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </x-offices.zeo.zeo-layout>
</section>
