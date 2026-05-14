<div>
    <section>
        {{-- Unified Header --}}
        <div class="flex items-center justify-between mb-5 px-1">
            <div>
                <h2 class="text-xl font-extrabold tracking-tight text-gray-900 dark:text-white">Educational Qualifications</h2>
                <p class="text-sm text-gray-500">Academic background and certifications</p>
            </div>
            @if($canCreate)
                <flux:modal.trigger name="add-qualification">
                    <flux:button variant="ghost" icon="plus" class="rounded-full">Add New</flux:button>
                </flux:modal.trigger>
            @endif
        </div>

        {{-- Main Content Area --}}
        <div class="space-y-4">
            @forelse ($qualificationList as $data)
                {{-- Desktop & Mobile Card Wrapper --}}
                <div class="group relative bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition-all">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        
                        <div class="flex items-start gap-4">
                            {{-- Academic Icon --}}
                            <div class="p-3 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl text-indigo-600 dark:text-indigo-400">
                                <flux:icon.academic-cap class="size-6" />
                            </div>

                            <div class="min-w-0">
                                <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-gray-100 leading-snug">
                                    {{ $data->qualification->qualification }}
                                </h3>
                                <p class="text-xs text-gray-500 mt-0.5 flex items-center gap-1">
                                    <flux:icon.building-library class="size-3" />
                                    {{ $data->institution }}
                                </p>
                                
                                {{-- Description Tag (Mobile Optimized) --}}
                                @if($data->description)
                                    <p class="mt-2 text-xs text-gray-400 italic line-clamp-2">
                                        {{ $data->description }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        {{-- Metadata & Actions --}}
                        <div class="flex items-center justify-between md:justify-end gap-6 border-t md:border-t-0 pt-3 md:pt-0 mt-1 md:mt-0">
                            <div class="text-left md:text-right">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Completed</p>
                                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                                    {{ \Carbon\Carbon::parse($data->effective_date)->format('M Y') }}
                                </p>
                            </div>

                            <div class="text-left md:text-right">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Grade</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400">
                                    {{ $data->qualificationGrade->grade ?? 'N/A' }}
                                </span>
                            </div>

                            @if($canDelete)
                                <div class="pl-4 border-l border-gray-100 dark:border-gray-700">
                                    <flux:button 
                                        icon="trash" 
                                        variant="ghost" 
                                        size="sm" 
                                        wire:click="delete({{ $data->id }})"
                                        onclick="return confirm('Remove this qualification?') || event.stopImmediatePropagation()"
                                        class="text-gray-400 hover:text-red-500 transition-colors"
                                    />
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center p-12 bg-gray-50 dark:bg-gray-900/20 rounded-3xl border border-dashed border-gray-200 dark:border-gray-800">
                    <flux:icon.academic-cap class="size-10 text-gray-300 dark:text-gray-600 mb-4" />
                    <p class="text-sm text-gray-500 font-medium">No qualifications listed yet.</p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- Refined Modal --}}
    @if($canCreate)
    <flux:modal wire:model="showModal" name="add-qualification" class="md:w-160">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg" badge="Education">Add Achievement</flux:heading>
                <flux:text class="mt-2">Ensure dates match your certificates for verification.</flux:text>
            </div>

            <form wire:submit.prevent="save" class="space-y-5">
                <flux:select wire:model.live="qualification" label="Qualification" icon="academic-cap">
                    <flux:select.option value="">Select Qualification</flux:select.option>
                    @foreach ($educationQualificationList as $data)
                        <flux:select.option value="{{ $data->qualifications_id }}">
                            {{ $data->qualification }}
                        </flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input label="Institution / University" wire:model.live="institution" icon="building-library" placeholder="e.g. University of Colombo" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input label="Effective Date" wire:model.live="effectiveDate" type="date" />
                    
                    <flux:select wire:model.live="grade" label="Grade / Result">
                        <flux:select.option value="">Select Grade</flux:select.option>
                        @foreach ($gradeOption as $key => $value)
                            <flux:select.option value="{{ $key }}">{{ $value }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>

                <flux:textarea rows="3" wire:model.live="description" label="Additional Details"
                    placeholder="Major subjects, thesis title, or special awards..." />

                <div class="flex gap-3 pt-4">
                    <flux:modal.close>
                        <flux:button variant="ghost" class="flex-1">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary" class="flex-1 shadow-lg shadow-indigo-500/20">Save Achievement</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
    @endif
</div>