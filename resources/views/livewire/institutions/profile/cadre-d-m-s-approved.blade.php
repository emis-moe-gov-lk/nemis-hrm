<section class="w-full" x-data="{ selectedRow: null }">
    {{-- 1. Header & Context Section --}}
    <header class="space-y-6 mb-8">
        <div>
            <flux:heading size="xl" level="1" class="text-2xl! font-black tracking-tight text-slate-800">
                {{ __('Staff Summary') }}
            </flux:heading>
            <flux:subheading size="lg" class="text-slate-500">
                {{ __('Real-time status of filled vs. approved posts. Excess staff highlighted by service seniority.') }}
            </flux:subheading>
        </div>
    </header>

    <x-institutions.institution-layout :institutionId="$id">
        {{-- Circular Info Box aligned Right --}}
        <div class="flex justify-end w-full">
            @if ($circular)
            <div class="flex items-center gap-3">
                <flux:button
                    wire:click="downloadPdf"
                    icon="printer"
                    variant="primary"
                    class="h-10 bg-blue-600! hover:bg-blue-700! text-white! border-none shadow-sm">
                    {{ __('Download PDF') }}
                </flux:button>

                <div class="flex items-center gap-4 px-4 py-2 bg-white border border-slate-300 rounded-xl shadow-sm w-fit">
                    <div class="text-right">
                        <span class="text-[10px] font-black text-slate-500 uppercase block leading-none mb-1">{{ __('Circular') }}</span>
                        <span class="text-sm font-bold text-blue-600 leading-none">{{ $circular->circular_no }}</span>
                    </div>

                    <flux:separator vertical class="h-6" />

                    <div class="text-right">
                        <span class="text-[10px] font-black text-slate-500 uppercase block leading-none mb-1">{{ __('Date') }}</span>
                        <span class="text-sm font-bold text-slate-700 leading-none">{{ $circular->issued_date }}</span>
                    </div>
                </div>
            </div>
            @else
            <div class="w-full rounded-2xl border border-amber-200 bg-amber-50 p-4 text-amber-900 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="mt-0.5 rounded-xl bg-amber-100 p-2 text-amber-700">
                        <flux:icon.exclamation-triangle class="h-5 w-5" />
                    </div>
                    <div>
                        <h2 class="text-sm font-black uppercase tracking-widest">{{ __('No Active DMS Circular') }}</h2>
                        <p class="mt-1 text-sm leading-6 text-amber-800">
                            {{ __('No active DMS approved-cadre circular exists yet. Create or activate a cadre circular before reviewing approved cadre against staff.') }}
                        </p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        @php
        $typeLabels = [
        1 => ['label' => 'Teacher / Subjects', 'class' => 'bg-blue-50 text-blue-700 border-blue-100'],
        2 => ['label' => 'Principal / Designation', 'class' => 'bg-purple-50 text-purple-700 border-purple-100'],
        3 => ['label' => 'Other', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-100'],
        ];

        $groupedRows = collect($rows)->groupBy('subject_type');

        // Grand Totals Calculation
        $grandApproved = collect($rows)->sum('approved_posts');
        $grandFilled = collect($rows)->sum('filled_posts');
        $grandDiff = collect($rows)->sum('diff');
        @endphp

        {{-- 2. MOBILE VIEW (Card Layout) --}}
        <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">{{ __('Approved') }}</span>
                    <span class="rounded-full bg-blue-50 px-2 py-1 text-[10px] font-black text-blue-700">{{ __('Cadre') }}</span>
                </div>
                <div class="mt-2 text-3xl font-black tracking-tight text-slate-900">{{ number_format($grandApproved) }}</div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">{{ __('Filled') }}</span>
                    <span class="rounded-full bg-emerald-50 px-2 py-1 text-[10px] font-black text-emerald-700">{{ __('Staff') }}</span>
                </div>
                <div class="mt-2 text-3xl font-black tracking-tight text-slate-900">{{ number_format($grandFilled) }}</div>
            </div>

            <div class="rounded-2xl border {{ $grandDiff < 0 ? 'border-red-200 bg-red-50/60' : ($grandDiff > 0 ? 'border-blue-200 bg-blue-50/60' : 'border-emerald-200 bg-emerald-50/60') }} p-4 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-[10px] font-black uppercase tracking-widest {{ $grandDiff < 0 ? 'text-red-600' : ($grandDiff > 0 ? 'text-blue-600' : 'text-emerald-600') }}">{{ __('Difference') }}</span>
                    <span class="rounded-full bg-white px-2 py-1 text-[10px] font-black {{ $grandDiff < 0 ? 'text-red-700' : ($grandDiff > 0 ? 'text-blue-700' : 'text-emerald-700') }}">
                        {{ $grandDiff < 0 ? __('Deficit') : ($grandDiff > 0 ? __('Excess') : __('Balanced')) }}
                    </span>
                </div>
                <div class="mt-2 text-3xl font-black tracking-tight {{ $grandDiff < 0 ? 'text-red-700' : ($grandDiff > 0 ? 'text-blue-700' : 'text-emerald-700') }}">
                    {{ $grandDiff > 0 ? '+' . number_format($grandDiff) : number_format($grandDiff) }}
                </div>
            </div>
        </div>

        <div class="md:hidden space-y-8 mt-6">
            @forelse($groupedRows as $typeId => $items)
            @php $currentType = $typeLabels[$typeId] ?? ['label' => 'General', 'class' => 'bg-slate-50 text-slate-700 border-slate-200']; @endphp

            <div class="space-y-4">
                <div class="px-2 py-1 inline-block rounded-md border text-[10px] font-black uppercase tracking-widest {{ $currentType['class'] }}">
                    {{ $currentType['label'] }}
                </div>

                @foreach($items as $row)
                @php $uniqueId = 'mob_' . $row['subject_id'] . '_' . $row['medium_id']; @endphp
                <div class="bg-white border border-slate-300 rounded-2xl overflow-hidden shadow-sm transition-all"
                    :class="selectedRow === '{{ $uniqueId }}' ? 'ring-2 ring-blue-600 border-transparent shadow-md' : ''">

                    {{-- Card Header --}}
                    <div class="p-4 flex justify-between items-start cursor-pointer" @click="selectedRow = (selectedRow === '{{ $uniqueId }}' ? null : '{{ $uniqueId }}')">
                        <div class="flex gap-3">
                            <flux:icon.chevron-right variant="mini" class="text-slate-300 mt-1 transition-transform" ::class="selectedRow === '{{ $uniqueId }}' ? 'rotate-90 text-blue-600' : ''" />
                            <div>
                                <div class="text-sm font-bold text-slate-900 leading-tight">{{ $row['subject_name'] }}</div>
                                <div class="text-[10px] text-slate-500 font-medium uppercase mt-1">{{ $row['medium_name'] }}</div>
                            </div>
                        </div>

                        <span class="px-2 py-0.5 rounded-full border text-[10px] font-bold uppercase {{ $row['status'] === 'Excess' ? 'bg-blue-100 text-blue-700 border-blue-200' : ($row['status'] === 'Deficit' ? 'bg-red-100 text-red-700 border-red-200' : 'bg-green-100 text-green-700 border-green-200') }}">
                            {{ $row['status'] }}
                        </span>
                    </div>

                    {{-- Stats Strip --}}
                    <div class="px-4 py-3 bg-slate-50/50 border-y border-slate-200 grid grid-cols-3 gap-2 text-center">
                        <div class="border-r border-slate-300">
                            <dt class="text-[9px] font-black text-slate-500 uppercase tracking-tighter">{{ __('Approved') }}</dt>
                            <dd class="text-xs font-bold text-slate-700">{{ $row['approved_posts'] }}</dd>
                        </div>
                        <div class="border-r border-slate-300">
                            <dt class="text-[9px] font-black text-slate-500 uppercase tracking-tighter">{{ __('Filled') }}</dt>
                            <dd class="text-xs font-bold text-slate-700">{{ $row['filled_posts'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-[9px] font-black text-slate-500 uppercase tracking-tighter">{{ __('Gap') }}</dt>
                            <dd class="text-xs font-black {{ $row['diff'] < 0 ? 'text-red-600' : ($row['diff'] > 0 ? 'text-blue-600' : 'text-slate-900') }}">
                                {{ $row['diff'] > 0 ? '+' . $row['diff'] : $row['diff'] }}
                            </dd>
                        </div>
                    </div>

                    {{-- Expanded Teacher List with Profile Links --}}
                    <div x-show="selectedRow === '{{ $uniqueId }}'" x-collapse x-cloak>
                        <div class="p-4 bg-white">
                            <div class="text-[9px] font-black text-slate-500 uppercase mb-3 tracking-widest">{{ __('Staff breakdown by seniority') }}</div>
                            @php $excessCount = $row['status'] === 'Excess' ? abs($row['diff']) : 0; @endphp

                            <div class="space-y-2">
                                @foreach($row['teachers'] as $tIndex => $teacher)
                                @php $isExcess = ($row['status'] === 'Excess' && $tIndex < $excessCount); @endphp
                                    @php
                                    // Determine route based on service
                                    $serviceId=$teacher->currentAppointment?->appointment?->service_id;
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
                                    <a href="{{ route($routeName, $teacher->employee->id) }}" class="mx-1">
                                        <div class="p-3 rounded-xl border {{ $isExcess ? 'bg-red-50 border-red-200' : 'bg-slate-50/50 border-slate-300' }}">
                                            <div class="flex justify-between items-start gap-2">
                                                <div class="text-[11px] font-bold {{ $isExcess ? 'text-red-700' : 'text-slate-800' }}">
                                                    {{ $teacher->employee->name_with_initials }}
                                                </div>
                                                @if($isExcess)
                                                <span class="shrink-0 text-[8px] bg-red-600 text-white px-1.5 py-0.5 rounded font-black uppercase">Excess</span>
                                                @endif
                                            </div>
                                            <div class="flex justify-between mt-2 text-[10px]">
                                                <span class="font-mono text-slate-500">{{ $teacher->employee->nic }}</span>
                                                <span class="font-bold {{ $isExcess ? 'text-red-600' : 'text-blue-600' }}">
                                                    {{ $teacher->currentAppointment->service_years }} years service
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                    @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @empty
            <div class="py-12 text-center text-slate-500">{{ __('No staff records found') }}</div>
            @endforelse

            {{-- Mobile Grand Total Footer --}}
            <div class="p-5 bg-white rounded-3xl text-slate-900 shadow-sm border border-slate-200 mt-8">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">{{ __('Grand Total Summary') }}</span>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[9px] font-black uppercase text-slate-600">{{ __('All Services') }}</span>
                </div>
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <div class="text-[9px] font-bold text-slate-500 uppercase">{{ __('Approved') }}</div>
                        <div class="text-xl font-black">{{ number_format($grandApproved) }}</div>
                    </div>
                    <div>
                        <div class="text-[9px] font-bold text-slate-500 uppercase">{{ __('Filled') }}</div>
                        <div class="text-xl font-black text-slate-700">{{ number_format($grandFilled) }}</div>
                    </div>
                    <div>
                        <div class="text-[9px] font-bold text-slate-500 uppercase">{{ __('Gap') }}</div>
                        <div class="text-xl font-black {{ $grandDiff < 0 ? 'text-red-600' : ($grandDiff > 0 ? 'text-blue-600' : 'text-emerald-600') }}">
                            {{ $grandDiff > 0 ? '+' . number_format($grandDiff) : number_format($grandDiff) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. WEB VIEW (Table) --}}
        <div class="hidden md:block bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden mt-6">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="w-10"></th>
                        <th class="px-5 py-4 text-[11px] font-black uppercase tracking-wider text-slate-500">Subject / Medium</th>
                        <th class="px-4 py-4 text-[11px] font-black uppercase tracking-wider text-slate-500 text-right">Approved</th>
                        <th class="px-4 py-4 text-[11px] font-black uppercase tracking-wider text-slate-500 text-right">Filled</th>
                        <th class="px-4 py-4 text-[11px] font-black uppercase tracking-wider text-slate-500 text-right">Difference</th>
                        <th class="px-5 py-4 text-[11px] font-black uppercase tracking-wider text-slate-500 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($groupedRows as $typeId => $items)
                    @php $currentType = $typeLabels[$typeId] ?? ['label' => 'Other', 'class' => 'bg-slate-50 text-slate-700']; @endphp
                    <tr class="{{ $currentType['class'] }} border-y border-slate-200">
                        <td colspan="6" class="px-5 py-2"><span class="text-[10px] font-black uppercase tracking-[0.2em]">{{ $currentType['label'] }}</span></td>
                    </tr>

                    @foreach($items as $row)
                    @php $uniqueId = 'web_' . $row['subject_id'] . '_' . $row['medium_id']; @endphp
                    <tr class="hover:bg-blue-50/30 transition-colors cursor-pointer" @click="selectedRow = (selectedRow === '{{ $uniqueId }}' ? null : '{{ $uniqueId }}')" :class="selectedRow === '{{ $uniqueId }}' ? 'bg-blue-50/50' : ''">
                        <td class="pl-5"><flux:icon.chevron-right variant="mini" class="text-slate-300 transition-transform" ::class="selectedRow === '{{ $uniqueId }}' ? 'rotate-90 text-blue-600' : ''" /></td>
                        <td class="px-5 py-4">
                            <div class="text-sm font-bold text-slate-900 leading-tight">{{ $row['subject_name'] }}</div>
                            <div class="text-[10px] text-slate-500 font-medium uppercase mt-0.5 tracking-tight">{{ $row['medium_name'] }}</div>
                        </td>
                        <td class="px-4 py-4 text-right text-sm font-semibold text-slate-700">{{ number_format($row['approved_posts']) }}</td>
                        <td class="px-4 py-4 text-right text-sm font-semibold text-slate-700">{{ number_format($row['filled_posts']) }}</td>
                        <td class="px-4 py-4 text-right text-sm font-black {{ $row['diff'] < 0 ? 'text-red-600' : ($row['diff'] > 0 ? 'text-blue-600' : 'text-slate-900') }}">{{ $row['diff'] > 0 ? '+' . number_format($row['diff']) : number_format($row['diff']) }}</td>
                        <td class="px-5 py-4 text-center">
                            <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wide {{ $row['status'] === 'Excess' ? 'bg-blue-100 text-blue-700' : ($row['status'] === 'Deficit' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700') }}">
                                {{ $row['status'] }}
                            </span>
                        </td>
                    </tr>

                    {{-- Expanded Table with Profile Links --}}
                    <tr x-show="selectedRow === '{{ $uniqueId }}'" x-transition x-cloak>
                        <td colspan="6" class="px-12 py-4 bg-slate-50/50">
                            <div class="bg-white border border-slate-300 rounded-xl overflow-hidden shadow-sm">
                                @if($row['teachers']->isNotEmpty())
                                @php $excessCount = $row['status'] === 'Excess' ? abs($row['diff']) : 0; @endphp
                                <table class="w-full text-xs">
                                    <thead class="bg-slate-50 border-b border-slate-300">
                                        <tr>
                                            <th class="px-4 py-2 text-[9px] font-black text-slate-500 uppercase">#</th>
                                            <th class="px-4 py-2 text-[9px] font-black text-slate-500 uppercase">Name</th>
                                            <th class="px-4 py-2 text-[9px] font-black text-slate-500 uppercase">NIC</th>
                                            <th class="px-4 py-2 text-[9px] font-black text-slate-500 uppercase text-right">Appointment Date</th>
                                            <th class="px-4 py-2 text-[9px] font-black text-slate-500 uppercase text-right">Service in School</th>
                                            <th class="px-4 py-2 text-[9px] font-black text-slate-500 uppercase text-right">action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($row['teachers'] as $tIndex => $teacher)
                                        @php $isExcessRow = ($row['status'] === 'Excess' && $tIndex < $excessCount); @endphp
                                            <tr class="transition-colors {{ $isExcessRow ? 'bg-red-50/80' : 'hover:bg-slate-50/50' }}">
                                            <td class="px-4 py-2.5 {{ $isExcessRow ? 'text-red-400' : 'text-slate-500' }}">
                                                {{ $tIndex + 1 }}
                                            </td>
                                            <td class="px-4 py-2.5 font-bold {{ $isExcessRow ? 'text-red-700' : 'text-slate-700' }}">
                                                <div class="flex items-center gap-2">
                                                    {{ $teacher->employee->name_with_initials }}
                                                    @if($isExcessRow)
                                                    <span class="text-[8px] px-1.5 py-0.5 bg-red-100 text-red-600 rounded uppercase font-black tracking-tighter border border-red-200">Excess Staff</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-4 py-2.5 font-mono {{ $isExcessRow ? 'text-red-500' : 'text-blue-600' }}">
                                                {{ $teacher->employee->nic }}
                                            </td>
                                            <td class="px-4 py-2.5 text-right font-black {{ $isExcessRow ? 'text-red-700' : 'text-slate-600' }}">
                                                {{ $teacher->currentAppointment->appoint_date->format('Y-m-d') }}
                                            </td>
                                            <td class="px-4 py-2.5 text-right font-black {{ $isExcessRow ? 'text-red-700' : 'text-slate-600' }}">
                                                {{ $teacher->currentAppointment->service_years }}
                                            </td>
                                            <td class="px-4 py-2.5 text-right font-black {{ $isExcessRow ? 'text-red-700' : 'text-slate-600' }}">
                                                @php
                                                // Determine route based on service
                                                $serviceId = $teacher->currentAppointment?->appointment?->service_id;
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
                                                <a href="{{ route($routeName, $teacher->employee->id) }}">
                                                    <flux:button icon="eye" size="sm" variant="outline" />
                                                </a>
                                            </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="p-6 text-center text-slate-500 italic text-xs">{{ __('No teachers currently assigned.') }}</div>
            @endif
        </div>
        </td>
        </tr>
        @endforeach
        @endforeach
        </tbody>
        {{-- Web Grand Total Footer --}}
        <tfoot class="bg-slate-50 border-t border-slate-200">
            <tr>
                <td colspan="2" class="px-5 py-5">
                    <div class="text-xs font-black uppercase tracking-[0.2em] text-slate-600">{{ __('Grand Total Summary') }}</div>
                    <div class="mt-1 text-[10px] font-bold uppercase text-slate-400">{{ __('All services and all subjects') }}</div>
                </td>
                <td class="px-4 py-5 text-right text-xl font-black text-slate-900">{{ number_format($grandApproved) }}</td>
                <td class="px-4 py-5 text-right text-xl font-black text-slate-700">{{ number_format($grandFilled) }}</td>
                <td class="px-4 py-5 text-right text-xl font-black {{ $grandDiff < 0 ? 'text-red-600' : ($grandDiff > 0 ? 'text-blue-600' : 'text-emerald-600') }}">
                    {{ $grandDiff > 0 ? '+' . number_format($grandDiff) : number_format($grandDiff) }}
                </td>
                <td class="px-5 py-5 text-center">
                    <span class="inline-flex items-center rounded-full bg-white px-3 py-1.5 text-[10px] font-black uppercase tracking-wide text-slate-600 ring-1 ring-slate-200">
                        {{ __('All Services') }}
                    </span>
                </td>
            </tr>
        </tfoot>
        </table>
        </div>
    </x-institutions.institution-layout>
</section>
