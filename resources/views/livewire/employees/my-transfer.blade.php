<div>
    @if (\App\Support\Transfer\TransferAccess::isSltsEmployee(auth()->user()))
    <livewire:transfer-module.teacher.index-teachers-module />
    @else
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 flex flex-col items-center justify-center bg-slate-50/30 dark:bg-zinc-800/20 border border-dashed border-slate-200 dark:border-zinc-700 rounded-3xl">
        <flux:icon name="exclamation-triangle" class="w-8 h-8 text-amber-500 mb-3" />
        <h3 class="text-sm font-bold text-slate-500">{{ __('This service is not available for you.') }}</h3>
    </div>
    @endif
</div>