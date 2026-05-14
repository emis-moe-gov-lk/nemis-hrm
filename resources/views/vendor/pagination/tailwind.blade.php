@if ($paginator->hasPages())
<nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between py-4 px-2">
    {{-- Mobile View --}}
    <div class="flex justify-between flex-1 sm:hidden">
        @if ($paginator->onFirstPage())
            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-slate-400 bg-white border border-slate-200 cursor-default rounded-xl dark:bg-slate-900 dark:border-slate-800">
                {!! __('pagination.previous') !!}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-200 rounded-xl hover:text-indigo-600 transition ease-in-out duration-150 dark:bg-slate-900 dark:border-slate-800 dark:text-slate-300">
                {!! __('pagination.previous') !!}
            </a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-slate-700 bg-white border border-slate-200 rounded-xl hover:text-indigo-600 transition ease-in-out duration-150 dark:bg-slate-900 dark:border-slate-800 dark:text-slate-300">
                {!! __('pagination.next') !!}
            </a>
        @else
            <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-slate-400 bg-white border border-slate-200 cursor-default rounded-xl dark:bg-slate-900 dark:border-slate-800">
                {!! __('pagination.next') !!}
            </span>
        @endif
    </div>

    {{-- Desktop View --}}
    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between gap-4">
        <div class="shrink-0">
            <p class="text-sm text-slate-500 dark:text-slate-400 whitespace-nowrap">
                {!! __('Showing') !!}
                <span class="font-bold text-slate-900 dark:text-white">{{ $paginator->firstItem() }}</span>
                {!! __('to') !!}
                <span class="font-bold text-slate-900 dark:text-white">{{ $paginator->lastItem() }}</span>
                {!! __('of') !!}
                <span class="font-bold text-slate-900 dark:text-white">{{ $paginator->total() }}</span>
                {!! __('results') !!}
            </p>
        </div>

        <div class="flex flex-wrap justify-end">
            <span class="relative z-0 inline-flex gap-1.5 flex-wrap justify-center">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                        <span class="relative inline-flex items-center px-2.5 py-2 text-sm font-medium text-slate-300 bg-white border border-slate-200 cursor-default rounded-xl dark:bg-slate-900 dark:border-slate-800 dark:text-slate-600" aria-hidden="true">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-2.5 py-2 text-sm font-medium text-slate-500 bg-white border border-slate-200 rounded-xl hover:text-indigo-600 hover:border-indigo-300 transition dark:bg-slate-900 dark:border-slate-800 dark:text-slate-400" aria-label="{{ __('pagination.previous') }}">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span aria-disabled="true">
                            <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-slate-400 bg-white border border-transparent cursor-default dark:bg-slate-900 dark:text-slate-600">{{ $element }}</span>
                        </span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page">
                                    <span class="relative inline-flex items-center px-3.5 py-2 text-sm font-bold text-white bg-indigo-600 border border-indigo-600 rounded-xl shadow-sm shadow-indigo-100 dark:shadow-none">{{ $page }}</span>
                                </span>
                            @else
                                <a href="{{ $url }}" class="relative inline-flex items-center px-3.5 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:text-indigo-600 hover:border-indigo-300 transition dark:bg-slate-900 dark:border-slate-800 dark:text-slate-400" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-2.5 py-2 text-sm font-medium text-slate-500 bg-white border border-slate-200 rounded-xl hover:text-indigo-600 hover:border-indigo-300 transition dark:bg-slate-900 dark:border-slate-800 dark:text-slate-400" aria-label="{{ __('pagination.next') }}">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @else
                    <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                        <span class="relative inline-flex items-center px-2.5 py-2 text-sm font-medium text-slate-300 bg-white border border-slate-200 cursor-default rounded-xl dark:bg-slate-900 dark:border-slate-800 dark:text-slate-600" aria-hidden="true">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </span>
                @endif
            </span>
        </div>
    </div>
</nav>
@endif