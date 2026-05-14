<?php

namespace App\Livewire\DOS;

use App\Imports\TeachersImport;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class BulkDOSImport extends Component
{
    use WithFileUploads;

    public $file;
    public $successCount = 0;
    public $failCount = 0;

    protected $rules = [
        'file' => 'required|file|mimes:xlsx,xls|max:102800', // 10MB limit
    ];

    public function import()
    {
        $this->validate();

        // Store uploaded file temporarily
        $storedPath = $this->file->store('imports', 'public');

        // Create import instance so we can access counters later
        $import = new TeachersImport(Auth::user()->people_id);

        try {
            Excel::import($import, storage_path('app/public/' . $storedPath));

            $this->successCount = $import->getSuccessCount();
            $this->failCount = $import->getFailCount();

            if ($this->successCount > 0) {
                session()->flash('success', 'Development Officer imported successfully!');
            } else {
                session()->flash('error', 'No Development Officer were imported. Please check your file.');
            }

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            // Log each validation failure
            foreach ($failures as $failure) {
                logger()->error('Excel validation failed', [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                    'values' => $failure->values(),
                ]);
            }

            $failedCount = count($failures);
            session()->flash('error', "Import validation failed for {$failedCount} rows. Check logs for details.". $e->getMessage());
        } catch (\Throwable $e) {
            logger()->error('Development Officer import failed', ['error' => $e->getMessage()]);
            session()->flash('error', 'Import failed: ' . $e->getMessage());
        }

        return redirect()->route('dos.list');
    }

    public function render()
    {
        return view('livewire.d-o-s.bulk-d-o-s-import');
    }
}
