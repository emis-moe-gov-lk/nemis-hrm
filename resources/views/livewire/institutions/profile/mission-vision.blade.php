<div>
    <section>
        <div class="mb-3">
            <div class="flex items-baseline justify-between py-2">
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200">Mission & Vision</h2>
                @can('institution.mission_vision.update')
                <flux:modal.trigger name="edit-profile-mission-vision">
                    <flux:button>Edit</flux:button>
                </flux:modal.trigger>
                @endcan
            </div>
            <flux:separator variant="subtle" />
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Mission</p>
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $institution->mission ?? 'N/A' }}</p>
            </div>
            <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Vision</p>
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $institution->vision ?? 'N/A' }}</p>
            </div>
        </div>
    </section>

    <flux:modal name="edit-profile-mission-vision" class="md:w-150">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Update profile</flux:heading>
                <flux:text class="mt-2">Make changes to your personal details.</flux:text>
            </div>

            <form wire:submit.prevent="updateMissionVision">
                @csrf
                <div class="space-y-4">

                    <div class="flex flex-col gap-4">
                        <flux:field>
                            <flux:textarea label="Mission" id="mission" type="text" wire:model.live="mission" placeholder="Mission" />
                        </flux:field>
                    </div>

                    <div class="flex flex-col gap-4">
                        <flux:field>
                            <flux:textarea label="Vision" id="vision" type="text" wire:model.live="vision" placeholder="Vision" />
                        </flux:field>
                    </div>
                </div>

                <div class="flex mt-4">
                    <flux:spacer />

                    <div class="flex gap-2">
                        <flux:button type="button" wire:click="resetForm">Reset</flux:button>
                        <flux:button type="submit" variant="primary">Save changes</flux:button>
                    </div>
                </div>
            </form>
        </div>
    </flux:modal>

</div>