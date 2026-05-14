<section class="w-full">
    <x-main-tables.layout>
        <div>
            {{-- Section Header --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div>
                    <flux:heading size="xl" level="1" class="!font-black tracking-tight text-indigo-600 dark:text-indigo-400 uppercase">
                        {{ __('Teaching Subjects') }}
                    </flux:heading>
                    <flux:subheading size="lg">{{ __('Manage Subject curriculum and configuration masks') }}</flux:subheading>
                </div>

                <flux:modal.trigger name="add-new-teaching-subject">
                    <flux:button icon="plus" color="primary" class="!rounded-2xl shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all">
                        {{ __('Add Teaching Subject') }}
                    </flux:button>
                </flux:modal.trigger>
            </div>

            {{-- Success Message --}}
            @if (session()->has('message'))
                <div class="mb-6 p-4 rounded-2xl bg-green-50 border border-green-100 text-green-800 text-sm font-bold flex items-center gap-2 animate-in fade-in slide-in-from-top-2">
                    <flux:icon.check-circle variant="micro" />
                    {{ session('message') }}
                </div>
            @endif

            {{-- CARD GRID --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse ($teaching_subjects as $key => $data)
                    <div class="group relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2rem] p-6 shadow-sm hover:shadow-xl hover:border-indigo-200 dark:hover:border-indigo-900 transition-all duration-300">
                        
                        {{-- Card Header: ID & Status --}}
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ $data->subject_id }}</span>
                                <span class="text-xs font-bold text-indigo-500 uppercase">{{ $data->subject_code }}</span>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter {{ $data->active_status ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                                {{ $data->active_status ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        {{-- Subject Names --}}
                        <div class="space-y-3 mb-6">
                            <div>
                                <h3 class="text-lg font-black text-slate-900 dark:text-white leading-tight group-hover:text-indigo-600 transition-colors">{{ $data->name_en }}</h3>
                                <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mt-1">{{ $data->name_si }}</p>
                                <p class="text-sm font-medium text-slate-400 dark:text-slate-500">{{ $data->name_ta }}</p>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-[10px] font-bold text-slate-600 dark:text-slate-400 uppercase">
                                    Type: {{ $data->type_name }}
                                </span>
                            </div>
                        </div>

                        {{-- Configuration Masks --}}
                        <div class="grid grid-cols-1 gap-2 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-2xl mb-6">
                            <div class="flex justify-between items-center">
                                <span class="text-[9px] font-black text-slate-400 uppercase">Grade Mask</span>
                                <code class="text-[10px] text-indigo-600 dark:text-indigo-400 font-mono tracking-tighter">{{ $data->grade_mask }}</code>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-[9px] font-black text-slate-400 uppercase">Lang Mask</span>
                                <code class="text-[10px] text-emerald-600 dark:text-emerald-400 font-mono tracking-tighter">{{ $data->language_mask }}</code>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex gap-2">
                            <flux:modal.trigger wire:click="editTeachingSubject({{ $data->id }})" name="edit-teaching-subject">
                                <flux:button size="sm" class="flex-1 !rounded-xl" icon="pencil-square">Edit</flux:button>
                            </flux:modal.trigger>
                            
                            <flux:button 
                                wire:click="toggleStatus({{ $data->id }})"
                                wire:confirm="Are you sure you want to change the status of {{ $data->name_en }}?"
                                size="sm" 
                                variant="ghost"
                                color="{{ $data->active_status ? 'danger' : 'primary' }}"
                                icon="{{ $data->active_status ? 'no-symbol' : 'check' }}"
                                class="!rounded-xl"
                            />
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 bg-slate-50 dark:bg-slate-800/50 rounded-[3rem] border-2 border-dashed border-slate-200 dark:border-slate-700 flex flex-col items-center">
                        <flux:icon.document-magnifying-glass size="xl" variant="outline" class="text-slate-300 mb-4" />
                        <p class="text-slate-500 font-bold">No Teaching Subjects Found!</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-10">
                {{ $teaching_subjects->links() }}
            </div>
        </div>

        {{-- MODAL: ADD NEW --}}
        <flux:modal wire:model="showModelNewTeachingSubject" name="add-new-teaching-subject" class="md:w-[32rem] rounded-[2rem]">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg" class="!font-black uppercase tracking-tight">Add Teaching Subject</flux:heading>
                    <flux:text class="mt-2">Enter the details for the new curriculum subject.</flux:text>
                </div>

                <form wire:submit.prevent="addNewTeachingSubject" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <flux:input label="Subject ID" wire:model.live="teaching_subject_id" mask="SUB9999" placeholder="SUB0000" />
                        <flux:input label="Subject Code" wire:model.live="teaching_subject_code" placeholder="MAT101" />
                    </div>

                    <flux:input label="Name (English)" wire:model.live="teaching_subject_name_en" />
                    <flux:input label="Name (Sinhala)" wire:model.live="teaching_subject_name_si" />
                    <flux:input label="Name (Tamil)" wire:model.live="teaching_subject_name_ta" />

                    <flux:select label="Subject Type" wire:model.live="subject_type">
                        <option value="">Select Type</option>
                        <option value="1">Subject</option>
                        <option value="2">Designation</option>
                        <option value="3">Other</option>
                    </flux:select>

                    <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl space-y-4">
                        <flux:heading size="sm" class="font-black uppercase text-slate-400 text-[10px]">Config Masks</flux:heading>
                        <flux:input label="Grade Mask (13 chars)" wire:model.live="grade_mask" placeholder="0000000000000" />
                        <flux:input label="Language Mask (4 chars)" wire:model.live="language_mask" placeholder="0000" />
                        <flux:input label="Category Mask (13 chars)" wire:model.live="category_mask" placeholder="0000000000000" />
                    </div>

                    <div class="flex gap-3 pt-4">
                        <flux:spacer />
                        <flux:modal.close>
                            <flux:button variant="ghost">Cancel</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary">Save Subject</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- MODAL: EDIT --}}
        <flux:modal wire:model="showModelEditTeachingSubject" name="edit-teaching-subject" class="md:w-[32rem] rounded-[2rem]">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg" class="!font-black uppercase tracking-tight">Edit Subject</flux:heading>
                    <flux:text class="mt-2">Modify existing subject properties and masks.</flux:text>
                </div>

                <form wire:submit.prevent="updateTeachingSubject" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <flux:input label="Subject ID" wire:model.live="update_teaching_subject_id" disabled mask="SUB9999" class="opacity-70" />
                        <flux:input label="Subject Code" wire:model.live="update_teaching_subject_code" />
                    </div>

                    <flux:input label="Name (English)" wire:model.live="update_teaching_subject_name_en" />
                    <flux:input label="Name (Sinhala)" wire:model.live="update_teaching_subject_name_si" />
                    <flux:input label="Name (Tamil)" wire:model.live="update_teaching_subject_name_ta" />

                    <flux:select label="Subject Type" wire:model.live="update_subject_type">
                        <option value="">Select Type</option>
                        <option value="1">Subject</option>
                        <option value="2">Designation</option>
                        <option value="3">Other</option>
                    </flux:select>

                    <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl space-y-4">
                        <flux:heading size="sm" class="font-black uppercase text-slate-400 text-[10px]">Config Masks</flux:heading>
                        <flux:input label="Grade Mask" wire:model.live="update_grade_mask" />
                        <flux:input label="Language Mask" wire:model.live="update_language_mask" />
                        <flux:input label="Category Mask" wire:model.live="update_category_mask" />
                    </div>

                    <div class="flex gap-3 pt-4">
                        <flux:spacer />
                        <flux:button type="submit" variant="primary">Save Changes</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>
    </x-main-tables.layout>
</section>