<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    {{-- Professional Header --}}
    <x-page-header
        title="National Schools Overview"
        subtitle="Centralized management for the national school sector. Monitor institutional records, academic leadership, and administrative cadre."
        icon="building-library"
    />

    {{-- Vibrant Card Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
        @foreach($items as $item)
        @can($item['permission'])
        <a href="{{ $item['route'] ?? '#' }}" wire:navigate
            class="group relative overflow-hidden bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 hover:border-transparent rounded-[2.5rem] p-8 transition-all duration-500 hover:-translate-y-2 hover:shadow-[0_30px_70px_-20px_rgba(0,0,0,0.15)]">

            {{-- Gradient Border Effect --}}
            <div class="absolute inset-0 p-[1px] rounded-[2.5rem] bg-linear-to-br {{ $item['gradient'] }} opacity-0 group-hover:opacity-100 transition-opacity duration-500 -z-10"></div>
            <div class="absolute inset-[1px] rounded-[2.45rem] bg-white dark:bg-zinc-900 -z-10"></div>
            <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-linear-to-br {{ $item['gradient'] }} opacity-[0.03] group-hover:opacity-[0.1] transition-opacity duration-700 blur-3xl"></div>
            <div class="absolute -left-10 -bottom-10 h-40 w-40 rounded-full bg-linear-to-tr {{ $item['gradient'] }} opacity-[0.02] group-hover:opacity-[0.08] transition-opacity duration-700 blur-3xl"></div>

            {{-- Left Accent Strip --}}
            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-12 rounded-r-full bg-linear-to-b {{ $item['gradient'] }} opacity-0 group-hover:opacity-100 transition-all duration-500"></div>

            <div class="relative flex flex-col h-full">
                {{-- Icon Module with Glow --}}
                <div class="mb-8 relative">
                    <div class="absolute inset-0 bg-linear-to-br {{ $item['gradient'] }} opacity-20 blur-2xl group-hover:opacity-40 transition-opacity duration-500 rounded-full scale-150"></div>
                    <div class="relative inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-linear-to-br {{ $item['gradient'] }} shadow-lg {{ $item['shadow'] }} text-white transform transition-all duration-500 group-hover:scale-110 group-hover:rotate-3">
                        <flux:icon :icon="$item['icon']" variant="mini" class="w-8 h-8" />
                    </div>
                </div>

                {{-- Text Content --}}
                <div class="flex-1 space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white transition-colors duration-300">
                            {{ $item['label'] }}
                        </h3>
                        <div class="h-8 w-8 rounded-full bg-slate-50 dark:bg-zinc-800 flex items-center justify-center opacity-0 group-hover:opacity-100 -translate-x-4 group-hover:translate-x-0 transition-all duration-500 shadow-sm">
                            <flux:icon.arrow-right variant="micro" class="{{ $item['text'] }}" />
                        </div>
                    </div>

                    <p class="text-[15px] font-medium text-slate-500 dark:text-zinc-400 leading-relaxed group-hover:text-slate-600 dark:group-hover:text-zinc-300 transition-colors duration-300">
                        {{ $item['desc'] }}
                    </p>
                </div>

                {{-- Dynamic Bottom Bar --}}
                <div class="mt-8 pt-6 border-t border-slate-200 dark:border-zinc-700 flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-[0.2em] {{ $item['text'] }} opacity-60">Manage Sector</span>
                    <div class="flex -space-x-2">
                        <div class="h-2 w-8 rounded-full bg-linear-to-r {{ $item['gradient'] }} opacity-20 group-hover:opacity-100 transition-opacity duration-700"></div>
                    </div>
                </div>
            </div>
        </a>
        @endcan
        @endforeach
    </div>
</div>