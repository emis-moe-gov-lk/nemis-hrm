<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    {{-- Refined Professional Header --}}
    <x-page-header
        title="Teacher HRM Dashboard"
        subtitle="Centralized human resource management for education professionals. Monitor service records and milestones."
        icon="academic-cap"
        :breadcrumbs="[
            'Teacher' => route('teacher.overview')
        ]">
        <x-slot:actions>
            <flux:modal.trigger name="search-profile">
                <flux:button variant="subtle" icon="magnifying-glass"
                    class="h-11 bg-white! dark:bg-slate-900! shadow-sm">Search Teachers...</flux:button>
            </flux:modal.trigger>
            @can('teacher.create')
            <flux:button href="{{ route('teacher.create') }}" icon="plus"
                class="h-11 bg-indigo-600! hover:bg-indigo-700! text-white! shadow-lg shadow-indigo-200/50 dark:shadow-none border-none">
                Add Teacher
            </flux:button>
            @endcan
        </x-slot:actions>
    </x-page-header>

    {{-- Corporate Info Card Grid --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 lg:gap-8">
        {{-- Card Loop --}}
        @foreach($items as $item)

        <a href="{{ $item['route'] ?? '#' }}" wire:navigate
            class="group relative flex items-start gap-8 p-8 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/80 rounded-[2.5rem] transition-all duration-300 hover:shadow-2xl {{ $item['hover_shadow'] }} {{ $item['hover_border'] }} dark:hover:border-slate-800">

            {{-- Corporate Icon Module (Subtle Tint) --}}
            <div
                class="shrink-0 flex items-center justify-center w-16 h-16 rounded-3xl {{ $item['bg'] }} {{ $item['text'] }} {{ $item['hover_bg'] }} group-hover:text-white group-hover:scale-105 transition-all duration-300 shadow-sm {{ $item['hover_shadow'] }}">
                <flux:icon :icon="$item['icon']" variant="mini"
                    class="w-8 h-8 transition-transform group-hover:scale-110" />
            </div>

            {{-- Content Module --}}
            <div class="flex-1 space-y-2 py-1">
                <div class="flex items-center justify-between">
                    <h3
                        class="text-xl font-bold text-slate-800 dark:text-slate-100 {{ $item['text'] }} transition-colors">
                        {{ $item['label'] }}
                    </h3>
                    <div
                        class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-all duration-500 -translate-x-2 group-hover:translate-x-0">
                        <span class="text-[11px] font-bold uppercase tracking-wider {{ $item['text'] }}">Access</span>
                        <flux:icon.arrow-right variant="micro" class="{{ $item['text'] }}" />
                    </div>
                </div>

                <p
                    class="text-[14px] font-semibold text-slate-500 dark:text-slate-500 leading-snug group-hover:text-slate-600 dark:group-hover:text-slate-300 transition-colors">
                    {{ $item['desc'] }}
                </p>

                {{-- Bottom Interaction Bar --}}
                <div
                    class="pt-2 flex items-center gap-4 opacity-40 group-hover:opacity-100 transition-opacity duration-300">
                    <div class="h-1 flex-1 bg-slate-50 dark:bg-slate-800 rounded-full overflow-hidden">
                        <div
                            class="h-full {{ $item['accent'] }} w-0 group-hover:w-full transition-all duration-1000 ease-out">
                        </div>
                    </div>
                </div>
            </div>
        </a>

        @endforeach
    </div>

    {{-- Search Flyout --}}
    <flux:modal name="search-profile" variant="flyout" class="space-y-6">
        <flux:heading size="lg" class="flex items-center gap-2">
            <flux:icon.magnifying-glass variant="mini" /> Search Registry
        </flux:heading>

        {{-- Use .live to trigger search on every keystroke --}}
        <flux:input wire:model.live.debounce.300ms="query" placeholder="Type name or NIC..."
            class="rounded-xl! shadow-sm" clearable />

        <div class="space-y-2 mt-4">
            @if(!empty($query))
            @forelse($results as $teacher)
            <a href="{{ route('teacher.profile.index', $teacher->id) }}"
                class="flex items-center gap-4 p-4 rounded-2xl hover:bg-indigo-50 dark:hover:bg-indigo-900/10 border border-transparent hover:border-indigo-100">
                <div class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold">
                    {{ substr($teacher->name_with_initials, 0, 1) }}
                </div>
                <div>
                    <p class="font-bold text-slate-900 dark:text-white leading-tight">{{ $teacher->name_with_initials }}
                    </p>
                    <p class="text-xs text-slate-500">{{ $teacher->nic }}</p>
                    <p class="text-xs text-indigo-400">{{ $teacher->currentAppointment?->workplace?->office()?->name }}
                    </p>
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