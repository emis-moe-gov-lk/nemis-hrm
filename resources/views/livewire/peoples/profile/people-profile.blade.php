<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6 min-h-screen">
    @php
        $name = $person->name_with_initials ?: ($person->full_name ?: 'Unnamed Person');
        $initials = strtoupper(substr($name, 0, 2));
        $permanentAddress = collect([
            $person->address_line1,
            $person->address_line2,
            $person->address_line3,
            $person->postal_code,
        ])
            ->filter()
            ->implode(', ');
        $temporaryAddress = collect([
            $person->t_address_line1,
            $person->t_address_line2,
            $person->t_address_line3,
            $person->t_postal_code,
        ])
            ->filter()
            ->implode(', ');
    @endphp

    <x-page-header title="People Profile" subtitle="NIC: {{ $person->nic ?: 'NIC not listed' }}" icon="user"
        :breadcrumbs="[
            'People' => route('peoples.list'),
            'Profile' => route('peoples.profile.view', $person->id),
        ]">
        <x-slot:actions>
            <flux:button href="{{ route('peoples.list') }}" wire:navigate variant="subtle" icon="arrow-left"
                class="h-11 bg-white! dark:bg-zinc-900! shadow-sm">
                Back
            </flux:button>

            @if ($canEdit)
                <flux:button wire:click="confirmDelete" variant="danger" icon="trash" class="h-11">
                    Delete Person
                </flux:button>
            @endif
        </x-slot:actions>
    </x-page-header>

    <section
        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="h-28 bg-linear-to-r from-indigo-600 via-cyan-600 to-emerald-600"></div>

        <div class="px-5 pb-6 sm:px-8">
            <div class="-mt-10 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
                    <div
                        class="h-24 w-24 rounded-2xl border-4 border-white bg-linear-to-br from-indigo-600 to-cyan-600 flex items-center justify-center text-2xl font-black text-white shadow-lg dark:border-zinc-900">
                        {{ $initials }}
                    </div>

                    <div class="pb-1">
                        <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                            {{ $name }}
                        </h1>
                    </div>
                </div>

                <span
                    class="inline-flex w-fit items-center gap-1 rounded-full border px-3 py-1 text-xs font-bold {{ $person->active_status ? 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'border-slate-300 bg-slate-100 text-slate-600 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300' }}">
                    <flux:icon.check-circle variant="micro" />
                    {{ $person->active_status ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>
    </section>

    @if ($canEdit)
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950/30 dark:text-emerald-300">
            <div class="flex items-start gap-3">
                <flux:icon.pencil-square variant="micro" class="mt-0.5 shrink-0" />
                <p>This person has no employment record, so profile details can be edited.</p>
            </div>
        </div>
    @else
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm font-semibold text-amber-800 dark:border-amber-800 dark:bg-amber-950/30 dark:text-amber-300">
            <div class="flex items-start gap-3">
                <flux:icon.lock-closed variant="micro" class="mt-0.5 shrink-0" />
                <p>This person already has an employment record. Profile editing is locked here.</p>
            </div>
        </div>

        {{-- Employment Details Card --}}
        @php
            $appt   = $person->currentAppointment ?? $person->appointment;
            $baseAppt = $person->appointment;
        @endphp

        @if($appt)
        <section class="overflow-hidden rounded-2xl border border-indigo-200 bg-white shadow-sm dark:border-indigo-800 dark:bg-zinc-900">
            {{-- Header --}}
            <div class="flex items-center gap-3 bg-linear-to-r from-indigo-600 to-cyan-600 px-5 py-3">
                <flux:icon.briefcase class="h-5 w-5 text-white" />
                <h2 class="text-sm font-black uppercase tracking-wider text-white">Employment Details</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-0 divide-y sm:divide-y-0 sm:divide-x divide-slate-100 dark:divide-zinc-800 p-0">

                {{-- Service --}}
                <div class="flex flex-col gap-1 p-5">
                    <p class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        <flux:icon.academic-cap variant="micro" class="shrink-0" />
                        Service
                    </p>
                    <p class="text-sm font-bold text-slate-800 dark:text-white">
                        {{ $appt->service->service_name ?? ($baseAppt?->service?->service_name ?? '—') }}
                    </p>
                    <span class="mt-0.5 inline-flex w-fit items-center rounded-full bg-indigo-50 dark:bg-indigo-900/30 px-2 py-0.5 text-[10px] font-bold text-indigo-600 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                        {{ $appt->rank?->rank_name ?? ($baseAppt?->rank?->rank_name ?? '—') }}
                    </span>
                </div>

                {{-- Position --}}
                <div class="flex flex-col gap-1 p-5">
                    <p class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        <flux:icon.identification variant="micro" class="shrink-0" />
                        Position
                    </p>
                    <p class="text-sm font-bold text-slate-800 dark:text-white">
                        {{ $appt->position?->position_name ?? ($baseAppt?->position?->position_name ?? '—') }}
                    </p>
                </div>

                {{-- Working Place --}}
                <div class="flex flex-col gap-1 p-5">
                    <p class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        <flux:icon.building-office-2 variant="micro" class="shrink-0" />
                        Working Place
                    </p>
                    <p class="text-sm font-bold text-slate-800 dark:text-white">
                        {{ $appt->workplace?->office_name ?? ($baseAppt?->workplace?->office_name ?? '—') }}
                    </p>
                    @if($appt->workplace?->workplace_id ?? $baseAppt?->workplace_id)
                        <span class="mt-0.5 inline-flex w-fit items-center rounded-full bg-cyan-50 dark:bg-cyan-900/30 px-2 py-0.5 text-[10px] font-bold text-cyan-600 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-800">
                            {{ $appt->workplace?->workplace_id ?? ($baseAppt?->workplace_id ?? '—') }}
                        </span>
                    @endif
                </div>
            </div>
        </section>
        @endif
    @endif

    @error('delete')
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700 dark:border-red-800 dark:bg-red-950/30 dark:text-red-300">
            <div class="flex items-start gap-3">
                <flux:icon.x-circle variant="micro" class="mt-0.5 shrink-0" />
                <p>{{ $message }}</p>
            </div>
        </div>
    @enderror

    <div class="space-y-6">
        {{-- 1. Personal & Socio-Cultural Details --}}
        <livewire:employees.personal-cultural :peopleId="$person->people_id" :canEdit="$canEdit" />
    </div>

    {{-- Family Details Card --}}
    @php
        $allFamilies = $person->familiesAsHusband->merge($person->familiesAsWife);
    @endphp

    @if($allFamilies->isNotEmpty())
    <section class="overflow-hidden rounded-2xl border border-rose-200 bg-white shadow-sm dark:border-rose-800 dark:bg-zinc-900">
        {{-- Header --}}
        <div class="flex items-center gap-3 bg-linear-to-r from-rose-500 to-pink-500 px-5 py-3">
            <flux:icon.heart class="h-5 w-5 text-white" />
            <h2 class="text-sm font-black uppercase tracking-wider text-white">Family Details</h2>
        </div>

        <div class="divide-y divide-slate-100 dark:divide-zinc-800">
            @foreach($allFamilies as $family)
            @php
                $isHusband = $person->familiesAsHusband->contains('id', $family->id);
                $spouse    = $isHusband ? $family->memberB : $family->memberA;
                $myRole    = $isHusband ? 'Husband' : 'Wife';
            @endphp

            <div class="p-5 space-y-4">
                {{-- Family ID + Role Badge --}}
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-xs font-bold text-slate-500 dark:text-zinc-400">
                            {{ $family->family_id }}
                        </span>
                        <span class="inline-flex items-center rounded-full border border-rose-200 bg-rose-50 px-2.5 py-0.5 text-[10px] font-bold text-rose-600 dark:border-rose-800 dark:bg-rose-900/30 dark:text-rose-300">
                            {{ $myRole }}
                        </span>
                        @if($family->active_status)
                            <span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-[10px] font-bold text-emerald-600 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300">
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-100 px-2.5 py-0.5 text-[10px] font-bold text-slate-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-400">
                                Inactive
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Spouse + Marriage Info Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                    {{-- Spouse Name --}}
                    <div class="flex flex-col gap-1">
                        <p class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            <flux:icon.user variant="micro" class="shrink-0" />
                            Spouse Name
                        </p>
                        <p class="text-sm font-bold text-slate-800 dark:text-white">
                            {{ $spouse?->name_with_initials ?: ($spouse?->full_name ?: '—') }}
                        </p>
                        @if($spouse?->nic)
                            <span class="mt-0.5 inline-flex w-fit items-center rounded-full bg-slate-100 dark:bg-zinc-800 px-2 py-0.5 text-[10px] font-bold text-slate-500 dark:text-zinc-400 border border-slate-200 dark:border-zinc-700">
                                {{ $spouse->nic }}
                            </span>
                        @endif
                    </div>

                    {{-- Marriage Date --}}
                    <div class="flex flex-col gap-1">
                        <p class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            <flux:icon.calendar-days variant="micro" class="shrink-0" />
                            Married Date
                        </p>
                        <p class="text-sm font-bold text-slate-800 dark:text-white">
                            {{ $family->married_date ? \Carbon\Carbon::parse($family->married_date)->format('d M Y') : '—' }}
                        </p>
                        @if($family->married_cf_no)
                            <span class="mt-0.5 text-xs text-slate-400">CF No: {{ $family->married_cf_no }}</span>
                        @endif
                    </div>

                    {{-- Divorce Date --}}
                    <div class="flex flex-col gap-1">
                        <p class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            <flux:icon.x-circle variant="micro" class="shrink-0" />
                            Divorce Date
                        </p>
                        <p class="text-sm font-bold {{ $family->divorce_date ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400 dark:text-zinc-500' }}">
                            {{ $family->divorce_date ? \Carbon\Carbon::parse($family->divorce_date)->format('d M Y') : '—' }}
                        </p>
                    </div>

                    {{-- Children Count --}}
                    <div class="flex flex-col gap-1">
                        <p class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            <flux:icon.users variant="micro" class="shrink-0" />
                            Children
                        </p>
                        <p class="text-sm font-bold text-slate-800 dark:text-white">
                            {{ $family->children->count() }} {{ Str::plural('child', $family->children->count()) }}
                        </p>
                    </div>
                </div>

                {{-- Children Table --}}
                @if($family->children->isNotEmpty())
                <div class="mt-2 overflow-hidden rounded-xl border border-slate-200 dark:border-zinc-700">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-zinc-800 text-left">
                                <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">#</th>
                                <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">Child Name</th>
                                <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">Date of Birth</th>
                                <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">Gender</th>
                                <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">Health</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
                            @foreach($family->children as $index => $child)
                            <tr class="hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="px-4 py-2.5 font-mono text-xs text-slate-400">{{ $index + 1 }}</td>
                                <td class="px-4 py-2.5 font-semibold text-slate-800 dark:text-white">
                                    {{ $child->child_name ?: '—' }}
                                </td>
                                <td class="px-4 py-2.5 text-slate-600 dark:text-zinc-300">
                                    {{ $child->date_of_birth ? \Carbon\Carbon::parse($child->date_of_birth)->format('d M Y') : '—' }}
                                </td>
                                <td class="px-4 py-2.5 text-slate-600 dark:text-zinc-300">
                                    {{ $child->gender?->gender_name ?? '—' }}
                                </td>
                                <td class="px-4 py-2.5">
                                    @if($child->health_condition)
                                        <span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-600 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300">Good</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full border border-amber-200 bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-600 dark:border-amber-800 dark:bg-amber-900/30 dark:text-amber-300">Not Healthy</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <section class="rounded-2xl border border-slate-300 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <h2 class="flex items-center gap-2 text-sm font-black uppercase tracking-wider text-slate-500">
            <flux:icon.shield-check variant="micro" />
            System
        </h2>

        <div class="mt-5">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">NIC Hash</p>
                <p class="mt-1 break-all font-mono text-xs font-semibold text-slate-700 dark:text-zinc-300">
                    {{ $person->nic_hash ?? 'Not listed' }}</p>
            </div>
        </div>
    </section>

    {{-- Delete Confirmation Modal --}}
    <flux:modal wire:model="showDeleteModal" class="max-w-md">
        <div class="space-y-5 p-2">
            {{-- Header --}}
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/40">
                    <flux:icon.trash class="h-5 w-5 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Delete Person</h3>
                    <p class="text-sm text-slate-500 dark:text-zinc-400">This action cannot be undone.</p>
                </div>
            </div>

            {{-- Warning text --}}
            <p class="text-sm text-slate-700 dark:text-zinc-300">
                Are you sure you want to permanently delete
                <span class="font-bold">{{ $person->name_with_initials ?: $person->full_name }}</span>?
                All related data will be removed.
            </p>

            {{-- Password Confirmation --}}
            <div class="space-y-1.5">
                <label for="delete-password" class="block text-xs font-bold text-slate-600 dark:text-zinc-300">
                    Confirm your password
                </label>

                <div class="relative" x-data="{ show: false }">
                    <input
                        id="delete-password"
                        wire:model="deletePassword"
                        :type="show ? 'text' : 'password'"
                        placeholder="Enter your current password"
                        autocomplete="current-password"
                        class="h-10 w-full rounded-xl border px-4 pr-11 text-sm font-medium shadow-sm outline-none transition
                            @error('deletePassword')
                                border-red-400 bg-red-50 text-red-900 ring-2 ring-red-400/20 placeholder:text-red-400
                                dark:border-red-600 dark:bg-red-950/20 dark:text-red-300
                            @else
                                border-slate-300 bg-white text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 placeholder:text-slate-400
                                dark:border-zinc-700 dark:bg-zinc-900 dark:text-white dark:placeholder:text-zinc-500
                            @enderror"
                        wire:keydown.enter="deletePerson" />

                    {{-- Show/hide password toggle --}}
                    <button
                        type="button"
                        @click="show = !show"
                        class="absolute right-2 top-1/2 -translate-y-1/2 h-7 w-7 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-zinc-200 transition-colors"
                        tabindex="-1"
                        :aria-label="show ? 'Hide password' : 'Show password'">
                        <flux:icon.eye x-show="!show" variant="micro" />
                        <flux:icon.eye-slash x-show="show" variant="micro" />
                    </button>
                </div>

                @error('deletePassword')
                    <p class="flex items-center gap-1.5 text-xs font-semibold text-red-600 dark:text-red-400">
                        <flux:icon.exclamation-circle variant="micro" class="shrink-0" />
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-3 pt-1">
                <flux:button
                    wire:click="$set('showDeleteModal', false)"
                    variant="subtle">
                    Cancel
                </flux:button>
                <flux:button
                    wire:click="deletePerson"
                    wire:loading.attr="disabled"
                    variant="danger"
                    icon="trash">
                    <span wire:loading.remove wire:target="deletePerson">Yes, Delete</span>
                    <span wire:loading wire:target="deletePerson">Verifying...</span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
