<section class="w-full">
    {{-- Header Section --}}
    <div class="relative mb-8 w-full">
        <flux:heading size="xl" level="1" class="text-2xl! font-black tracking-tight text-zinc-800 dark:text-zinc-100 mb-1">{{ __('Institution Cadre Summary for National School') }}</flux:heading>
        <flux:subheading size="lg" class="text-zinc-500 dark:text-zinc-400 mb-6">{{ __('List of Cadre Summary under the selected National School.') }}</flux:subheading>
        <flux:separator variant="subtle" class="dark:bg-zinc-800" />
    </div>

    {{-- Circular Info Box --}}
    <div class="flex justify-end w-full mb-6">
        <div class="flex items-center gap-3 px-4 py-3 bg-zinc-50/50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-sm w-full sm:w-fit">
            <div class="flex-1 sm:flex-none text-right">
                <span class="text-[9px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block mb-0.5">{{ __('Circular') }}</span>
                <span class="text-sm font-extrabold text-blue-600 dark:text-blue-500 tracking-tight">{{ $circular->circular_no }}</span>
            </div>

            <flux:separator vertical class="h-8 dark:bg-zinc-800" />

            <div class="flex-1 sm:flex-none text-right">
                <span class="text-[9px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block mb-0.5">{{ __('Date') }}</span>
                <span class="text-sm font-bold text-zinc-700 dark:text-zinc-300">{{ $circular->issued_date }}</span>
            </div>

            <flux:separator vertical class="h-8 dark:bg-zinc-800" />

            <div class="flex items-center">
                <flux:button wire:click="downloadPdf" icon="printer" variant="subtle" size="sm" class="text-zinc-500 hover:text-blue-600 dark:text-zinc-400 dark:hover:text-blue-400">
                    {{ __('Download PDF') }}
                </flux:button>
            </div>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-2 items-center w-full mb-8 bg-zinc-50/50 dark:bg-zinc-900/50 p-4 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm">

        <flux:select wire:model.live="province" class="w-full">
            <flux:select.option value="null">All provinces</flux:select.option>
            @foreach ($provinceOption as $province)
            <flux:select.option value="{{ $province->workplace_id }}">
                {{ $province->short_name }}
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

    <div>

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
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
        <div class="bg-zinc-50/50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm relative overflow-hidden group hover:border-blue-500/30 transition-all duration-300">
            <div class="absolute -right-4 -top-4 text-blue-500/5 dark:text-blue-500/10 group-hover:text-blue-500/20 transition-colors transform rotate-12">
                <flux:icon.table-cells variant="solid" class="w-24 h-24" />
            </div>
            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400 mb-2 block relative z-10">{{ __('Total Approved') }}</span>
            <div class="text-4xl font-black text-zinc-900 dark:text-white relative z-10 tracking-tighter">{{ $grandApproved }}</div>
        </div>

        <div class="bg-zinc-50/50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm relative overflow-hidden group hover:border-purple-500/30 transition-all duration-300">
            <div class="absolute -right-4 -top-4 text-purple-500/5 dark:text-purple-500/10 group-hover:text-purple-500/20 transition-colors transform rotate-12">
                <flux:icon.users variant="solid" class="w-24 h-24" />
            </div>
            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400 mb-2 block relative z-10">{{ __('Total Filled') }}</span>
            <div class="text-4xl font-black text-zinc-600 dark:text-zinc-400 relative z-10 tracking-tighter">{{ $grandFilled }}</div>
        </div>

        <div class="bg-zinc-50/50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm relative overflow-hidden group hover:border-emerald-500/30 transition-all duration-300">
            <div class="absolute -right-4 -top-4 {{ $grandDiff < 0 ? 'text-red-500/5 dark:text-red-500/10 group-hover:text-red-500/20' : 'text-emerald-500/5 dark:text-emerald-500/10 group-hover:text-emerald-500/20' }} transition-colors transform rotate-12">
                <flux:icon.arrows-up-down variant="solid" class="w-24 h-24" />
            </div>
            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400 mb-2 block relative z-10">{{ __('Overall Gap') }}</span>
            <div class="text-4xl font-black {{ $grandDiff < 0 ? 'text-red-600 dark:text-red-400' : ($grandDiff > 0 ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-900 dark:text-white') }} relative z-10 tracking-tighter">
                {{ $grandDiff > 0 ? '+' . $grandDiff : $grandDiff }}
            </div>
        </div>
    </div>

    {{-- 2. MOBILE VIEW (Enhanced Card Layout) --}}
    <div class="md:hidden space-y-12">
        @forelse($groupedRows as $typeId => $items)
        @php $currentType = $typeLabels[$typeId] ?? ['label' => 'General', 'class' => 'bg-zinc-50 dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 border-zinc-100 dark:border-zinc-800']; @endphp

        <div class="space-y-4">
            {{-- Category Sticky Header --}}
            <div class="sticky top-0 z-10 py-2 bg-zinc-50/95 dark:bg-zinc-900/95 backdrop-blur-sm -mx-4 px-4 mb-2">
                <div class="inline-flex px-3 py-1 rounded-lg border text-[10px] font-black uppercase tracking-widest shadow-sm {{ $currentType['class'] }}">
                    {{ $currentType['label'] }}
                </div>
            </div>

            @foreach($items as $row)
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl overflow-hidden shadow-sm active:scale-[0.98] transition-transform">
                <div class="p-5 border-b border-zinc-50 dark:border-zinc-800 flex justify-between items-start gap-4">
                    <div class="flex-1">
                        <div class="text-base font-bold text-zinc-900 dark:text-white leading-tight mb-1">{{ $row['subject_name'] }}</div>
                        <div class="text-[10px] text-zinc-500 dark:text-zinc-400 font-bold uppercase tracking-tight">{{ $row['medium_name'] }}</div>
                    </div>

                    @php
                    $statusClasses = [
                    'Balanced' => 'bg-green-100 dark:bg-green-500/10 text-green-700 dark:text-green-400 border-green-200 dark:border-green-500/20',
                    'Excess' => 'bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border-blue-200 dark:border-blue-500/20',
                    'Deficit' => 'bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-400 border-red-200 dark:border-red-500/20',
                    ][$row['status']] ?? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border-zinc-200 dark:border-zinc-700';
                    @endphp
                    <span class="shrink-0 px-3 py-1 rounded-full border text-[10px] font-black uppercase {{ $statusClasses }}">
                        {{ $row['status'] }}
                    </span>
                </div>

                <div class="px-5 py-4 grid grid-cols-3 gap-4 bg-zinc-50/30 dark:bg-zinc-800/30 text-center">
                    <div class="space-y-1">
                        <dt class="text-[9px] uppercase font-bold text-zinc-400 dark:text-zinc-500 tracking-tighter">{{ __('Approved') }}</dt>
                        <dd class="text-base font-extrabold text-zinc-700 dark:text-zinc-300">{{ $row['approved_posts'] }}</dd>
                    </div>
                    <div class="space-y-1">
                        <dt class="text-[9px] uppercase font-bold text-zinc-400 dark:text-zinc-500 tracking-tighter">{{ __('Filled') }}</dt>
                        <dd class="text-base font-extrabold text-zinc-700 dark:text-zinc-300">{{ $row['filled_posts'] }}</dd>
                    </div>
                    <div class="space-y-1">
                        <dt class="text-[9px] uppercase font-bold text-zinc-400 dark:text-zinc-500 tracking-tighter">{{ __('Gap') }}</dt>
                        <dd class="text-base font-black {{ $row['diff'] < 0 ? 'text-red-600 dark:text-red-400' : ($row['diff'] > 0 ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-900 dark:text-zinc-100') }}">
                            {{ $row['diff'] > 0 ? '+' . $row['diff'] : $row['diff'] }}
                        </dd>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @empty
        <div class="text-center py-16 bg-white dark:bg-zinc-900 rounded-2xl border-2 border-dashed border-zinc-200 dark:border-zinc-800 text-zinc-400 dark:text-zinc-500 text-sm">
            <div class="mb-2 text-2xl font-bold">📭</div>
            {{ __('No staff records found') }}
        </div>
        @endforelse

        {{-- MOBILE GRAND TOTAL --}}
        @if($rows)
        <div class="p-6 bg-zinc-950 dark:bg-black rounded-4xl text-white shadow-2xl mt-10 relative overflow-hidden border border-zinc-800">
            <div class="absolute top-0 right-0 p-4 opacity-10 text-4xl font-black"></div>
            <div class="text-[11px] font-black uppercase tracking-[0.2em] text-zinc-500 mb-6 border-b border-zinc-800/50 pb-3">{{ __('Grand Summary') }}</div>
            <div class="grid grid-cols-3 gap-6 text-center relative z-10">
                <div>
                    <div class="text-[10px] font-bold text-zinc-500 uppercase mb-1">{{ __('Approved') }}</div>
                    <div class="text-2xl font-black tracking-tighter">{{ $grandApproved }}</div>
                </div>
                <div>
                    <div class="text-[10px] font-bold text-zinc-500 uppercase mb-1">{{ __('Filled') }}</div>
                    <div class="text-2xl font-black text-zinc-400 tracking-tighter">{{ $grandFilled }}</div>
                </div>
                <div>
                    <div class="text-[10px] font-bold text-zinc-500 uppercase mb-1">{{ __('Gap') }}</div>
                    <div class="text-2xl font-black tracking-tighter {{ $grandDiff < 0 ? 'text-red-400' : ($grandDiff > 0 ? 'text-blue-400' : 'text-white') }}">
                        {{ $grandDiff > 0 ? '+' . $grandDiff : $grandDiff }}
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- 3. WEB VIEW (Polished Table) --}}
    <div class="hidden md:block bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-sm overflow-hidden mt-6">
        <table class="w-full text-left border-collapse">
            <thead class="bg-zinc-50/80 dark:bg-zinc-950/80 border-b border-zinc-200 dark:border-zinc-800">
                <tr>
                    <th class="px-6 py-5 text-[11px] font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-500 border-r border-zinc-100 dark:border-zinc-800">Subject / Medium</th>
                    <th class="px-4 py-5 text-[11px] font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-500 text-right">Approved</th>
                    <th class="px-4 py-5 text-[11px] font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-500 text-right">Filled</th>
                    <th class="px-4 py-5 text-[11px] font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-500 text-right">Gap</th>
                    <th class="px-6 py-5 text-[11px] font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-500 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse($groupedRows as $typeId => $items)
                @php $currentType = $typeLabels[$typeId] ?? ['label' => 'Other', 'class' => 'bg-zinc-50 dark:bg-zinc-900/50 text-zinc-700 dark:text-zinc-300']; @endphp
                <tr class="{{ $currentType['class'] }} border-y border-zinc-100/50 dark:border-zinc-800/50">
                    <td colspan="5" class="px-6 py-2.5">
                        <span class="text-[10px] font-black uppercase tracking-[0.25em] opacity-80">{{ $currentType['label'] }}</span>
                    </td>
                </tr>

                @foreach($items as $row)
                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors group">
                    <td class="px-6 py-4 border-r border-zinc-50 dark:border-zinc-800">
                        <div class="text-sm font-bold text-zinc-900 dark:text-white leading-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $row['subject_name'] }}</div>
                        <div class="text-[10px] text-zinc-400 dark:text-zinc-500 font-bold uppercase mt-1 tracking-tight">{{ $row['medium_name'] }}</div>
                    </td>
                    <td class="px-4 py-4 text-right text-sm font-bold text-zinc-600 dark:text-zinc-400 tracking-tight">{{ $row['approved_posts'] }}</td>
                    <td class="px-4 py-4 text-right text-sm font-bold text-zinc-600 dark:text-zinc-400 tracking-tight">{{ $row['filled_posts'] }}</td>
                    <td class="px-4 py-4 text-right">
                        <span class="text-sm font-black {{ $row['diff'] < 0 ? 'text-red-600 dark:text-red-400' : ($row['diff'] > 0 ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-900 dark:text-zinc-100') }}">
                            {{ $row['diff'] > 0 ? '+' . $row['diff'] : $row['diff'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @php
                        $statusBadge = [
                        'Balanced' => 'bg-green-100 dark:bg-green-500/10 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-500/20',
                        'Excess' => 'bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-500/20',
                        'Deficit' => 'bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-500/20',
                        ][$row['status']] ?? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700';
                        @endphp
                        <span class="inline-block px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $statusBadge }}">
                            {{ $row['status'] }}
                        </span>
                    </td>
                </tr>
                @endforeach
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-20 text-center text-zinc-400 dark:text-zinc-500 italic text-sm">
                        {{ __('No staff data available.') }}
                    </td>
                </tr>
                @endforelse
            </tbody>

            @if($rows)
            <tfoot class="bg-zinc-950 dark:bg-black text-white border-t-4 border-zinc-800 dark:border-zinc-900">
                <tr>
                    <td class="px-6 py-8 text-xs font-black uppercase tracking-[0.3em] text-zinc-500 dark:text-zinc-600">
                        {{ __('Grand Summary') }}
                    </td>
                    <td class="px-4 py-8 text-right text-2xl font-black tracking-tighter text-zinc-100">
                        {{ $grandApproved }}
                    </td>
                    <td class="px-4 py-8 text-right text-2xl font-black text-zinc-400 dark:text-zinc-500 tracking-tighter">
                        {{ $grandFilled }}
                    </td>
                    <td class="px-4 py-8 text-right text-2xl font-black tracking-tighter {{ $grandDiff < 0 ? 'text-red-400' : ($grandDiff > 0 ? 'text-blue-400' : 'text-zinc-100') }}">
                        {{ $grandDiff > 0 ? '+' . $grandDiff : $grandDiff }}
                    </td>
                    <td class="px-6 py-8 text-center">
                        <span class="px-3 py-1.5 rounded-lg bg-zinc-900 dark:bg-zinc-950 text-[10px] font-black uppercase text-zinc-500 dark:text-zinc-600 tracking-widest border border-zinc-800 dark:border-zinc-900">
                            {{ __('Finalized') }}
                        </span>
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</section>