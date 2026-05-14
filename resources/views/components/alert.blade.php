@props([
    'type' => 'info',
    'dismissible' => false,
    'message' => '',
    'icon' => true,
    'size' => 'md', // sm, md, lg
])

@php
    // Base classes
    $baseClasses = 'rounded-xl border shadow-sm mb-4 backdrop-blur-sm';

    // Size classes
    $sizeClasses = [
        'sm' => 'px-3 py-2 text-sm',
        'md' => 'px-4 py-3 text-sm',
        'lg' => 'px-4 py-4 text-base',
    ];

    // Color schemes
    $alertClasses = [
        'success' =>
            'bg-emerald-50/80 border-emerald-100 text-emerald-800 dark:bg-emerald-950/30 dark:border-emerald-800/50 dark:text-emerald-200',
        'error' =>
            'bg-rose-50/80 border-rose-100 text-rose-800 dark:bg-rose-950/30 dark:border-rose-800/50 dark:text-rose-200',
        'warning' =>
            'bg-amber-50/80 border-amber-100 text-amber-800 dark:bg-amber-950/30 dark:border-amber-800/50 dark:text-amber-200',
        'info' =>
            'bg-sky-50/80 border-sky-100 text-sky-800 dark:bg-sky-950/30 dark:border-sky-800/50 dark:text-sky-200',
    ];

    $iconClasses = [
        'success' => 'text-emerald-600 dark:text-emerald-400',
        'error' => 'text-rose-600 dark:text-rose-400',
        'warning' => 'text-amber-600 dark:text-amber-400',
        'info' => 'text-sky-600 dark:text-sky-400',
    ];

    $icons = [
        'success' =>
            'M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z',
        'error' =>
            'M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z',
        'warning' =>
            'M8.485 3.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 3.495zM10 6a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 6zm0 9a1 1 0 100-2 1 1 0 000 2z',
        'info' =>
            'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z',
    ];

    $class = $baseClasses . ' ' . $sizeClasses[$size] . ' ' . ($alertClasses[$type] ?? $alertClasses['info']);
    $iconClass = $iconClasses[$type] ?? $iconClasses['info'];
    $iconPath = $icons[$type] ?? $icons['info'];
@endphp

<div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
    x-transition:leave-end="opacity-0 transform scale-95" class="{{ $class }}" role="alert" {{ $attributes }}>

    <div class="flex items-start gap-3">
        <!-- Icon -->
        @if ($icon)
            <div class="flex-shrink-0">
                <div
                    class="w-8 h-8 rounded-lg bg-white/50 dark:bg-gray-800/50 flex items-center justify-center shadow-xs">
                    <svg class="w-4 h-4 {{ $iconClass }}" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="{{ $iconPath }}" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
        @endif

        <!-- Content -->
        <div class="flex-1 min-w-0">
            <p class="font-medium leading-relaxed">
                {{ $message ?: $slot }}
            </p>
        </div>

        <!-- Dismiss Button -->
        @if ($dismissible)
            <div class="flex-shrink-0">
                <button @click="show = false" type="button"
                    class="inline-flex items-center justify-center w-7 h-7 rounded-lg transition-all duration-200
                           hover:bg-black/5 dark:hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-offset-2
                           {{ $iconClass }} focus:ring-current"
                    aria-label="Close alert">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                    </svg>
                </button>
            </div>
        @endif
    </div>
{{-- How to use examples:
    <!-- Basic usage -->
    <x-alert type="success" message="Operation completed successfully!" />

    <!-- With slot content -->
    <x-alert type="warning" dismissible>
        Your session will expire in 5 minutes.
    </x-alert>

    <!-- Different sizes (alternative version) -->
    <x-alert type="error" size="sm" message="Small error alert" />
    <x-alert type="info" size="lg" dismissible>
        Large informational alert with more content.
    </x-alert>

    <!-- Without icon -->
    <x-alert type="success" :icon="false" message="No icon version" /> --}}
</div>
