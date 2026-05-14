<div class="container mx-left px-4 py-8 antialiased">
    <div class="max-w-3xl mx-left"> {{-- Constraining width makes hierarchy look better --}}
        
        {{-- Header Section --}}
        <div class="mb-10 text-center lg:text-left">
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                Office Hierarchy
            </h2>
            <p class="text-sm font-medium text-slate-500 mt-1">
                Sri Lanka Education System Administrative Structure
            </p>
        </div>

        {{-- Hierarchy Track --}}
        <div class="relative space-y-0">
            {{-- Vertical Line (The Path) --}}
            <div class="absolute left-[19px] top-2 h-[calc(100%-40px)] w-0.5 bg-gradient-to-b from-indigo-500 via-slate-200 to-slate-200 dark:via-slate-700 dark:to-slate-800"></div>

            @foreach ($officeHierarchy as $index => $level)
                @php
                    $isFirst = $index === 0;
                    $isLast = $index === count($officeHierarchy) - 1;
                    
                    $label = 'Level ' . ($index + 1);
                    if ($isFirst) $label = 'Central Policy';
                    elseif ($isLast) $label = 'Service Point';

                    // Color logic for nodes
                    $nodeColor = $isFirst ? 'bg-indigo-600 ring-indigo-100' : ($isLast ? 'bg-emerald-500 ring-emerald-100' : 'bg-white border-2 border-slate-300 ring-slate-50');
                @endphp

                <div class="relative flex items-start gap-6 pb-10 group">
                    {{-- The Node (Circle) --}}
                    <div class="relative z-10 flex items-center justify-center">
                        <div class="h-10 w-10 rounded-full {{ $nodeColor }} ring-4 flex items-center justify-center transition-transform duration-300 group-hover:scale-110">
                            @if($isFirst)
                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            @elseif($isLast)
                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            @else
                                <div class="h-2 w-2 rounded-full bg-slate-400"></div>
                            @endif
                        </div>
                    </div>

                    {{-- Content Card --}}
                    <div class="flex-1 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-5 shadow-sm transition-all duration-200 hover:shadow-md hover:border-indigo-200">
                        <div class="flex flex-wrap justify-between items-start gap-2">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-indigo-600 dark:text-indigo-400">
                                    {{ $label }}
                                </span>
                                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mt-0.5">
                                    {{ $level->office_level_name }}
                                </h3>
                            </div>
                            
                            <div class="flex items-center gap-1.5 bg-slate-50 dark:bg-slate-800 px-3 py-1 rounded-full border border-slate-100 dark:border-slate-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                <span class="text-xs font-bold text-slate-600 dark:text-slate-300">
                                    {{ $level->workplaces_count }} <span class="font-medium text-slate-400">Units</span>
                                </span>
                            </div>
                        </div>
                        
                        {{-- Optional Action/Details --}}
                        <div class="mt-4 flex items-center justify-between">
                             <a href="#" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1">
                                View Workplaces
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                             </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Note Footer --}}
        <div class="mt-8 p-4 bg-indigo-50/50 dark:bg-slate-800/50 rounded-xl border border-indigo-100/50 dark:border-slate-700">
            <div class="flex gap-3">
                <svg class="h-5 w-5 text-indigo-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-xs leading-relaxed text-slate-600 dark:text-slate-400">
                    <strong>Note:</strong> The Ministry of Education (MOE) operates at the central level. Provincial, Zonal, and Divisional offices represent decentralized administrative tiers responsible for regional implementation.
                </p>
            </div>
        </div>
    </div>
</div>