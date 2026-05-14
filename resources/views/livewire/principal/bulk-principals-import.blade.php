<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Bulk upload Principals') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Create principal profile and account') }}</flux:subheading>
        <flux:separator variant="subtle" />

        <div class="my-6">
            <div class="max-w-5xl bg-white p-6 rounded-lg border border-gray-200">
                {{-- Success Message --}}
                @if (session()->has('success'))
                    <div class="p-4 mb-4 text-green-700 bg-green-50 border border-green-200 rounded">
                        <p class="font-medium">Success</p>
                        <p class="text-sm mt-1">{{ session('success') }}</p>
                        @if ($successCount > 0)
                            <p class="text-sm mt-1">{{ $successCount }} principals imported successfully.</p>
                        @endif
                    </div>
                @endif

                {{-- Error Message --}}
                @if (session()->has('error'))
                    <div class="p-4 mb-4 text-red-700 bg-red-50 border border-red-200 rounded">
                        <p class="font-medium">Error</p>
                        <p class="text-sm mt-1">{{ session('error') }}</p>
                    </div>
                @endif

                {{-- Import Results --}}
                @if ($successCount > 0 || $failCount > 0)
                    <div class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded">
                        <p class="font-medium text-gray-700 mb-2">Import Results:</p>
                        <div class="flex gap-4 text-sm">
                            <span class="text-green-600">✓ {{ $successCount }} successful</span>
                            <span class="text-red-600">✗ {{ $failCount }} failed</span>
                        </div>
                    </div>
                @endif

                {{-- Download Template --}}
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded">
                    <p class="text-sm text-blue-700 mb-2">Download our Excel template to ensure proper formatting:</p>
                    <a href="{{ route('principal.download.template') }}"
                        class="inline-flex items-center px-3 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                        📥 Download Template
                    </a>
                </div>

                {{-- Upload Form --}}
                <form wire:submit.prevent="import">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Upload Excel File
                        </label>

                        <input type="file" wire:model="file" accept=".xlsx,.xls,.csv"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">

                        @error('file')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        @if ($file)
                            <p class="mt-2 text-sm text-gray-600">
                                Selected: {{ $file->getClientOriginalName() }}
                            </p>
                        @endif
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" wire:click="$set('file', null)"
                            class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">
                            Clear
                        </button>
                        <button type="submit" wire:loading.attr="disabled"
                            class="px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50">
                            <span wire:loading.remove>Import Principals</span>
                            <span wire:loading>Importing...</span>
                        </button>
                    </div>
                </form>

                {{-- Loading --}}
                <div wire:loading.delay class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded">
                    <p class="text-sm text-blue-700">Processing file, please wait...</p>
                </div>
            </div>
        </div>
    </div>
</div>
