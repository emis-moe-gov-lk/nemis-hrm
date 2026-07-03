<section class="w-full">
    <x-main-tables.layout>
        <div class="mx-auto max-w-[1400px] px-4 pb-12 lg:px-0">
            <div class="mb-8 flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <div class="space-y-1">
                    <flux:heading size="xl" level="1" class="font-black! uppercase tracking-tight text-slate-900 dark:text-white">
                        {{ __('Transfer Recommendations') }}
                    </flux:heading>
                    <flux:subheading size="lg" class="flex items-center gap-2">
                        <flux:icon.clipboard-document-check variant="micro" class="text-indigo-500" />
                        {{ __('Maintain principal, zonal, and workflow recommendation options for teacher transfer applications') }}
                    </flux:subheading>
                </div>

                <flux:modal.trigger name="add-transfer-recommendation" wire:click="openCreateModal">
                    <flux:button icon="plus" color="primary" class="w-full rounded-2xl! shadow-lg shadow-indigo-500/20 md:w-auto">
                        {{ __('Add Recommendation') }}
                    </flux:button>
                </flux:modal.trigger>
            </div>

            <div class="mb-6 rounded-3xl border border-amber-200 bg-amber-50/80 px-5 py-4 text-sm text-amber-900 dark:border-amber-900/60 dark:bg-amber-950/20 dark:text-amber-200">
                <div class="flex items-start gap-3">
                    <flux:icon.information-circle variant="micro" class="mt-0.5 shrink-0" />
                    <div class="space-y-1">
                        <p class="font-bold">{{ __('The recommendation text is customizable.') }}</p>
                        <p>{{ __('Use the workflow effect flag to decide whether selecting that recommendation should mark the application as Not Recomended. This keeps business logic stable even when wording changes.') }}</p>
                    </div>
                </div>
            </div>

            @if (session()->has('message'))
                <div class="mb-6 animate-in fade-in slide-in-from-top-4 duration-500">
                    <div class="flex items-center gap-3 rounded-2xl border border-emerald-100 bg-emerald-50 p-4 text-emerald-800 dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400">
                        <flux:icon.check-circle variant="micro" class="shrink-0" />
                        <span class="text-sm font-bold">{{ session('message') }}</span>
                    </div>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mb-6 animate-in fade-in slide-in-from-top-4 duration-500">
                    <div class="flex items-center gap-3 rounded-2xl border border-rose-100 bg-rose-50 p-4 text-rose-800 dark:border-rose-800 dark:bg-rose-900/20 dark:text-rose-300">
                        <flux:icon.exclamation-triangle variant="micro" class="shrink-0" />
                        <span class="text-sm font-bold">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <div class="mb-6 grid grid-cols-1 gap-4 rounded-3xl border border-slate-300 bg-white p-5 dark:border-slate-700 dark:bg-slate-900 md:grid-cols-[1fr,18rem]">
                <flux:field>
                    <flux:input
                        wire:model.live.debounce.300ms="search"
                        icon="magnifying-glass"
                        label="{{ __('Search') }}"
                        placeholder="{{ __('Search by ID, office level, or recommendation text') }}"
                        class="rounded-xl!"
                    />
                </flux:field>

                <flux:field>
                    <flux:select wire:model.live="officeLevelFilter" label="{{ __('Office Level') }}" class="rounded-xl!">
                        <option value="">{{ __('All office levels') }}</option>
                        @foreach ($officeLevels as $officeLevel)
                            <option value="{{ $officeLevel->office_level_id }}">
                                {{ $officeLevel->office_level_name }}
                            </option>
                        @endforeach
                    </flux:select>
                </flux:field>
            </div>

            <div class="overflow-hidden rounded-[2rem] border border-slate-300 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left dark:divide-slate-700">
                        <thead class="bg-slate-50 text-[11px] font-black uppercase tracking-[0.18em] text-slate-500 dark:bg-slate-800/60 dark:text-slate-400">
                            <tr>
                                <th class="px-5 py-4">{{ __('ID') }}</th>
                                <th class="px-5 py-4">{{ __('Office Level') }}</th>
                                <th class="min-w-[24rem] px-5 py-4">{{ __('Recommendation') }}</th>
                                <th class="px-5 py-4">{{ __('Workflow Effect') }}</th>
                                <th class="px-5 py-4">{{ __('Usage') }}</th>
                                <th class="px-5 py-4">{{ __('Status') }}</th>
                                <th class="px-5 py-4 text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse ($recommendations as $recommendation)
                                <tr class="align-top hover:bg-slate-50/80 dark:hover:bg-slate-800/40">
                                    <td class="whitespace-nowrap px-5 py-5">
                                        <span class="font-mono text-xs font-bold text-indigo-600 dark:text-indigo-400">
                                            {{ $recommendation->transfer_recommendation_list_id }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-5">
                                        <div class="space-y-1">
                                            <p class="text-sm font-bold text-slate-900 dark:text-white">
                                                {{ $recommendation->officeLevel?->office_level_name ?? __('Not linked') }}
                                            </p>
                                            <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-slate-400">
                                                {{ $recommendation->office_level_id }}
                                            </p>
                                        </div>
                                    </td>
                                    <td class="px-5 py-5">
                                        <p class="text-sm font-semibold leading-6 text-slate-700 dark:text-slate-200">
                                            {{ $recommendation->decision }}
                                        </p>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-5">
                                        <flux:badge color="{{ $recommendation->rejectsApplication() ? 'rose' : 'green' }}" size="sm" class="font-black uppercase tracking-tight">
                                            {{ $recommendation->rejectsApplication() ? __('Not Recomended') : __('Continue Workflow') }}
                                        </flux:badge>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-5">
                                        <span class="font-mono text-sm font-black text-slate-700 dark:text-slate-200">
                                            {{ number_format($recommendation->application_recommendations_count) }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-5">
                                        <flux:badge color="{{ $recommendation->active_status ? 'green' : 'red' }}" size="sm" class="font-black uppercase tracking-tight">
                                            {{ $recommendation->active_status ? __('Active') : __('Inactive') }}
                                        </flux:badge>
                                    </td>
                                    <td class="px-5 py-5">
                                        <div class="flex justify-end gap-2">
                                            <flux:modal.trigger name="edit-transfer-recommendation" wire:click="editRecommendation({{ $recommendation->id }})">
                                                <flux:button size="sm" variant="ghost" icon="pencil-square" class="rounded-xl!">
                                                    {{ __('Edit') }}
                                                </flux:button>
                                            </flux:modal.trigger>

                                            <flux:button
                                                size="sm"
                                                variant="filled"
                                                color="{{ $recommendation->active_status ? 'red' : 'primary' }}"
                                                icon="{{ $recommendation->active_status ? 'no-symbol' : 'check' }}"
                                                wire:click="toggleStatus({{ $recommendation->id }})"
                                                wire:confirm="{{ __('Are you sure you want to change this recommendation status?') }}"
                                                class="rounded-xl!"
                                            >
                                                {{ $recommendation->active_status ? __('Disable') : __('Enable') }}
                                            </flux:button>

                                            @if ($recommendation->application_recommendations_count === 0)
                                                <flux:button
                                                    size="sm"
                                                    variant="danger"
                                                    icon="trash"
                                                    wire:click="deleteRecommendation({{ $recommendation->id }})"
                                                    wire:confirm="{{ __('Delete this unused recommendation?') }}"
                                                    class="rounded-xl!"
                                                >
                                                    {{ __('Delete') }}
                                                </flux:button>
                                            @else
                                                <flux:button size="sm" variant="ghost" icon="lock-closed" disabled class="rounded-xl!">
                                                    {{ __('Used') }}
                                                </flux:button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="flex h-16 w-16 items-center justify-center rounded-3xl border-2 border-dashed border-slate-300 text-slate-300 dark:border-slate-700 dark:text-slate-600">
                                                <flux:icon.clipboard-document-check />
                                            </div>
                                            <p class="text-sm font-black uppercase tracking-widest text-slate-500">{{ __('No recommendations found') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-8">
                {{ $recommendations->links() }}
            </div>
        </div>

        <flux:modal wire:model="showNewRecommendationModal" name="add-transfer-recommendation" class="w-full max-w-2xl rounded-[2.5rem] p-8">
            <form wire:submit.prevent="addRecommendation" class="space-y-6">
                @csrf
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-3xl bg-indigo-600 text-white shadow-xl shadow-indigo-500/30">
                        <flux:icon.plus />
                    </div>
                    <div>
                        <flux:heading size="lg" class="font-black! uppercase tracking-tight">{{ __('New Recommendation') }}</flux:heading>
                        <flux:text>{{ __('Create an option for a transfer application approval step.') }}</flux:text>
                    </div>
                </div>

                <flux:field>
                    <flux:select label="{{ __('Office Level') }}" wire:model="officeLevelId" class="rounded-xl!">
                        <option value="">{{ __('Select office level') }}</option>
                        @foreach ($officeLevels as $officeLevel)
                            <option value="{{ $officeLevel->office_level_id }}">{{ $officeLevel->office_level_name }}</option>
                        @endforeach
                    </flux:select>
                    @error('officeLevelId') <p class="mt-2 text-sm font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                </flux:field>

                <flux:field>
                    <flux:textarea label="{{ __('Recommendation Text') }}" wire:model.blur="decision" rows="4" class="rounded-xl!" />
                    @error('decision') <p class="mt-2 text-sm font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                </flux:field>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <label class="flex items-start gap-3 rounded-3xl border border-slate-300 px-4 py-4 dark:border-slate-700">
                        <flux:checkbox wire:model="rejectsApplication" />
                        <span class="space-y-1">
                            <span class="block text-sm font-black text-slate-900 dark:text-white">{{ __('Mark application as Not Recomended') }}</span>
                            <span class="block text-xs leading-5 text-slate-500 dark:text-slate-400">{{ __('Use for refusal or negative recommendations.') }}</span>
                        </span>
                    </label>

                    <label class="flex items-start gap-3 rounded-3xl border border-slate-300 px-4 py-4 dark:border-slate-700">
                        <flux:checkbox wire:model="activeStatus" />
                        <span class="space-y-1">
                            <span class="block text-sm font-black text-slate-900 dark:text-white">{{ __('Active') }}</span>
                            <span class="block text-xs leading-5 text-slate-500 dark:text-slate-400">{{ __('Only active options appear in approval forms.') }}</span>
                        </span>
                    </label>
                </div>

                <div class="flex gap-3 pt-2">
                    <flux:modal.close class="flex-1">
                        <flux:button variant="ghost" class="w-full rounded-xl!">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary" class="flex-[2] rounded-xl! shadow-lg shadow-indigo-500/20">
                        {{ __('Add Recommendation') }}
                    </flux:button>
                </div>
            </form>
        </flux:modal>

        <flux:modal wire:model="showEditRecommendationModal" name="edit-transfer-recommendation" class="w-full max-w-2xl rounded-[2.5rem] p-8">
            <form wire:submit.prevent="updateRecommendation" class="space-y-6">
                @csrf
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-3xl bg-amber-500 text-white shadow-xl shadow-amber-500/30">
                        <flux:icon.pencil-square />
                    </div>
                    <div>
                        <flux:heading size="lg" class="font-black! uppercase tracking-tight">{{ __('Edit Recommendation') }}</flux:heading>
                        <flux:text>{{ __('Update the recommendation wording and workflow effect.') }}</flux:text>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <flux:field>
                        <flux:input disabled label="{{ __('Recommendation ID') }}" wire:model="updateRecommendationListId" class="rounded-xl! opacity-70" />
                    </flux:field>

                    <flux:field>
                        <flux:select label="{{ __('Office Level') }}" wire:model="updateOfficeLevelId" class="rounded-xl!">
                            <option value="">{{ __('Select office level') }}</option>
                            @foreach ($officeLevels as $officeLevel)
                                <option value="{{ $officeLevel->office_level_id }}">{{ $officeLevel->office_level_name }}</option>
                            @endforeach
                        </flux:select>
                        @error('updateOfficeLevelId') <p class="mt-2 text-sm font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                    </flux:field>
                </div>

                <flux:field>
                    <flux:textarea label="{{ __('Recommendation Text') }}" wire:model.blur="updateDecision" rows="4" class="rounded-xl!" />
                    @error('updateDecision') <p class="mt-2 text-sm font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                </flux:field>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <label class="flex items-start gap-3 rounded-3xl border border-slate-300 px-4 py-4 dark:border-slate-700">
                        <flux:checkbox wire:model="updateRejectsApplication" />
                        <span class="space-y-1">
                            <span class="block text-sm font-black text-slate-900 dark:text-white">{{ __('Mark application as Not Recomended') }}</span>
                            <span class="block text-xs leading-5 text-slate-500 dark:text-slate-400">{{ __('When selected, approval will stop and the application status becomes Not Recomended.') }}</span>
                        </span>
                    </label>

                    <label class="flex items-start gap-3 rounded-3xl border border-slate-300 px-4 py-4 dark:border-slate-700">
                        <flux:checkbox wire:model="updateActiveStatus" />
                        <span class="space-y-1">
                            <span class="block text-sm font-black text-slate-900 dark:text-white">{{ __('Active') }}</span>
                            <span class="block text-xs leading-5 text-slate-500 dark:text-slate-400">{{ __('Only active options appear in approval forms.') }}</span>
                        </span>
                    </label>
                </div>

                <div class="flex gap-3 pt-2">
                    <flux:modal.close class="flex-1">
                        <flux:button variant="ghost" class="w-full rounded-xl!">{{ __('Discard') }}</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary" class="flex-[2] rounded-xl! shadow-lg shadow-indigo-500/20">
                        {{ __('Save Changes') }}
                    </flux:button>
                </div>
            </form>
        </flux:modal>
    </x-main-tables.layout>
</section>
