<section class="w-full">
    <x-offices.zeo.zeo-layout :officeId="$officeId">
        {{-- Page Header --}}
        <div class="relative mb-6 w-full">
            <flux:heading size="xl" level="1" class="text-slate-900 dark:text-white">{{ __('Zonal Education Office') }}</flux:heading>
            <flux:subheading size="lg" class="mb-6 text-slate-500 dark:text-slate-500">
                {{ __('Statistics about Zonal Education Office structure and staff distribution.') }}
            </flux:subheading>
            <flux:separator variant="subtle" />
        </div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white">
                {{ __('Create Institution Group') }}
            </h2>
            <a href="{{ route('offices.zeo.profile.institution-groups', $officeId) }}" wire:navigate>
                <flux:button variant="ghost" icon="arrow-left" class="shadow-sm">
                    {{ __('Back to Institution Groups') }}
                </flux:button>
            </a>
        </div>


        <div class="mx-auto">
            <div class="bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-3xl p-6 md:p-10 shadow-sm relative overflow-hidden">
                <div class="absolute right-0 top-0 w-64 h-64 bg-linear-to-bl from-blue-100 to-transparent dark:from-blue-900/20 rounded-bl-full -z-10 opacity-70"></div>

                <form wire:submit.prevent="createInstiutionGroup" class="space-y-8 relative z-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Group Name --}}
                        <flux:field>
                            <flux:label>{{ __('Group Name') }}</flux:label>
                            <flux:input wire:model="groupName" placeholder="e.g. Set 01" required />
                            <flux:error name="groupName" />
                        </flux:field>

                        {{-- Officer --}}
                        <flux:field>
                            <flux:label>{{ __('Group Assigned Officer') }}</flux:label>
                            <flux:select wire:model="assignedPerson" placeholder="Select officer">
                                @foreach ($officersList as $officer)
                                <flux:select.option :value="$officer->employee_id">{{ $officer->employee->name_with_initials }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="assignedPerson" />
                        </flux:field>
                    </div>

                    {{-- Description --}}
                    <flux:field>
                        <flux:label>{{ __('Description') }}</flux:label>
                        <flux:textarea wire:model="description" placeholder="Brief description of the group's characteristics..." rows="3" />
                        <flux:error name="description" />
                    </flux:field>

                    <div class="flex items-start gap-4">
                        {{-- Institution --}}
                        <flux:field class="flex-1">
                            <flux:label>{{ __('Institution') }}</flux:label>
                            <flux:select wire:model="institutionId">
                                <flux:select.option value="">Select institutions</flux:select.option>
                                @foreach ($institutionList as $institution)
                                <flux:select.option value="{{ $institution->id }}">{{ str_pad($institution['census_no'], 5, '0', STR_PAD_LEFT) }} - {{ $institution->name }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="institutionId" />
                        </flux:field>

                        {{-- Add Institution Button --}}
                        <flux:field>
                            <flux:label class="opacity-0">{{ __('Action') }}</flux:label>
                            <flux:button type="button" wire:click="addInstitution" variant="primary" icon="plus" class="shadow-sm">
                                {{ __('Add Institution') }}
                            </flux:button>
                        </flux:field>
                    </div>

                    @if (count($selectedInstitutions) > 0)
                    <div class="space-y-3">
                        <flux:label>{{ __('Selected Institutions') }}</flux:label>
                        @foreach ($selectedInstitutions as $index => $institution)
                        <div class="flex items-center gap-3 p-3 mt-1 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-300 dark:border-slate-700">
                            <div class="flex-1">
                                <p class="font-medium text-slate-900 dark:text-white">{{ $institution['name'] }}</p>
                                <p class="text-sm text-slate-500 dark:text-slate-500">
                                    Census No: {{ str_pad($institution['census_no'], 5, '0', STR_PAD_LEFT) }}
                                </p>
                            </div>
                            <flux:button type="button" wire:click="removeInstitution({{ $index }})" variant="ghost" size="sm" class="shrink-0">
                                {{ __('Remove') }}
                            </flux:button>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <flux:separator variant="subtle" />

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('offices.zeo.profile.institution-groups', $officeId) }}" wire:navigate>
                            <flux:button variant="ghost">
                                {{ __('Cancel') }}
                            </flux:button>
                        </a>
                        <flux:button type="submit" variant="primary" icon="check" class="shadow-sm">
                            {{ __('Save Group') }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>

    </x-offices.zeo.zeo-layout>
</section>