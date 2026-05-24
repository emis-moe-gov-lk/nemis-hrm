<section class="w-full">
    <x-offices.peo.peo-layout :officeId="$officeId">
        {{-- Header Section --}}
        <div class="relative mb-8 w-full">
            <flux:heading size="xl" level="1" class="mb-1">{{ __('Institution Cadre Summary') }}</flux:heading>
            <flux:subheading size="lg" class="mb-6">{{ __('List of Cadre Summary under the selected PEO.') }}</flux:subheading>
        </div>
        @if($circular)
        <div class="flex justify-end w-full mb-6">
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-slate-300 rounded-2xl shadow-sm w-full sm:w-fit">
                <div class="flex-1 sm:flex-none text-right">
                    <span class="text-[9px] font-bold text-slate-500 uppercase tracking-wider block mb-0.5">{{ __('Circular') }}</span>
                    <span class="text-sm font-extrabold text-blue-600 tracking-tight">{{ $circular->circular_no }}</span>
                </div>

                <flux:separator vertical class="h-8" />

                <div class="flex-1 sm:flex-none text-right">
                    <span class="text-[9px] font-bold text-slate-500 uppercase tracking-wider block mb-0.5">{{ __('Date') }}</span>
                    <span class="text-sm font-bold text-slate-700">{{ $circular->issued_date }}</span>
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
        1 => ['label' => 'Teacher / Subjects', 'class' => 'bg-blue-50 text-blue-700 border-blue-100'],
        2 => ['label' => 'Principal / Designation', 'class' => 'bg-purple-50 text-purple-700 border-purple-100'],
        3 => ['label' => 'Other', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-100'],
        ];

        $groupedRows = collect($rows)->groupBy('subject_type');
        $grandApproved = collect($rows)->sum('approved_posts');
        $grandFilled = collect($rows)->sum('filled_posts');
        $grandDiff = collect($rows)->sum('diff');
        @endphp

        {{-- 2. MOBILE VIEW (Enhanced Card Layout) --}}
        <div class="md:hidden space-y-8">
            @forelse($groupedRows as $typeId => $items)
            @php $currentType = $typeLabels[$typeId] ?? ['label' => 'General', 'class' => 'bg-slate-50 text-slate-700 border-slate-200']; @endphp

            <div class="space-y-4">
                {{-- Category Sticky Header --}}
                <div class="sticky top-0 z-10 py-2 bg-slate-50/95 backdrop-blur-sm -mx-4 px-4 mb-2">
                    <div class="inline-flex px-3 py-1 rounded-lg border text-[10px] font-black uppercase tracking-widest shadow-sm {{ $currentType['class'] }}">
                        {{ $currentType['label'] }}
                    </div>
                </div>

                @foreach($items as $row)
                <div class="bg-white border border-slate-300 rounded-3xl overflow-hidden shadow-sm active:scale-[0.98] transition-transform">
                    <div class="p-5 border-b border-slate-200 flex justify-between items-start gap-4">
                        <div class="flex-1">
                            <div class="text-base font-bold text-slate-900 leading-tight mb-1">{{ $row['subject_name'] }}</div>
                            <div class="text-[10px] text-slate-500 font-bold uppercase tracking-tight">{{ $row['medium_name'] }}</div>
                        </div>

                        @php
                        $statusClasses = [
                        'Balanced' => 'bg-green-100 text-green-700 border-green-200',
                        'Excess' => 'bg-blue-100 text-blue-700 border-blue-200',
                        'Deficit' => 'bg-red-100 text-red-700 border-red-200',
                        ][$row['status']] ?? 'bg-slate-100 text-slate-700 border-slate-300';
                        @endphp
                        <span class="shrink-0 px-3 py-1 rounded-full border text-[10px] font-black uppercase {{ $statusClasses }}">
                            {{ $row['status'] }}
                        </span>
                    </div>

                    <div class="px-5 py-4 grid grid-cols-3 gap-4 bg-slate-50/30 text-center">
                        <div class="space-y-1">
                            <dt class="text-[9px] uppercase font-bold text-slate-500 tracking-tighter">{{ __('Approved') }}</dt>
                            <dd class="text-base font-extrabold text-slate-700">{{ $row['approved_posts'] }}</dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-[9px] uppercase font-bold text-slate-500 tracking-tighter">{{ __('Filled') }}</dt>
                            <dd class="text-base font-extrabold text-slate-700">{{ $row['filled_posts'] }}</dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-[9px] uppercase font-bold text-slate-500 tracking-tighter">{{ __('Gap') }}</dt>
                            <dd class="text-base font-black {{ $row['diff'] < 0 ? 'text-red-600' : ($row['diff'] > 0 ? 'text-blue-600' : 'text-slate-900') }}">
                                {{ $row['diff'] > 0 ? '+' . $row['diff'] : $row['diff'] }}
                            </dd>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @empty
            <div class="text-center py-16 bg-white rounded-3xl border-2 border-dashed border-slate-300 text-slate-500 text-sm">
                <div class="mb-2">📭</div>
                {{ __('No staff records found') }}
            </div>
            @endforelse

            {{-- MOBILE GRAND TOTAL --}}
            @if($rows)
            <div class="p-6 bg-indigo-600 rounded-[2.5rem] text-white shadow-2xl mt-10 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10 text-4xl font-black"></div>
                <div class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-500 mb-6 border-b border-slate-800 pb-3">{{ __('Grand Summary') }}</div>
                <div class="grid grid-cols-3 gap-6 text-center relative z-10">
                    <div>
                        <div class="text-[10px] font-bold text-slate-500 uppercase mb-1">{{ __('Approved') }}</div>
                        <div class="text-2xl font-black tracking-tighter">{{ $grandApproved }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-slate-500 uppercase mb-1">{{ __('Filled') }}</div>
                        <div class="text-2xl font-black text-slate-300 tracking-tighter">{{ $grandFilled }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-slate-500 uppercase mb-1">{{ __('Gap') }}</div>
                        <div class="text-2xl font-black tracking-tighter {{ $grandDiff < 0 ? 'text-red-400' : ($grandDiff > 0 ? 'text-blue-400' : 'text-white') }}">
                            {{ $grandDiff > 0 ? '+' . $grandDiff : $grandDiff }}
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- 3. WEB VIEW (Polished Table) --}}
        <div class="hidden md:block bg-white border border-slate-300 rounded-3xl shadow-sm overflow-hidden mt-6">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50/80 border-b border-slate-300">
                    <tr>
                        <th class="px-6 py-5 text-[11px] font-black uppercase tracking-widest text-slate-500 border-r border-slate-200">Subject / Medium</th>
                        <th class="px-4 py-5 text-[11px] font-black uppercase tracking-widest text-slate-500 text-right">Approved</th>
                        <th class="px-4 py-5 text-[11px] font-black uppercase tracking-widest text-slate-500 text-right">Filled</th>
                        <th class="px-4 py-5 text-[11px] font-black uppercase tracking-widest text-slate-500 text-right">Gap</th>
                        <th class="px-6 py-5 text-[11px] font-black uppercase tracking-widest text-slate-500 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($groupedRows as $typeId => $items)
                    @php $currentType = $typeLabels[$typeId] ?? ['label' => 'Other', 'class' => 'bg-slate-50 text-slate-700']; @endphp
                    <tr class="{{ $currentType['class'] }} border-y border-slate-200/50">
                        <td colspan="5" class="px-6 py-2.5">
                            <span class="text-[10px] font-black uppercase tracking-[0.25em]">{{ $currentType['label'] }}</span>
                        </td>
                    </tr>

                    @foreach($items as $row)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-6 py-4 border-r border-slate-200">
                            <div class="text-sm font-bold text-slate-900 leading-tight group-hover:text-blue-600 transition-colors">{{ $row['subject_name'] }}</div>
                            <div class="text-[10px] text-slate-500 font-bold uppercase mt-1 tracking-tight">{{ $row['medium_name'] }}</div>
                        </td>
                        <td class="px-4 py-4 text-right text-sm font-bold text-slate-600 tracking-tight">{{ $row['approved_posts'] }}</td>
                        <td class="px-4 py-4 text-right text-sm font-bold text-slate-600 tracking-tight">{{ $row['filled_posts'] }}</td>
                        <td class="px-4 py-4 text-right">
                            <span class="text-sm font-black {{ $row['diff'] < 0 ? 'text-red-600' : ($row['diff'] > 0 ? 'text-blue-600' : 'text-slate-900') }}">
                                {{ $row['diff'] > 0 ? '+' . $row['diff'] : $row['diff'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                            $statusBadge = [
                            'Balanced' => 'bg-green-100 text-green-700',
                            'Excess' => 'bg-blue-100 text-blue-700',
                            'Deficit' => 'bg-red-100 text-red-700',
                            ][$row['status']] ?? 'bg-slate-100 text-slate-700';
                            @endphp
                            <span class="inline-block px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $statusBadge }}">
                                {{ $row['status'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center text-slate-500 italic text-sm">
                            {{ __('No staff data available.') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>

                @if($rows)
                <tfoot class="bg-indigo-600 text-white border-t-4 border-slate-800">
                    <tr>
                        <td class="px-6 py-8 text-xs font-black uppercase tracking-[0.3em] text-slate-500">
                            {{ __('Grand Summary') }}
                        </td>
                        <td class="px-4 py-8 text-right text-2xl font-black tracking-tighter">
                            {{ $grandApproved }}
                        </td>
                        <td class="px-4 py-8 text-right text-2xl font-black text-slate-500 tracking-tighter">
                            {{ $grandFilled }}
                        </td>
                        <td class="px-4 py-8 text-right text-2xl font-black tracking-tighter {{ $grandDiff < 0 ? 'text-red-400' : ($grandDiff > 0 ? 'text-blue-400' : 'text-white') }}">
                            {{ $grandDiff > 0 ? '+' . $grandDiff : $grandDiff }}
                        </td>
                        <td class="px-6 py-8 text-center">
                            <span class="px-3 py-1.5 rounded-lg bg-slate-800 text-[10px] font-black uppercase text-slate-500 tracking-widest border border-slate-700">
                                {{ __('Finalized') }}
                            </span>
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </x-offices.peo.peo-layout>
</section>