@props([
    'label' => null,
    'items' => [], // array: [value => label]
])

<div wire:ignore.self x-data="searchableDropdown({
    items: {{ \Illuminate\Support\Js::from($items) }},
    initialValue: @entangle($attributes->wire('model')),
})" class="relative w-full">

    <!-- SELECT BUTTON -->
    <button type="button" @click="toggle()"
        class="w-full flex items-center justify-between rounded-lg border border-gray-300 bg-white 
           px-3 py-2 text-sm text-gray-700 shadow-sm transition
           hover:border-gray-400 focus:outline-none focus:ring-1 focus:border-gray-700">

        <span class="block truncate" x-text="selectedLabel || 'Select an option'"></span>

        <!-- Chevron Icon (Flux style) -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-300" fill="none" viewBox="0 0 24 24"
            stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M8.25 15 12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
        </svg>

    </button>


    <!-- DROPDOWN -->
    <div x-show="open" x-cloak @click.outside="open = false" :class="dropUp ? 'bottom-full mb-1' : 'top-full mt-1'"
        class="absolute z-[99999] bg-white border rounded-lg w-full shadow-sm" style="display:none;">
        <!-- SEARCH BOX -->
        <!-- SEARCH BOX -->
        <div class="p-2 border-b">
            <div class="relative">
                <!-- Search Icon -->
                <span class="absolute inset-y-0 left-2 flex items-center text-gray-400 pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>

                </span>

                <!-- Search Input -->
                <input type="text" x-ref="search" x-model="search" @input="filter()" placeholder="Search..."
                    class="w-full border px-8 py-2 rounded-lg text-sm focus:outline-none">
            </div>
        </div>


        <!-- RESULTS -->
        <div class="max-h-48 overflow-y-auto">
            <template x-for="opt in filtered" :key="opt.value">
                <div @click="choose(opt)" class="px-3 py-2 cursor-pointer hover:bg-gray-100 text-sm">
                    <span x-text="opt.label" class="text-gray-700 font-medium"></span>
                </div>
            </template>

            <div x-show="filtered.length === 0" class="px-3 py-2 text-sm text-gray-500 text-center">
                No results found
            </div>
        </div>
    </div>

    <!-- LIVEWIRE BINDING -->
    <input type="hidden" {{ $attributes->whereStartsWith('wire:model') }} x-model="value">
</div>

<script>
    function searchableDropdown({
        items,
        initialValue
    }) {
        return {
            open: false,
            dropUp: false, // auto position
            search: '',
            items: items,
            filtered: [],
            value: initialValue,
            selectedLabel: '',

            init() {
                this.filtered = Object.entries(this.items).map(([value, label]) => ({
                    value,
                    label
                }));

                if (this.value && this.items[this.value]) {
                    this.selectedLabel = this.items[this.value];
                }

                this.$watch('value', (val) => {
                    if (val && this.items[val]) {
                        this.selectedLabel = this.items[val];
                    }
                });
            },

            toggle() {
                this.open = !this.open;

                if (this.open) {
                    this.detectPosition();
                    this.$nextTick(() => this.$refs.search.focus());
                }
            },

            // AUTO OPEN UP / DOWN
            detectPosition() {
                const rect = this.$el.getBoundingClientRect();
                const viewportHeight = window.innerHeight;

                const spaceBelow = viewportHeight - rect.bottom;
                const dropdownHeight = 200; // approx dropdown height

                this.dropUp = spaceBelow < dropdownHeight;
            },

            filter() {
                const q = this.search.toLowerCase();

                this.filtered = Object.entries(this.items)
                    .filter(([value, label]) => label.toLowerCase().includes(q))
                    .map(([value, label]) => ({
                        value,
                        label
                    }));
            },

            choose(opt) {
                this.selectedLabel = opt.label;
                this.value = opt.value;
                this.open = false;
                this.search = '';
            }
        }
    }
</script>
