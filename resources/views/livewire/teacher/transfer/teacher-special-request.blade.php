<div class="px-6 py-10 lg:px-12 max-w-7xl mx-auto space-y-12 pb-20">
    {{-- Header Section --}}
    <div class="space-y-4">
        <nav class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-indigo-500">
            <a href="{{ route('my-transfer.teacher-transfer-potal') }}" wire:navigate class="hover:text-indigo-600 transition-colors">Portal</a>
            <flux:icon.chevron-right variant="micro" class="h-3 w-3 text-slate-300" />
            <span class="text-slate-400">Special Request</span>
        </nav>

        <div class="space-y-1">
            <h1 class="text-3xl lg:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-tight">
                Special Transfer <span class="text-indigo-600 dark:text-indigo-500">Request</span>
            </h1>
            <p class="text-slate-500 dark:text-slate-400 font-medium max-w-2xl leading-relaxed">
                Dedicated movement pathway for humanitarian grounds including medical, security, and marital considerations.
            </p>
        </div>
    </div>

    {{-- Coming Soon Content --}}
    <div class="flex flex-col items-center justify-center py-20 px-6 bg-white dark:bg-slate-950 border border-slate-100 dark:border-slate-800/80 rounded-4xl shadow-sm">
        <div class="relative">
            <div class="absolute -inset-4 bg-indigo-500/10 dark:bg-indigo-500/20 rounded-full blur-2xl"></div>
            <div class="relative h-24 w-24 rounded-3xl bg-linear-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-xl shadow-indigo-200 dark:shadow-none mb-8">
                <flux:icon.sparkles variant="mini" class="h-12 w-12" />
            </div>
        </div>

        <div class="text-center space-y-4 max-w-md">
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">New Feature Coming Soon</h2>
            <p class="text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
                We are currently finalizing the specialized evaluation matrix for humanitarian requests. This module will be available in the upcoming phase of the 2026 cycle.
            </p>
        </div>

        <div class="mt-10 flex flex-col sm:flex-row gap-4">
            <flux:button href="{{ route('my-transfer.teacher-transfer-potal') }}" wire:navigate variant="subtle" size="sm" class="font-bold">
                Back to Portal
            </flux:button>
            <flux:button href="{{ route('my-transfer.teacher-annual-transfer') }}" wire:navigate variant="primary" size="sm" class="font-bold bg-indigo-600">
                Check Annual Transfer
            </flux:button>
        </div>
    </div>
</div>