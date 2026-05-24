<section class="w-full">

    <x-main-tables.layout>
        <div class="max-w-[1400px] mx-auto pb-12">

            {{-- Section Header & Action Bar --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10 px-4 lg:px-0">
                <div class="space-y-1">
                    <flux:heading size="xl" level="1" class="!font-black tracking-tight text-slate-900 dark:text-white">
                        {{ __('Cadre Circulars') }}
                    </flux:heading>
                    <flux:subheading size="lg" class="flex items-center gap-2">
                        <flux:icon.document-text variant="micro" class="text-indigo-500" />
                        {{ __('Manage institutional directives, issued dates, and effective periods') }}
                    </flux:subheading>
                </div>

                <div class="flex items-center gap-3">
                    <flux:modal.trigger name="add-new-cadre-circular">
                        <flux:button icon="plus" color="primary"
                            class="w-full md:w-auto !rounded-2xl shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all">
                            {{ __('Add New Circular') }}
                        </flux:button>
                    </flux:modal.trigger>
                </div>
            </div>

            {{-- Notifications --}}
            @if (session()->has('message'))
                <div class="mx-4 lg:mx-0 mb-8 animate-in fade-in slide-in-from-top-4 duration-500">
                    <div
                        class="flex items-center gap-3 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400 shadow-sm">
                        <flux:icon.check-circle variant="micro" class="shrink-0" />
                        <span class="text-sm font-bold">{{ session('message') }}</span>
                    </div>
                </div>
            @endif

            {{-- Card Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 px-4 lg:px-0">
                @forelse ($cadreCircular as $key => $data)
                    <div
                        class="group relative bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-[2.5rem] p-6 transition-all duration-300 hover:shadow-2xl hover:shadow-indigo-500/10 hover:-translate-y-1.5 flex flex-col">

                        {{-- Top Row: Index & Status --}}
                        <div class="flex justify-between items-start mb-6">
                            <div
                                class="flex items-center justify-center w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 font-black text-xs ring-1 ring-indigo-100 dark:ring-indigo-800">
                                {{ $cadreCircular->firstItem() + $key }}
                            </div>

                            <flux:badge size="sm" variant="pill" color="{{ $data->active_status ? 'green' : 'red' }}"
                                class="!font-black ring-1 ring-inset shadow-sm">
                                {{ $data->active_status ? __('Active') : __('Inactive') }}
                            </flux:badge>
                        </div>

                        {{-- Core Content --}}
                        <div class="space-y-4 mb-8">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span
                                        class="text-[10px] font-bold uppercase text-indigo-500 tracking-widest">{{ $data->circular_id }}</span>
                                    <span class="text-slate-300 dark:text-slate-600">•</span>
                                    <span
                                        class="text-[10px] font-bold uppercase text-slate-500 tracking-widest">{{ __('Issued:') }}
                                        {{ $data->issued_date }}</span>
                                </div>
                                <flux:heading size="lg"
                                    class="!font-black text-slate-900 dark:text-white group-hover:text-indigo-600 transition-colors">
                                    {{ $data->circular_no }}
                                </flux:heading>
                                <p class="text-sm font-bold text-slate-600 dark:text-slate-300 mt-1 line-clamp-1">
                                    {{ $data->title ?? 'Untitled Circular' }}</p>
                            </div>

                            <div
                                class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-transparent group-hover:border-indigo-100 dark:group-hover:border-indigo-900/30 transition-colors duration-300">
                                <p class="text-xs text-slate-500 dark:text-slate-500 line-clamp-3 leading-relaxed italic">
                                    "{{ $data->description }}"
                                </p>
                            </div>
                        </div>

                        {{-- Action Footer --}}
                        <div class="flex items-center gap-2 mt-auto">
                            <flux:button wire:click="editCadreCircular({{ $data->id }})"
                                class="flex-1 !rounded-2xl !py-3 font-bold shadow-sm" variant="filled" icon="pencil-square">
                                {{ __('Edit') }}
                            </flux:button>

                            <flux:button wire:click="toggleStatus({{ $data->id }})"
                                wire:confirm="Are you sure you want to {{ $data->active_status ? 'deactivate' : 'activate' }} this circular?"
                                class="!rounded-2xl !py-3 px-4 shadow-sm" variant="ghost"
                                color="{{ $data->active_status ? 'red' : 'green' }}"
                                icon="{{ $data->active_status ? 'no-symbol' : 'check' }}" />
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-full py-20 bg-slate-50/50 dark:bg-slate-800/10 rounded-[3rem] border-2 border-dashed border-slate-300 dark:border-slate-700 flex flex-col items-center justify-center text-center">
                        <div
                            class="w-20 h-20 bg-indigo-50 dark:bg-indigo-900/20 rounded-full flex items-center justify-center mb-6 text-indigo-500">
                            <flux:icon.document-text size="xl" />
                        </div>
                        <flux:heading size="lg" class="!font-black text-slate-900 dark:text-white">
                            {{ __('No Circulars Found') }}</flux:heading>
                        <flux:text class="max-w-xs mx-auto">
                            {{ __('Get started by adding your first cadre circular to the system registry.') }}</flux:text>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-12 px-4">
                {{ $cadreCircular->links() }}
            </div>
        </div>

        {{-- MODAL: ADD NEW --}}
        <flux:modal wire:model="showModelNewCadreCircular" name="add-new-cadre-circular"
            class="w-full max-w-2xl rounded-[2.5rem] p-8">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg" class="!font-black tracking-tight">{{ __('Add Cadre Circular') }}
                    </flux:heading>
                    <flux:text>{{ __('Register a new policy or directive in the system.') }}</flux:text>
                </div>

                @if (session()->has('error'))
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-100 text-rose-800 text-xs font-bold">
                        {{ session('error') }}
                    </div>
                @endif

                <form wire:submit.prevent="addNewCadreCircular" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                    @csrf
                    {{-- ID & Number --}}
                    <flux:field>
                        <flux:input label="Circular ID" wire:model.live="circularId" class="!rounded-xl shadow-sm"
                            placeholder="CIR001" mask="CIR999" />
                    </flux:field>

                    <flux:field>
                        <flux:input label="Circular Number" wire:model.live="circularNo" class="!rounded-xl shadow-sm"
                            placeholder="e.g. 02/2024" />
                    </flux:field>

                    {{-- Title --}}
                    <flux:field class="md:col-span-2">
                        <flux:input label="Title" wire:model.live="title" class="!rounded-xl shadow-sm"
                            placeholder="Enter circular title" />
                    </flux:field>

                    {{-- Description --}}
                    <flux:field class="md:col-span-2">
                        <flux:textarea label="Description" wire:model.live="description" class="!rounded-xl shadow-sm"
                            rows="3" placeholder="Enter detailed description..." />
                    </flux:field>

                    {{-- Dates --}}
                    <flux:field>
                        <flux:input type="date" label="Issued Date" wire:model.live.debounce.150ms="issuedDate"
                            class="!rounded-xl shadow-sm" />
                    </flux:field>

                    <flux:field>
                        <flux:input type="date" label="Effective From" wire:model.live.debounce.150ms="effectiveFrom"
                            class="!rounded-xl shadow-sm" />
                    </flux:field>

                    <flux:field>
                        <flux:input type="date" label="Effective To" wire:model.live.debounce.150ms="effectiveTo"
                            class="!rounded-xl shadow-sm" />
                    </flux:field>

                    {{-- Selection Logic --}}
                    <flux:field>
                        <flux:select label="Superseded Circular" id="supersededCircular"
                            wire:model.live="supersededCircular" class="!rounded-xl shadow-sm">
                            <option value="">{{ __('Select Circular') }}</option>
                            @forelse ($supersededCircularOption as $superseded)
                                <option value="{{ $superseded->circular_id }}">{{ $superseded->circular_no }}
                                    [{{ $superseded->issued_date }}]</option>
                            @empty
                                <option value="">{{ __('No Circulars Found') }}</option>
                            @endforelse
                        </flux:select>
                    </flux:field>

                    <flux:field class="md:col-span-2">
                        <flux:select label="Active Status (Deactivates others if active)" id="activeStatus"
                            wire:model.live="activeStatus" class="!rounded-xl shadow-sm">
                            <option value="1">{{ __('Active') }}</option>
                            <option value="0">{{ __('Inactive') }}</option>
                        </flux:select>
                    </flux:field>

                    <div
                        class="md:col-span-2 flex flex-col-reverse md:flex-row gap-3 pt-6 border-t border-slate-200 dark:border-slate-700">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-xl">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary"
                            class="flex-[2] !rounded-xl px-6 shadow-md shadow-indigo-500/20">{{ __('Add Circular') }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- MODAL: EDIT --}}
        <flux:modal wire:model="showModelEditCadreCircular" name="edit-cadre-circular"
            class="w-full max-w-2xl rounded-[2.5rem] p-8">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg" class="!font-black tracking-tight">{{ __('Edit Circular') }}</flux:heading>
                    <flux:text>{{ __('Update the records for') }} <span
                            class="font-bold text-indigo-600">{{ $updateCircularNo ?? 'this circular' }}</span>
                    </flux:text>
                </div>

                @if (session()->has('error'))
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-100 text-rose-800 text-xs font-bold">
                        {{ session('error') }}
                    </div>
                @endif

                <form wire:submit.prevent="updateCadreCircularList"
                    class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                    @csrf
                    <flux:field>
                        <flux:input disabled label="Circular ID" wire:model.live="updateCircularId"
                            class="!rounded-xl bg-slate-50 dark:bg-slate-800 border-dashed shadow-none" mask="CIR999" />
                    </flux:field>

                    <flux:field>
                        <flux:input label="Circular Number" wire:model.live="updateCircularNo"
                            class="!rounded-xl shadow-sm" />
                    </flux:field>

                    <flux:field class="md:col-span-2">
                        <flux:input label="Title" wire:model.live="updateTitle" class="!rounded-xl shadow-sm" />
                    </flux:field>

                    <flux:field class="md:col-span-2">
                        <flux:textarea label="Description" wire:model.live="updateDescription"
                            class="!rounded-xl shadow-sm" rows="3" />
                    </flux:field>

                    <flux:field>
                        <flux:input type="date" label="Issued Date" wire:model.live.debounce.150ms="updateIssuedDate"
                            class="!rounded-xl shadow-sm" />
                    </flux:field>

                    <flux:field>
                        <flux:input type="date" label="Effective From"
                            wire:model.live.debounce.150ms="updateEffectiveFrom" class="!rounded-xl shadow-sm" />
                    </flux:field>

                    <flux:field>
                        <flux:input type="date" label="Effective To" wire:model.live.debounce.150ms="updateEffectiveTo"
                            class="!rounded-xl shadow-sm" />
                    </flux:field>

                    <flux:field>
                        <flux:select label="Superseded Circular" id="updateSupersededCircular"
                            wire:model.live="updateSupersededCircular" class="!rounded-xl shadow-sm">
                            <option value="">{{ __('Select Circular') }}</option>
                            @foreach ($supersededCircularOption as $superseded)
                                <option value="{{ $superseded->circular_id }}">{{ $superseded->circular_no }}
                                    [{{ $superseded->issued_date }}]</option>
                            @endforeach
                        </flux:select>
                    </flux:field>

                    <div
                        class="md:col-span-2 flex flex-col-reverse md:flex-row gap-3 pt-6 border-t border-slate-200 dark:border-slate-700">
                        <flux:modal.close class="flex-1">
                            <flux:button variant="ghost" class="w-full !rounded-xl">{{ __('Discard') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary"
                            class="flex-[2] !rounded-xl px-6 shadow-md shadow-indigo-500/20">{{ __('Save Changes') }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>
    </x-main-tables.layout>
</section>