<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    {{-- Header --}}
    <x-page-header
        title="Add DMS Approved Cadre"
        subtitle="Configure approved staff positions for {{ $institution->name }}"
        icon="plus-circle"
    />

        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex items-center gap-4 px-4 py-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm transition-all hover:border-blue-300 dark:hover:border-blue-700/50">
                <div class="text-center">
                    <span class="text-[10px] font-black text-zinc-400 dark:text-zinc-400 uppercase block tracking-widest">{{ __('Workplace') }}</span>
                    <span class="text-sm font-bold text-blue-600 dark:text-blue-400">{{ $institution->name }}</span>
                </div>
                <flux:separator vertical class="h-8 dark:bg-zinc-800" />
                <div class="text-center">
                    <span class="text-[10px] font-black text-zinc-400 dark:text-zinc-400 uppercase block tracking-widest">{{ __('Census') }}</span>
                    <span class="text-sm font-bold text-zinc-700 dark:text-zinc-300">{{ $institution->census_no }}</span>
                </div>
            </div>

            <div class="flex items-center gap-4 px-4 py-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm transition-all hover:border-emerald-300 dark:hover:border-emerald-700/50">
                <div class="text-center">
                    <span class="text-[10px] font-black text-zinc-400 dark:text-zinc-400 uppercase block tracking-widest">{{ __('Circular') }}</span>
                    <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ $circular->circular_no }}</span>
                </div>
                <flux:separator vertical class="h-8 dark:bg-zinc-800" />
                <div class="text-center">
                    <span class="text-[10px] font-black text-zinc-400 dark:text-zinc-400 uppercase block tracking-widest">{{ __('Date') }}</span>
                    <span class="text-sm font-bold text-zinc-700 dark:text-zinc-300">{{ $circular->issued_date }}</span>
                </div>
            </div>
        </div>

    <form wire:submit.prevent="save" class="space-y-6">

        {{-- Custom Notification (Standard Tailwind) --}}
        @if (session('success'))
        <div class="flex items-center gap-3 p-4 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 text-green-800 dark:text-green-400 rounded-xl text-sm font-medium">
            <flux:icon.check-circle variant="mini" class="text-green-600 dark:text-green-500" />
            {{ session('success') }}
        </div>
        @endif

        {{-- Table Container --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[700px]">
                    <thead class="bg-zinc-50/80 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-700">
                        <tr>
                            <th class="sticky left-0 z-10 bg-zinc-50/80 dark:bg-zinc-800 px-6 py-4 text-[11px] font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-400 border-r border-zinc-200 dark:border-zinc-700 shadow-[1px_0_0_0_var(--zinc-200)] dark:shadow-[1px_0_0_0_var(--zinc-800)]">
                                {{ __('Subject Details') }}
                            </th>
                            @foreach ($mediums as $medium)
                            <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-400 text-center border-r border-zinc-100 dark:border-zinc-700 last:border-r-0">
                                {{ $medium->name }}
                            </th>
                            @endforeach
                            <th class="px-6 py-4 text-[11px] font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-400 text-center bg-zinc-100/30 dark:bg-zinc-800/30">
                                {{ __('Total') }}
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                        @php
                        $grouped = $subjectList->groupBy('type');
                        $typeLabels = [
                        1 => ['label' => 'Teacher / Subjects', 'class' => 'bg-blue-50 dark:bg-blue-900/10 text-blue-700 dark:text-blue-400'],
                        2 => ['label' => 'Principal / Designation', 'class' => 'bg-purple-50 dark:bg-purple-900/10 text-purple-700 dark:text-purple-400'],
                        3 => ['label' => 'Other', 'class' => 'bg-emerald-50 dark:bg-emerald-900/10 text-emerald-700 dark:text-emerald-400'],
                        ];

                        $columnTotals = array_fill_keys($mediums->pluck('medium_id')->toArray(), 0);
                        $grandTotal = 0;
                        @endphp

                        @foreach ($grouped as $type => $subjects)
                        {{-- Section Header --}}
                        <tr class="{{ $typeLabels[$type]['class'] ?? 'bg-zinc-50 dark:bg-zinc-800/30' }}">
                            <td colspan="{{ count($mediums) + 2 }}" class="px-6 py-2.5 text-[10px] font-black uppercase tracking-[0.15em] border-y border-zinc-200/50 dark:border-zinc-700/80 shadow-[inset_0_1px_0_rgba(255,255,255,0.1)]">
                                {{ $typeLabels[$type]['label'] ?? 'General' }}
                            </td>
                        </tr>

                        @foreach ($subjects as $subject)
                        @php $rowTotal = 0; @endphp
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors group">
                            {{-- Subject Name (Sticky) --}}
                            <td class="sticky left-0 z-10 bg-white dark:bg-zinc-900 group-hover:bg-zinc-50 dark:group-hover:bg-zinc-800/50 px-6 py-4 border-r border-zinc-100 dark:border-zinc-700 shadow-[1px_0_0_0_var(--zinc-100)] dark:shadow-[1px_0_0_0_var(--zinc-800)] transition-colors">
                                <div class="text-sm font-bold text-zinc-900 dark:text-zinc-100 leading-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $subject->name_en }}</div>
                                <div class="text-[10px] text-zinc-400 dark:text-zinc-400 font-mono mt-1 uppercase tracking-tighter bg-zinc-100 dark:bg-zinc-800 inline-block px-1.5 py-0.5 rounded border border-zinc-200 dark:border-zinc-700">ID: {{ $subject->subject_id }}</div>
                            </td>

                            {{-- Inputs --}}
                            @foreach ($mediums as $medium)
                            @php
                            $val = (int)($cadre[$subject->subject_id][$medium->medium_id] ?? 0);
                            $rowTotal += $val;
                            $columnTotals[$medium->medium_id] += $val;
                            @endphp
                            <td class="px-3 py-2 border-r border-zinc-50 dark:border-zinc-700/50 last:border-r-0">
                                <flux:input
                                    type="number"
                                    min="0"
                                    size="sm"
                                    class="text-center font-bold bg-zinc-50/50 dark:bg-zinc-900"
                                    wire:model.live="cadre.{{ $subject->subject_id }}.{{ $medium->medium_id }}" />
                            </td>
                            @endforeach

                            {{-- Row Sum --}}
                            <td class="px-6 py-2 text-center bg-zinc-50/30 dark:bg-zinc-800/30">
                                <span class="text-sm font-bold {{ $rowTotal > 0 ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-300 dark:text-zinc-700' }}">
                                    {{ $rowTotal }}
                                </span>
                            </td>
                        </tr>
                        @php $grandTotal += $rowTotal; @endphp
                        @endforeach
                        @endforeach
                    </tbody>

                    {{-- Footer Summary --}}
                    @if($subjectList->isNotEmpty())
                    <tfoot class="bg-zinc-900 dark:bg-black text-white border-t-4 border-white dark:border-zinc-700 relative z-20 box-border border-b border-x border-r border-l">
                        <tr class="font-bold relative">
                            <td class="sticky left-0 z-30 bg-zinc-900 dark:bg-black px-6 py-5 text-xs font-black uppercase tracking-widest border-r border-zinc-800 dark:border-zinc-900 shadow-[1px_0_0_0_var(--zinc-800)] dark:shadow-[1px_0_0_0_var(--zinc-900)]">
                                {{ __('Column Totals') }}
                            </td>
                            @foreach ($mediums as $medium)
                            <td class="px-4 py-5 text-center text-sm font-bold border-r border-zinc-800 dark:border-zinc-900 last:border-r-0 relative z-20">
                                {{ $columnTotals[$medium->medium_id] }}
                            </td>
                            @endforeach
                            <td class="px-0 py-0 text-center relative pointer-events-none align-middle w-28">
                                <div class="absolute inset-0 bg-blue-600 dark:bg-blue-600/80 z-20 w-full h-full border-t flex items-center justify-center border-t-blue-500 border-x border-blue-600 dark:border-x-blue-600/80">
                                    <span class="text-xl font-black leading-none text-white w-full h-full flex items-center justify-center">{{ $grandTotal }}</span>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4">
            <div class="flex items-start sm:items-center gap-3 text-zinc-400 dark:text-zinc-400 bg-blue-50/50 dark:bg-blue-900/10 p-3 rounded-xl border border-blue-100/50 dark:border-blue-900/30">
                <flux:icon.information-circle variant="mini" class="text-blue-500 dark:text-blue-400 shrink-0 mt-0.5 sm:mt-0" />
                <span class="text-[12px] leading-relaxed">
                    {{ __('Updates are saved only after clicking Save Changes. Totals update in real-time. Check the blue cell for the final verification.') }}
                </span>
            </div>

            <div class="flex gap-3 w-full sm:w-auto shrink-0">
                <flux:button href="{{ route('cadre-dms-approved.view', $school_id) }}" variant="ghost" class="flex-1 sm:flex-none">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary" class="px-8 flex-1 sm:flex-none shadow-md shadow-blue-500/20 dark:shadow-blue-900/30">
                    {{ __('Save Changes') }}
                </flux:button>
            </div>
        </div>
    </form>
</div>
