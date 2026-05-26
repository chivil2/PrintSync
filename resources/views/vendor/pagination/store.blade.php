@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between gap-4">
        
        {{-- Results Info --}}
        <div class="text-sm text-gray-600">
            Showing 
            <span class="font-semibold text-gray-900">{{ $paginator->firstItem() ?? 0 }}</span> 
            to 
            <span class="font-semibold text-gray-900">{{ $paginator->lastItem() ?? 0 }}</span> 
            of 
            <span class="font-semibold text-gray-900">{{ $paginator->total() }}</span> 
            results
        </div>

        {{-- Pagination Buttons --}}
        <div class="flex items-center gap-2">
            {{-- Previous --}}
            @if (!$paginator->onFirstPage())
                <a href="{{ $paginator->previousPageUrl() }}" 
                   class="flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 text-gray-600 hover:border-[#F47C3C] hover:text-[#F47C3C] hover:bg-orange-50 transition-colors"
                   aria-label="Previous page">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
            @else
                <span class="flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 text-gray-300 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </span>
            @endif

            {{-- Page Numbers --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="flex items-center justify-center w-10 h-10 text-gray-400">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="flex items-center justify-center w-10 h-10 rounded-lg bg-[#F47C3C] text-white font-semibold">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" 
                               class="flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 text-gray-600 hover:border-[#F47C3C] hover:text-[#F47C3C] hover:bg-orange-50 transition-colors font-medium">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" 
                   class="flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 text-gray-600 hover:border-[#F47C3C] hover:text-[#F47C3C] hover:bg-orange-50 transition-colors"
                   aria-label="Next page">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            @else
                <span class="flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 text-gray-300 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            @endif
        </div>
    </nav>
@endif
