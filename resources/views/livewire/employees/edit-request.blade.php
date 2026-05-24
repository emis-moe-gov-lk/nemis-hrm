<div>
    <div class="mx-auto mb-20 px-4">
        <div class="flow-root">
            <ul role="list" class="-mb-8">
                @forelse ($editRequests as $editRequest)
                    @php
                        // 1. Determine Status Logic
                        $statusText = $editRequest->getStatusTextAttribute();
                        $statusLower = strtolower($statusText);
                        
                        $isRejected = str_contains($statusLower, 'reject');
                        $isPending = str_contains($statusLower, 'pending');
                        // Default to emerald/green if not pending or rejected
                        $isApproved = !$isRejected && !$isPending;

                        // 2. Define Dynamic Color Classes
                        $accentColor = $isPending ? 'amber' : ($isRejected ? 'red' : 'emerald');
                    @endphp

                    <li>
                        <div class="relative pb-12">
                            {{-- Vertical Timeline Line --}}
                            @if (!$loop->last)
                                <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                            @endif

                            <div class="relative flex items-start space-x-4">
                                {{-- Status Icon Indicator --}}
                                <div class="relative px-1">
                                    <div @class([
                                        'h-8 w-8 rounded-full ring-8 ring-white dark:ring-gray-900 flex items-center justify-center shadow-sm text-white',
                                        'bg-amber-500' => $isPending,
                                        'bg-red-500' => $isRejected,
                                        'bg-emerald-500' => $isApproved,
                                    ])>
                                        @if($isPending)
                                            <flux:icon name="clock" size="xs" variant="solid" />
                                        @elseif($isRejected)
                                            <flux:icon name="x-mark" size="xs" variant="solid" />
                                        @else
                                            <flux:icon name="check-circle" size="xs" variant="solid" />
                                        @endif
                                    </div>
                                </div>

                                {{-- Unified Card Container --}}
                                <div class="min-w-0 flex-1">
                                    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden">
                                        
                                        {{-- Card Header --}}
                                        <div class="bg-gray-50 dark:bg-gray-800/50 px-4 py-2 border-b border-gray-200 dark:border-gray-700 flex flex-wrap items-center justify-between gap-1">
                                            <span class="text-xs font-bold text-gray-600 dark:text-gray-400 tracking-wider">
                                                {{ $editRequest->complaint_request_ref }}
                                            </span>
                                            <span class="text-[10px] italic text-gray-400">
                                                {{ $editRequest->created_ago }}
                                            </span>
                                        </div>

                                        {{-- Upper Card: User's Message --}}
                                        <div class="p-4">
                                            <div class="flex items-center gap-2 mb-3">
                                                <flux:badge size="sm" color="blue" variant="flat" class="uppercase text-[10px] font-bold">User Request</flux:badge>
                                            </div>
                                            
                                            <div class="space-y-2">
                                                @if(isset($editRequest->requested_changes['subject']))
                                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                                                        {{ $editRequest->requested_changes['subject'] }}
                                                    </h4>
                                                @endif
                                                
                                                @if(isset($editRequest->requested_changes['complaint']))
                                                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                                        {{ $editRequest->requested_changes['complaint'] }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Lower Card: Reviewer Reply (Separate Card Section) --}}
                                        @if(!empty($editRequest->review_comments))
                                            <div @class([
                                                'border-t border-dashed border-gray-200 dark:border-gray-700 p-4',
                                                'bg-red-50/30 dark:bg-red-900/10' => $isRejected,
                                                'bg-emerald-50/30 dark:bg-emerald-900/10' => $isApproved,
                                            ])>
                                                <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Decision:</span>
                                                        {{-- Decision Badge Color matches Status --}}
                                                        <flux:badge size="sm" :color="$isRejected ? 'red' : 'emerald'" variant="solid" class="font-bold">
                                                            {{ $statusText }}
                                                        </flux:badge>
                                                    </div>
                                                    <span class="text-[10px] text-gray-400 italic">{{ $editRequest->updated_ago }}</span>
                                                </div>

                                                {{-- Reply Content with matching side-border --}}
                                                <div @class([
                                                    'relative pl-4 border-l-2',
                                                    'border-red-500' => $isRejected,
                                                    'border-emerald-500' => $isApproved,
                                                ])>
                                                    <p @class([
                                                        'text-sm italic leading-relaxed',
                                                        'text-red-900 dark:text-red-200' => $isRejected,
                                                        'text-emerald-900 dark:text-emerald-200' => $isApproved,
                                                    ])>
                                                        "{{ $editRequest->review_comments }}"
                                                    </p>
                                                    
                                                    @if($editRequest->reviewer)
                                                        <div class="mt-4 flex items-center gap-2 opacity-70">
                                                            <div @class([
                                                                'h-5 w-5 rounded-full flex items-center justify-center',
                                                                'bg-red-200 dark:bg-red-800' => $isRejected,
                                                                'bg-emerald-200 dark:bg-emerald-800' => $isApproved,
                                                            ])>
                                                                <flux:icon name="user" size="micro" class="text-current" />
                                                            </div>
                                                            <p class="text-[10px] uppercase font-bold tracking-widest text-current">
                                                                {{ $editRequest->reviewer->title->title_name ?? '' }} {{ $editRequest->reviewer->name_with_initials ?? '' }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            {{-- Pending Footer --}}
                                            <div class="px-4 py-3 bg-amber-50/30 dark:bg-amber-900/10 border-t border-amber-100 dark:border-amber-900/20">
                                                <p class="text-xs text-amber-700 dark:text-amber-500 flex items-center gap-2 font-medium">
                                                    <flux:icon name="clock" size="micro" variant="solid" />
                                                    Pending review by administration...
                                                </p>
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                @empty
                    <div class="flex flex-col items-center justify-center py-20 border-2 border-dashed border-gray-200 dark:border-gray-800 rounded-3xl text-center">
                        <flux:icon name="document-magnifying-glass" class="text-gray-300 dark:text-gray-600 mb-4" size="xl" />
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">{{ __('No requests found') }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Your history will appear here once you make a request.') }}</p>
                    </div>
                @endforelse
            </ul>
        </div>
    </div>
</div>