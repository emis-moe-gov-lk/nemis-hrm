<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8 min-h-screen">
    <x-page-header
        title="People Directory"
        subtitle="People records from the central registry."
        icon="users"
        :count="$employees->total()"
        countLabel="People Registered"
        :breadcrumbs="[
            'People' => route('peoples.list'),
            'Directory' => route('peoples.list')
        ]" />

    <div class="flex flex-col gap-4 p-4 bg-white dark:bg-zinc-900/50 border border-slate-300 dark:border-zinc-700 rounded-2xl shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-sm font-bold text-slate-900 dark:text-white">Registry records</p>
                <p class="text-xs text-slate-500 dark:text-zinc-400">Showing records from the people table only.</p>
            </div>

            <flux:modal.trigger name="search-people">
                <flux:button
                    variant="filled"
                    icon="magnifying-glass"
                    class="w-full sm:w-auto h-10 bg-slate-50! hover:bg-indigo-50! dark:bg-zinc-800! text-slate-700! hover:text-indigo-600! dark:text-slate-300! dark:hover:text-indigo-400! border border-slate-300 dark:border-zinc-700 shadow-sm">
                    Search People
                </flux:button>
            </flux:modal.trigger>
        </div>

        {{-- Employment Filter --}}
        <div class="flex flex-wrap items-center gap-2 border-t border-slate-100 dark:border-zinc-800 pt-3">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400 shrink-0">
                Employment:
            </span>

            @foreach([
                'all'     => ['label' => 'All',              'icon' => 'users',       'color' => 'indigo'],
                'with'    => ['label' => 'With Employment',  'icon' => 'briefcase',   'color' => 'emerald'],
                'without' => ['label' => 'No Employment',    'icon' => 'user-minus',  'color' => 'amber'],
            ] as $value => $option)
                <label
                    for="emp-filter-{{ $value }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border cursor-pointer select-none transition-all duration-150
                        {{ $employmentFilter === $value
                            ? ($option['color'] === 'indigo'  ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm' :
                              ($option['color'] === 'emerald' ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' :
                                                               'bg-amber-500 text-white border-amber-500 shadow-sm'))
                            : 'bg-white dark:bg-zinc-800 text-slate-600 dark:text-zinc-300 border-slate-300 dark:border-zinc-700 hover:border-indigo-300 dark:hover:border-indigo-600' }}">
                    <input
                        type="radio"
                        id="emp-filter-{{ $value }}"
                        wire:model.live="employmentFilter"
                        value="{{ $value }}"
                        class="sr-only" />
                    <flux:icon.{{ $option['icon'] }} variant="micro" />
                    {{ $option['label'] }}
                </label>
            @endforeach
        </div>
    </div>


    <div class="space-y-4">
        @forelse($employees as $employee)
            @php
                $name = $employee->name_with_initials ?: ($employee->full_name ?: 'Unnamed Person');
                $initials = strtoupper(substr($name, 0, 2));
                $statusClass = $employee->active_status
                    ? 'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-700'
                    : 'bg-slate-100 text-slate-600 border-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600';
            @endphp

            <div class="group relative overflow-hidden bg-white dark:bg-zinc-900 border border-slate-300 dark:border-zinc-700 hover:border-indigo-300 dark:hover:border-indigo-600 rounded-2xl p-5 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xl flex flex-col lg:flex-row lg:items-center gap-5">
                <div class="flex items-center gap-4 flex-1 min-w-0">
                    <div class="relative shrink-0">
                        <div class="h-14 w-14 rounded-2xl bg-linear-to-br from-indigo-600 to-cyan-600 flex items-center justify-center text-white text-lg font-extrabold shadow-md select-none">
                            {{ $initials }}
                        </div>
                        <span class="absolute -bottom-1 -right-1 h-4 w-4 rounded-full {{ $employee->active_status ? 'bg-emerald-500' : 'bg-slate-400' }} ring-2 ring-white dark:ring-zinc-900"></span>
                    </div>

                    <div class="min-w-0">
                        <h3 class="font-extrabold text-slate-900 dark:text-white text-base leading-tight truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                            {{ $name }}
                        </h3>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold border bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800 tracking-wide">
                                <flux:icon.credit-card variant="micro" />
                                {{ $employee->nic ?? 'NIC not listed' }}
                            </span>

                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $statusClass }} tracking-wide">
                                <flux:icon.check-circle variant="micro" />
                                {{ $employee->active_status ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 w-full lg:w-[28rem]">
                    <a href="mailto:{{ $employee->email }}" class="flex items-center gap-2 text-sm text-slate-600 dark:text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors min-w-0">
                        <flux:icon.envelope variant="micro" class="shrink-0 text-slate-500" />
                        <span class="truncate">{{ $employee->email ?? 'Email not provided' }}</span>
                    </a>

                    <a href="tel:{{ $employee->phone }}" class="flex items-center gap-2 text-sm text-slate-600 dark:text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors min-w-0">
                        <flux:icon.phone variant="micro" class="shrink-0 text-slate-500" />
                        <span class="truncate">{{ $employee->phone ?? 'Phone not provided' }}</span>
                    </a>
                </div>

                <div class="w-full lg:w-56 lg:border-l border-slate-200 dark:border-zinc-700 lg:pl-5">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Address</p>
                    <p class="text-sm font-semibold text-slate-700 dark:text-zinc-300 truncate">
                        {{ collect([$employee->address_line1, $employee->address_line2, $employee->address_line3, $employee->postal_code])->filter()->implode(', ') ?: 'Address not listed' }}
                    </p>
                </div>

                <div class="w-full lg:w-auto border-t lg:border-t-0 border-slate-200 dark:border-zinc-700 pt-4 lg:pt-0">
                    <flux:button
                        href="{{ route('peoples.profile.view', $employee->id) }}"
                        wire:navigate
                        size="sm"
                        icon="eye"
                        class="w-full lg:w-auto rounded-xl! bg-indigo-600! hover:bg-indigo-700! text-white! border-none font-bold">
                        View
                    </flux:button>
                </div>
            </div>
        @empty
            <div class="py-20 flex flex-col items-center justify-center bg-white dark:bg-zinc-900 border-2 border-dashed border-slate-300 dark:border-zinc-700 rounded-[2rem]">
                <div class="relative p-6 bg-slate-50 dark:bg-zinc-800 rounded-full shadow-inner mb-6">
                    <flux:icon.users class="w-16 h-16 text-slate-400" />
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-slate-200">No People Found</h3>
                <p class="text-slate-500 mt-2 max-w-xs text-center font-medium">There are no people records to display.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-10">{{ $employees->links() }}</div>

    <flux:modal name="search-people" variant="flyout" class="space-y-6">
        <flux:heading size="lg" class="flex items-center gap-2">
            <flux:icon.magnifying-glass variant="mini" /> Search People
        </flux:heading>

        <form wire:submit.prevent="search" class="flex items-center gap-2">
            <div class="relative flex-1 min-w-0">
                <input
                    type="text"
                    wire:model="query"
                    placeholder="Type name, NIC, people ID, email or phone..."
                    class="h-10 w-full rounded-xl border border-slate-300 bg-white px-4 pr-11 text-sm font-medium text-slate-900 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 placeholder:text-slate-400 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white dark:placeholder:text-zinc-500" />

                @if(!empty($query))
                    <button
                        type="button"
                        wire:click="clearSearch"
                        aria-label="Clear search"
                        title="Clear search"
                        class="absolute right-2 top-1/2 z-10 -translate-y-1/2 h-7 w-7 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors">
                        <flux:icon.x-mark variant="micro" />
                    </button>
                @endif
            </div>

            <flux:button
                type="submit"
                icon="magnifying-glass"
                aria-label="Search"
                title="Search"
                class="h-10 w-10! px-0! shrink-0 bg-indigo-600! hover:bg-indigo-700! text-white! border-none" />
        </form>

        <div class="space-y-2 mt-4">
            @if(!empty($query))
                @forelse($results as $person)
                    <a href="{{ route('peoples.profile.view', $person->id) }}" wire:navigate class="flex items-center gap-4 p-4 rounded-2xl hover:bg-indigo-50 dark:hover:bg-indigo-900/10 border border-transparent hover:border-indigo-100 dark:hover:border-indigo-900 transition-all">
                        <div class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold">
                            {{ strtoupper(substr($person->name_with_initials ?: $person->full_name ?: 'P', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-900 dark:text-white leading-tight truncate">
                                {{ $person->name_with_initials ?: $person->full_name ?: 'Unnamed Person' }}
                            </p>
                            <p class="text-xs text-slate-500">{{ $person->people_id }} · {{ $person->nic ?? 'NIC not listed' }}</p>
                            <p class="text-xs text-indigo-400 truncate">{{ $person->email ?? $person->phone ?? 'Contact not provided' }}</p>
                        </div>
                    </a>
                @empty
                    <p class="text-center text-slate-500 text-sm italic">No results found for "{{ $query }}"</p>
                @endforelse
            @else
                <p class="text-center text-slate-500 text-sm">Start typing to see results...</p>
            @endif
        </div>
    </flux:modal>
</div>
