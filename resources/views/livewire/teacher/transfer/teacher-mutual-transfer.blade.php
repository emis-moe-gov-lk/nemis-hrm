<div class="px-4 sm:px-6 lg:px-8 py-8 max-w-7xl mx-auto space-y-8 pb-20">
    {{-- Header Section --}}
    <div class="space-y-4">
        <nav class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-indigo-500">
            <a href="{{ route('my-transfer') }}" wire:navigate class="hover:text-indigo-600 transition-colors">Portal</a>
            <flux:icon.chevron-right variant="micro" class="h-3 w-3 text-slate-300" />
            <span class="text-slate-500">Mutual Transfer</span>
        </nav>

        <div class="space-y-1">
            <h1 class="text-3xl lg:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-tight">
                Mutual Transfer <span class="text-indigo-600 dark:text-indigo-500">Service</span>
            </h1>
            <p class="text-slate-500 dark:text-slate-500 font-medium max-w-2xl leading-relaxed">
                Direct position swaps between professionals of the same grade and subject. Coordinate your movement with colleagues across the national school network.
            </p>
        </div>
    </div>

    {{-- Coming Soon Content --}}
    <div class="flex flex-col items-center justify-center py-20 px-6 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-700/80 rounded-4xl shadow-sm">
        <div class="relative">
            <div class="absolute -inset-4 bg-indigo-500/10 dark:bg-indigo-500/20 rounded-full blur-2xl"></div>
            <div class="relative h-24 w-24 rounded-3xl bg-linear-to-br from-indigo-600 to-emerald-500 flex items-center justify-center text-white shadow-xl shadow-indigo-200 dark:shadow-none mb-8">
                <flux:icon.user-group variant="mini" class="h-12 w-12" />
            </div>
        </div>

        <div class="text-center space-y-4 max-w-md">
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Mutual Transfer Coming Soon</h2>
            <p class="text-slate-500 dark:text-slate-500 font-medium leading-relaxed">
                Our peer-matching algorithm and coordination console are currently in the final testing phase. This module will allow you to find and request position swaps starting later this cycle.
            </p>
        </div>

        <div class="mt-10 flex flex-col sm:flex-row gap-4">
            <flux:button href="{{ route('my-transfer') }}" wire:navigate variant="subtle" size="sm" class="font-bold">
                Back to Portal
            </flux:button>
            <flux:button href="{{ route('my-transfer.teacher-annual-transfer') }}" wire:navigate variant="primary" size="sm" class="font-bold bg-indigo-600">
                Go to Annual Transfer
            </flux:button>
        </div>
    </div>
</div>