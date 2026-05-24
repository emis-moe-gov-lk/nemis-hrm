@php
if (! isset($scrollTo)) {
$scrollTo = 'body';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
? <<<JS
    (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
    JS
    : '' ;
    @endphp

    <div>
    @if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between border-t border-slate-300 dark:border-slate-700 px-6 sm:px-0 mt-8">
        {{-- Previous Button --}}
        <div class="-mt-px flex w-0 flex-1">
            @if ($paginator->onFirstPage())
            <span class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-slate-300 dark:text-slate-600 cursor-not-allowed">
                <svg class="mr-3 h-5 w-5 text-slate-300 dark:text-slate-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M18 10a.75.75 0 01-.75.75H4.66l2.1 1.95a.75.75 0 11-1.02 1.1l-3.5-3.25a.75.75 0 010-1.1l3.5-3.25a.75.75 0 111.02 1.1l-2.1 1.95h12.59A.75.75 0 0118 10z" clip-rule="evenodd" />
                </svg>
                Previous
            </span>
            @else
            <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 dark:text-slate-500 dark:hover:border-slate-600 dark:hover:text-slate-200 transition-colors focus:outline-none">
                <svg class="mr-3 h-5 w-5 text-slate-500 dark:text-slate-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M18 10a.75.75 0 01-.75.75H4.66l2.1 1.95a.75.75 0 11-1.02 1.1l-3.5-3.25a.75.75 0 010-1.1l3.5-3.25a.75.75 0 111.02 1.1l-2.1 1.95h12.59A.75.75 0 0118 10z" clip-rule="evenodd" />
                </svg>
                Previous
            </button>
            @endif
        </div>

        {{-- Pages --}}
        <div class="hidden md:-mt-px md:flex md:justify-center overflow-x-auto hide-scrollbar max-w-[60%]">
            @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
            <span class="inline-flex items-center border-t-2 border-transparent px-2 pt-4 text-sm font-medium text-slate-500">
                {{ $element }}
            </span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
            @foreach ($element as $page => $url)
            @if ($page == $paginator->currentPage())
            <span class="inline-flex items-center border-t-2 border-indigo-500 px-3 pt-4 text-sm font-bold text-indigo-600 dark:text-indigo-400" aria-current="page">
                {{ $page }}
            </span>
            @else
            <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="inline-flex items-center border-t-2 border-transparent px-3 pt-4 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 dark:text-slate-500 dark:hover:border-slate-600 dark:hover:text-slate-200 transition-colors focus:outline-none">
                {{ $page }}
            </button>
            @endif
            @endforeach
            @endif
            @endforeach
        </div>

        {{-- Next Button --}}
        <div class="-mt-px flex w-0 flex-1 justify-end">
            @if ($paginator->hasMorePages())
            <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" class="inline-flex items-center border-t-2 border-transparent pl-1 pt-4 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 dark:text-slate-500 dark:hover:border-slate-600 dark:hover:text-slate-200 transition-colors focus:outline-none">
                Next
                <svg class="ml-3 h-5 w-5 text-slate-500 dark:text-slate-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M2 10a.75.75 0 01.75-.75h12.59l-2.1-1.95a.75.75 0 111.02-1.1l3.5 3.25a.75.75 0 010 1.1l-3.5 3.25a.75.75 0 11-1.02-1.1l2.1-1.95H2.75A.75.75 0 012 10z" clip-rule="evenodd" />
                </svg>
            </button>
            @else
            <span class="inline-flex items-center border-t-2 border-transparent pl-1 pt-4 text-sm font-medium text-slate-300 dark:text-slate-600 cursor-not-allowed">
                Next
                <svg class="ml-3 h-5 w-5 text-slate-300 dark:text-slate-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M2 10a.75.75 0 01.75-.75h12.59l-2.1-1.95a.75.75 0 111.02-1.1l3.5 3.25a.75.75 0 010 1.1l-3.5 3.25a.75.75 0 11-1.02-1.1l2.1-1.95H2.75A.75.75 0 012 10z" clip-rule="evenodd" />
                </svg>
            </span>
            @endif
        </div>
    </nav>
    @endif
    </div>