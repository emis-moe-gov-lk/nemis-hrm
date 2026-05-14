<div class="min-h-screen bg-slate-50/50 p-4 antialiased">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <header class="max-w-7xl mx-left mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Promotion Management</h1>
            <p class="text-slate-500 text-sm">Review and authorize teacher grade advancements.</p>
        </div>
    </header>

    <section class="max-w-7xl mx-left mb-6">
        <div
            class="bg-white p-3 rounded-2xl border border-slate-200 shadow-sm flex flex-col lg:flex-row gap-3 items-center">

            <div class="relative flex-1 w-full">
                <i
                    class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm z-10"></i>
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Search by Email, NIC or Phone..."
                    class="w-full pl-11 pr-4 py-2.5 bg-slate-50/50 border-transparent focus:bg-white focus:ring-2 focus:ring-indigo-500 rounded-xl text-sm transition-all outline-none" />
            </div>

            <div class="flex flex-wrap md:flex-nowrap gap-2 w-full lg:w-auto">
                <flux:select wire:model.live="institutionFilter" class="flex-1 min-w-[140px]">
                    <flux:select.option value="">All Institution</flux:select.option>
                    @foreach ($institutions as $institution)
                    <flux:select.option value="{{ $institution->workplace_id }}">{{ $institution->name }}
                    </flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.live="gradeFilter" class="flex-1 min-w-[140px]">
                    <flux:select.option value="">All Grades</flux:select.option>
                    @foreach ($ranks as $rank)
                    <flux:select.option value="{{ $rank->rank_id }}">Grade {{ $rank->rank_name }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:button wire:click="resetFilters" icon="arrow-path" variant="subtle" class="aspect-square" />
            </div>
        </div>
    </section>

    <main class="max-w-7xl mx-left flex flex-col gap-3" id="cardContainer">
        @forelse($employees as $employee)
        @php
        $currentRank = $employee->currentAppointment->rank->rank_name ?? 'N/A';
        $nextRank = $employee->currentAppointment->rank->nextRank()->rank_name ?? null;
        $status = $nextRank ? 'Pending' : 'Not Eligible';
        @endphp

        <div class="promotion-row group bg-white rounded-2xl border border-slate-200 p-4 shadow-sm hover:border-indigo-300 transition-all flex flex-col lg:flex-row lg:items-center gap-4 lg:gap-6"
            data-name="{{ $employee->name_with_initials }}" data-id="{{ $employee->nic }}"
            data-grade="{{ $currentRank }}" data-status="{{ $status }}">

            <div class="flex items-center gap-4 lg:w-1/4 shrink-0">
                <div class="relative shrink-0">
                    <img class="h-12 w-12 rounded-2xl object-cover border-2 border-white shadow-md"
                        src="{{ $employee->gender_id == 'G02' ? asset('images/profile_f.png') : asset('images/profile_m.png') }}">
                    <span
                        class="absolute -bottom-1 -right-1 block h-3.5 w-3.5 rounded-full ring-2 ring-white {{ $employee->currentAppointment?->appointment?->is_confirmed ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                </div>
                <div class="truncate">
                    <h3 class="font-bold text-slate-900 leading-tight truncate">{{ $employee->name_with_initials }}</h3>
                    <p class="text-[10px] text-slate-400 font-bold tracking-wider uppercase">{{ $employee->nic }}</p>
                </div>
            </div>

            <div class="flex flex-col lg:w-1/4">
                <p class="text-xs font-bold text-slate-700 truncate">
                    {{ $employee->currentAppointment?->position?->position_name ?? 'N/A' }}
                </p>
                <p class="text-[11px] text-slate-500 truncate flex items-center gap-1">
                    <i class="fa-solid fa-building-circle-check text-[9px]"></i>
                    {{ $employee->currentAppointment?->workplace?->office()?->name ?? 'No Workplace' }}
                </p>
            </div>

            <div class="flex items-center gap-3 lg:w-1/4 bg-slate-50 px-4 py-2 rounded-xl border border-slate-100">
                <div class="flex-1 text-center">
                    <p class="text-[9px] font-black text-slate-400 uppercase">Current</p>
                    <span class="text-[11px] font-bold text-slate-600">{{ $currentRank }}</span>
                </div>
                <i class="fa-solid fa-arrow-right text-slate-300 text-[10px]"></i>
                <div class="flex-1 text-center">
                    <p class="text-[9px] font-black text-slate-400 uppercase">Next</p>
                    <span class="text-[11px] font-bold text-indigo-600">{{ $nextRank ?? 'End' }}</span>
                </div>
            </div>

            <div class="flex items-center justify-between lg:justify-end gap-3 lg:w-1/4 lg:ml-auto">
                <div class="hidden xl:block text-right mr-4">
                    <p class="text-[9px] font-black text-slate-300 uppercase">Current Service Period</p>
                    <p class="text-xs font-bold text-slate-700">
                        {{ $employee->currentAppointment->service_years ?? '0' }}
                    </p>
                </div>

                @if ($nextRank)
                <div class="flex gap-2 w-full lg:w-auto">
                    <button wire:click="promoteTeacher({{ $employee->id }})"
                        class="flex-1 lg:flex-none px-5 py-2.5 bg-indigo-600 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-100 hover:bg-indigo-700 transition-all active:scale-95">
                        <i
                            class="fa-solid fa-turn-up text-[10px] group-hover/btn:-translate-y-0.5 transition-transform"></i>
                        <span class="text-[10px] font-bold uppercase tracking-tight">Promote</span>
                    </button>
                </div>
                @else
                <span
                    class="w-full lg:w-auto text-center px-4 py-2 bg-slate-100 text-slate-400 rounded-xl text-[10px] font-bold uppercase tracking-tight">
                    Max Grade
                </span>
                @endif


                @can('teacher.promotion.demote')
                <button
                    onclick="openModal('{{ $employee->name_with_initials }}', '{{ $employee->nic }}', '{{ $currentRank }}', 'Demotion')"
                    class="flex items-center gap-3 px-3 py-2 text-slate-400 border border-transparent hover:border-red-100 hover:bg-red-50 hover:text-red-600 rounded-xl transition-all group/btn">
                    <i
                        class="fa-solid fa-turn-down text-[10px] group-hover/btn:-translate-y-0.5 transition-transform"></i>
                    <span class="text-[10px] font-bold uppercase tracking-tight">Demote</span>
                </button>
                @endcan

            </div>
        </div>
        @empty
        <div class="bg-white border border-dashed border-slate-300 rounded-3xl py-12 text-center">
            <i class="fa-solid fa-user-slash text-slate-200 text-4xl mb-3"></i>
            <p class="text-slate-500 font-medium">No records matching your search.</p>
        </div>
        @endforelse

        <div class="mt-8">{{ $employees->links() }}</div>
    </main>

    <flux:modal wire:model="showModalPromoteTeacher" name="promote-teacher" class="p-0 overflow-hidden !max-w-2xl">
        <div class="px-8 pt-8 pb-6 text-center">
            <flux:heading size="xl" class="text-slate-900 font-bold tracking-tight">Teacher Promotion</flux:heading>
            <flux:subheading class="text-slate-500">Official grade transition for the current academic year
            </flux:subheading>
        </div>

        <form wire:submit.prevent="updateTeacherPromotion" class="px-8 pb-8 space-y-8">

            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100 flex items-center gap-4">
                <div class="relative shrink-0">
                    <img class="w-16 h-16 rounded-2xl object-cover border-2 border-white shadow-sm"
                        src="{{ $teacher?->gender_id == 'G02' ? asset('images/profile_f.png') : asset('images/profile_m.png') }}">
                    <span
                        class="absolute -top-1 -right-1 block h-3 w-3 rounded-full ring-2 ring-slate-50 {{ $teacher?->currentAppointment?->appointment?->is_confirmed ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                </div>

                <div class="flex-1">
                    <h3 class="font-bold text-slate-900 leading-tight">{{$teacher?->name_with_initials}}</h3>
                    <div class="flex items-center gap-2 mt-1">
                        <span
                            class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{$teacher?->nic}}</span>
                        <span class="text-slate-200">•</span>
                    </div>
                </div>

                <div class="text-right">
                    <p class="text-[10px] uppercase font-bold tracking-widest text-slate-400">Total Service</p>
                    <p class="text-sm font-bold text-slate-700">
                        {{ $teacher?->appointment->service_years ?? '0' }}
                    </p>
                </div>
            </div>

            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100 flex items-center gap-4">
                <div class="flex-1">
                    <h3 class="font-bold text-slate-900 leading-tight">
                        {{$teacher?->currentAppointment?->workplace?->office()?->name}}
                    </h3>
                    <div class="flex items-center gap-2 mt-1">
                        <span
                            class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{$teacher?->currentAppointment?->workplace?->office()?->address}}</span>
                        <span class="text-slate-200">•</span>
                    </div>
                </div>

                <div class="text-right">
                    <p class="text-[10px] uppercase font-bold tracking-widest text-slate-400">On this Institution</p>
                    <p class="text-sm font-bold text-slate-700">
                        {{ $teacher?->currentAppointment->service_years ?? '0' }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6">

                <div
                    class="flex items-center justify-between gap-4 p-6 bg-white border border-slate-200 rounded-2xl shadow-sm">
                    <div class="flex-1 text-center">
                        <p class="text-[10px] uppercase font-bold tracking-widest text-slate-400 mb-1">Current</p>
                        <span class="text-lg font-bold text-slate-400 line-through decoration-slate-300">
                            {{ $teacher?->currentAppointment->rank->rank_name }}
                        </span>
                    </div>

                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-slate-900 text-white">
                        <flux:icon.arrow-right class="size-5" />
                    </div>

                    <div class="flex-1">
                        <p class="text-[10px] uppercase font-bold tracking-widest text-slate-900 mb-1 text-center">New
                            Grade</p>
                        <flux:select wire:model.live="promotion_grade" placeholder="Select Grade">
                            @foreach ($ranks as $rank)
                            <flux:select.option value="{{ $rank->rank_id }}">{{ $rank->rank_name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label class="text-[11px] uppercase font-bold tracking-wider text-slate-500">Letter
                            Reference</flux:label>
                        <flux:input wire:model.live="promotion_letter_no" placeholder="ED/2026/PM/045"
                            class="!bg-white" />
                    </flux:field>

                    <flux:field>
                        <flux:label class="text-[11px] uppercase font-bold tracking-wider text-slate-500">Effective Date
                        </flux:label>
                        <flux:input type="date" wire:model.live="promotion_effect_date" class="!bg-white" />
                    </flux:field>
                </div>

            </div>

            <div class="flex items-center justify-between px-8 py-6 bg-slate-50 border-t border-slate-100">
                <flux:modal.close>
                    <button type="button"
                        class="text-sm font-semibold text-slate-500 hover:text-slate-800 transition-colors">
                        Cancel
                    </button>
                </flux:modal.close>

                <flux:button type="submit"
                    class="!bg-slate-900 hover:!bg-black !text-white px-8 py-2.5 rounded-xl shadow-lg shadow-slate-200">
                    <span class="text-xs font-bold uppercase tracking-widest">Complete Promotion</span>
                </flux:button>
            </div>
        </form>


    </flux:modal>
</div>