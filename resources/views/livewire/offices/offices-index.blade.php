<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    {{-- Refined Professional Header --}}
    <x-page-header
        title="Administrative Hierarchy"
        subtitle="Explore the organizational structure of the Sri Lanka Education System."
        icon="building-office-2"
        :breadcrumbs="[
            'Administrative Hierarchy' => route('offices.index')
        ]" />

    {{-- Hierarchy Track --}}
    <div class="relative space-y-0 pl-2">
        {{-- Vertical Line (The Path) --}}
        <div class="absolute left-[31px] top-4 h-[calc(100%-60px)] w-px bg-linear-to-b from-indigo-500 via-purple-400 to-emerald-400 opacity-20"></div>

        @php
            $people = auth()->user()->people ?? null;
            $userWorkplace = ($people && $people->currentAppointment && $people->currentAppointment->workplace)
                ? $people->currentAppointment->workplace
                : null;
            
            $userOfficeLevelRank = null;
            if ($userWorkplace) {
                $userOfficeLevel = \App\Models\OfficeLevel::where('office_level_id', $userWorkplace->office_level_id)->first();
                if ($userOfficeLevel) {
                    $userOfficeLevelRank = $userOfficeLevel->office_level_rank;
                }
            }
        @endphp

        @foreach ($officeHierarchy as $index => $level)
        @php
        $isFirst = $index === 0;
        $isLast = $index === count($officeHierarchy) - 1;

        $label = 'Level ' . ($index + 1);
        if ($isFirst) $label = 'Central Policy';
        elseif ($isLast) $label = 'Service Point';

        $styles = [
        'OLID001' => ['gradient' => 'from-indigo-600 to-blue-700', 'shadow' => 'shadow-indigo-500/10', 'text' => 'text-indigo-600', 'icon' => 'building-library'],
        'OLID002' => ['gradient' => 'from-blue-600 to-cyan-600', 'shadow' => 'shadow-blue-500/10', 'text' => 'text-blue-600', 'icon' => 'building-office-2'],
        'OLID003' => ['gradient' => 'from-cyan-600 to-teal-600', 'shadow' => 'shadow-cyan-500/10', 'text' => 'text-cyan-600', 'icon' => 'building-office'],
        'OLID004' => ['gradient' => 'from-teal-600 to-emerald-600', 'shadow' => 'shadow-teal-500/10', 'text' => 'text-teal-600', 'icon' => 'map-pin'],
        'OLID005' => ['gradient' => 'from-emerald-600 to-green-600', 'shadow' => 'shadow-emerald-500/10', 'text' => 'text-emerald-600', 'icon' => 'map'],
        'OLID006' => ['gradient' => 'from-orange-600 to-amber-600', 'shadow' => 'shadow-orange-500/10', 'text' => 'text-orange-600', 'icon' => 'academic-cap'],
        ];

        $currentStyle = $styles[$level->office_level_id] ?? ['gradient' => 'from-slate-600 to-slate-700', 'shadow' => 'shadow-slate-500/10', 'text' => 'text-slate-600', 'icon' => 'stop'];

        $routeMap = [
        'OLID001' => 'offices.moe.list',
        'OLID002' => 'offices.pmoe.list',
        'OLID003' => 'offices.peo.list',
        'OLID004' => 'offices.zeo.list',
        'OLID005' => 'offices.deo.list',
        'OLID006' => 'offices.institutions.list',
        ];

        $route = isset($routeMap[$level->office_level_id]) ? route($routeMap[$level->office_level_id]) : '#';

        $canAccess = true;
        if ($userOfficeLevelRank !== null && $level->office_level_rank < $userOfficeLevelRank) {
            $canAccess = false;
        } else {
            if ($route && $route !== '#' && !str_starts_with($route, 'javascript:')) {
                try {
                    $routeRequest = request()->create($route);
                    $routeObj = app('router')->getRoutes()->match($routeRequest);
                    $middlewares = $routeObj->gatherMiddleware();
                    
                    foreach ($middlewares as $middleware) {
                        if (is_string($middleware)) {
                            if (str_starts_with($middleware, 'permission:')) {
                                $perms = explode('|', str_replace('permission:', '', $middleware));
                                $hasPerm = false;
                                foreach ($perms as $perm) {
                                    if (auth()->user() && auth()->user()->can(trim($perm))) {
                                        $hasPerm = true;
                                        break;
                                    }
                                }
                                if (!$hasPerm) {
                                    $canAccess = false;
                                    break;
                                }
                            } elseif (str_starts_with($middleware, 'role:')) {
                                $roles = explode('|', str_replace('role:', '', $middleware));
                                $hasRole = false;
                                foreach ($roles as $role) {
                                    if (auth()->user() && auth()->user()->hasRole(trim($role))) {
                                        $hasRole = true;
                                        break;
                                    }
                                }
                                if (!$hasRole) {
                                    $canAccess = false;
                                    break;
                                }
                            } elseif (str_starts_with($middleware, 'role_or_permission:')) {
                                $items = explode('|', str_replace('role_or_permission:', '', $middleware));
                                $hasAccess = false;
                                foreach ($items as $item) {
                                    $trimmed = trim($item);
                                    if (auth()->user() && (auth()->user()->hasRole($trimmed) || auth()->user()->can($trimmed))) {
                                        $hasAccess = true;
                                        break;
                                    }
                                }
                                if (!$hasAccess) {
                                    $canAccess = false;
                                    break;
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // Default to true
                }
            }
        }
        @endphp

        <div class="relative flex items-start gap-12 pb-14 group">
            {{-- The Node (Circle) --}}
            <div class="relative z-10 flex items-center justify-center shrink-0">
                <div class="h-16 w-16 rounded-2xl bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 shadow-xl flex items-center justify-center transition-all duration-500 group-hover:scale-110 group-hover:rotate-6 ring-8 ring-slate-50 dark:ring-zinc-900/50">
                    <div class="h-12 w-12 rounded-xl bg-linear-to-br {{ $canAccess ? $currentStyle['gradient'] : 'from-slate-400 to-slate-500' }} flex items-center justify-center text-white shadow-lg {{ $canAccess ? $currentStyle['shadow'] : 'shadow-slate-500/10' }}">
                        @if($canAccess)
                        <flux:icon :icon="$currentStyle['icon']" variant="mini" class="w-6 h-6" />
                        @else
                        <flux:icon name="lock-closed" variant="mini" class="w-6 h-6" />
                        @endif
                    </div>
                </div>
            </div>

            {{-- Content Card --}}
            <div class="flex-1 bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 rounded-4xl p-8 shadow-sm transition-all duration-500 {{ $canAccess ? 'hover:shadow-[0_30px_60px_-15px_rgba(0,0,0,0.1)] hover:border-transparent group-hover:-translate-y-1' : 'opacity-85' }} relative overflow-hidden">
                {{-- Subtle Background Glow --}}
                @if($canAccess)
                <div class="absolute inset-0 bg-linear-to-br {{ $currentStyle['gradient'] }} opacity-0 group-hover:opacity-[0.03] transition-opacity duration-500"></div>
                @endif

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] font-bold uppercase tracking-widest {{ $canAccess ? $currentStyle['text'] : 'text-slate-400' }} opacity-80">
                                {{ $label }}
                            </span>
                            @if($isFirst)
                            <span class="px-2 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-[9px] font-black uppercase tracking-tighter text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800">Policy Center</span>
                            @endif
                        </div>
                        <h3 class="text-2xl font-bold {{ $canAccess ? 'text-slate-900 dark:text-white' : 'text-slate-400 dark:text-zinc-500' }} leading-tight">
                            {{ $level->office_level_name }}
                        </h3>
                    </div>

                    <div class="flex items-center gap-6">
                        <div class="flex flex-col items-end">
                            <span class="text-3xl font-extrabold {{ $canAccess ? 'text-slate-900 dark:text-white' : 'text-slate-400 dark:text-zinc-500' }}">{{ number_format($level->workplaces_count) }}</span>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Total Units</span>
                        </div>
                        <div class="h-12 w-px bg-slate-100 dark:bg-zinc-800 hidden md:block"></div>
                        @if($canAccess)
                        <a href="{{ $route }}" wire:navigate
                            class="h-12 w-12 rounded-xl bg-slate-50 dark:bg-zinc-800 flex items-center justify-center text-slate-500 group-hover:bg-linear-to-br group-hover:{{ $currentStyle['gradient'] }} group-hover:text-white transition-all duration-500 shadow-inner">
                            <flux:icon.arrow-right variant="mini" class="w-6 h-6" />
                        </a>
                        @else
                        <div class="h-12 w-12 rounded-xl bg-slate-100 dark:bg-zinc-800/50 flex items-center justify-center text-slate-400 dark:text-zinc-600 cursor-not-allowed shadow-inner" title="Access Denied">
                            <flux:icon name="lock-closed" variant="mini" class="w-5 h-5" />
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Card Footer Action --}}
                <div class="mt-8 pt-6 border-t border-slate-200 dark:border-zinc-700 flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-500 dark:text-zinc-400">
                        {{ __('Centralised management of') }} {{ strtolower($level->office_level_name) }}
                    </p>
                    @if($canAccess)
                    <a href="{{ $route }}" wire:navigate class="text-sm font-bold {{ $currentStyle['text'] }} hover:underline transition-all">
                        {{ __('Open Registry') }}
                    </a>
                    @else
                    <span class="text-sm font-bold text-slate-400 dark:text-zinc-500 flex items-center gap-1 cursor-not-allowed">
                        <flux:icon name="lock-closed" variant="micro" class="w-4 h-4" />
                        {{ __('Registry Locked') }}
                    </span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- System Note --}}
    <div class="p-10 bg-linear-to-br from-indigo-50 to-blue-50 dark:from-zinc-900 dark:to-zinc-800/50 rounded-4xl border border-indigo-100 dark:border-zinc-700 relative overflow-hidden group">
        <div class="absolute -right-10 -bottom-10 h-40 w-40 bg-indigo-500 opacity-[0.03] rounded-full blur-3xl group-hover:opacity-[0.08] transition-opacity duration-700"></div>
        <div class="flex gap-8 relative z-10">
            <div class="h-14 w-14 rounded-2xl bg-white dark:bg-zinc-900 shadow-sm flex items-center justify-center text-indigo-500 shrink-0 border border-indigo-50">
                <flux:icon name="information-circle" class="w-8 h-8" />
            </div>
            <div class="space-y-3">
                <h4 class="text-lg font-bold text-slate-900 dark:text-white">Administrative Note</h4>
                <p class="text-base leading-relaxed text-slate-600 dark:text-slate-500 font-medium max-w-3xl">
                    The hierarchy represents the formal flow of authority from the **Central Ministry** down to **Local Institutions**. Each tier manages specific administrative functions ensuring policy consistency across the national education sector.
                </p>
            </div>
        </div>
    </div>
</div>