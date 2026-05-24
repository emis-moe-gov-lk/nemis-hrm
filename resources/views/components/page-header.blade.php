@props([
    'title',
    'subtitle' => null,
    'breadcrumbs' => [],
    'icon' => 'building-office',
    'count' => null,
    'countLabel' => 'Profiles Registered',
])

<div class="space-y-6">
    @if(count($breadcrumbs) > 0)
    <nav class="flex" aria-label="Breadcrumb">
        <ol role="list" class="flex items-center space-x-4">
            <li>
                <div>
                    <a href="{{ route('dashboard') }}" class="text-slate-500 hover:text-indigo-500 dark:text-zinc-400 dark:hover:text-indigo-400 transition-colors">
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M9.293 2.293a1 1 0 011.414 0l7 7A1 1 0 0117 11h-1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-3a1 1 0 00-1-1H9a1 1 0 00-1 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-6H3a1 1 0 01-.707-1.707l7-7z" clip-rule="evenodd" />
                        </svg>
                        <span class="sr-only">Home</span>
                    </a>
                </div>
            </li>
            @foreach($breadcrumbs as $label => $link)
            @php
                $canAccess = true;
                if ($link && $link !== '#' && !str_starts_with($link, 'javascript:')) {
                    try {
                        $routeRequest = request()->create($link);
                        $route = app('router')->getRoutes()->match($routeRequest);
                        $middlewares = $route->gatherMiddleware();
                        
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
                        // Default to true for external links or unresolvable URLs
                    }
                }
            @endphp
            <li>
                <div class="flex items-center">
                    <svg class="h-5 w-5 shrink-0 text-slate-300 dark:text-zinc-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                    </svg>
                    @if($loop->last)
                    <span class="ml-4 text-sm font-extrabold text-indigo-600 dark:text-indigo-400" aria-current="page">{{ $label }}</span>
                    @elseif(!$canAccess)
                    <span class="ml-4 text-sm font-medium text-slate-400 dark:text-zinc-500 cursor-not-allowed" title="Access Denied">{{ $label }}</span>
                    @else
                    <a href="{{ $link }}" class="ml-4 text-sm font-medium text-slate-500 hover:text-slate-700 dark:text-zinc-400 dark:hover:text-zinc-300 transition-colors">{{ $label }}</a>
                    @endif
                </div>
            </li>
            @endforeach
        </ol>
    </nav>
    @endif

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-8 border-b border-slate-200 dark:border-slate-700">
        <div class="space-y-1 max-w-3xl">
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                {{ $title }}
            </h1>
            <p class="text-slate-500 dark:text-slate-500 font-medium flex items-center gap-2">
                <span class="p-1 bg-indigo-100 dark:bg-indigo-900/30 rounded text-indigo-600 dark:text-indigo-400">
                    <flux:icon name="{{ $icon }}" variant="mini" class="w-4 h-4" />
                </span>
                {{ $subtitle }}
                @if($count !== null)
                <span class="text-slate-300 dark:text-zinc-700 mx-1">|</span>
                <span class="text-indigo-600 dark:text-indigo-400 font-bold">{{ $count }} {{ $countLabel }}</span>
                @endif
            </p>
        </div>

        <div class="flex flex-wrap items-center justify-end gap-3 w-full md:w-auto">
            {{ $actions ?? '' }}
        </div>
    </div>
</div>
