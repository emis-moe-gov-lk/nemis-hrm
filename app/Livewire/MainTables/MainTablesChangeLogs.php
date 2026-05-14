<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use App\Models\Versions;
use App\Models\ChangeLogs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MainTablesChangeLogs extends Component
{
    public $versionOption;

    public function mount(){
        $this->versionOption = Versions::orderBy('version_id', 'asc')->get();
    }

    public $showModelNewChangeLog = false;
    public $versionId, $type, $title, $description;


    // 🔹 Validation rules
    protected function rules()
    {
        if ($this->editChangeLogId) {
            // ✅ Editing existing record
            return [
                'updateVersionId' => 'required|string|max:50',
                'updateType' => 'required|string|max:50',
                'updateTitle' => 'required|string|max:50',
                'updateDescription' => 'required|string',
            ];
        }

        return [
            'versionId' => 'required|string|max:50',
            'type' => 'required|string|max:50',
            'title' => 'required|string|max:50',
            'description' => 'required|string',
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // 🔹 Submit form
    public function addNewChangeLog()
    {
        $validated = $this->validate();

        try{
            ChangeLogs::create([
                'version_id' => $this->versionId,
                'type' => $this->type,
                'title' => $this->title,
                'description' => $this->description,
            ]);

            session()->flash('message', '✅ New Change Log added successfully!');

            // ✅ Close modal
            $this->showModelNewChangeLog = false;

            // ✅ Reset form fields (but keep modal control variable)
            $this->reset(['versionId', 'type', 'title', 'description']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to save change log data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Change log creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }

    }

    public function deleteChangeLog($id)
    {
        $changeLog = ChangeLogs::find($id);

        if ($changeLog) {
            $changeLog->delete();
            session()->flash('message', '✅ Change Log deleted successfully!');
        } else {
            session()->flash('message', 'Change Log not found!');
        }
    }

    public $showModelEditChangeLog = false;
    public $editChangeLogId, $updateVersionId, $updateType, $updateTitle, $updateDescription;


    public function editChangeLog($id)
    {
        $changeLog = ChangeLogs::findOrFail($id);

        $this->editChangeLogId = $changeLog->id;
        $this->updateVersionId = $changeLog->version_id;
        $this->updateType = $changeLog->type;
        $this->updateTitle = $changeLog->title;
        $this->updateDescription = $changeLog->description;

        $this->showModelEditChangeLog = true; // ensure modal is open
    }

    public function updateChangeLog()
    {
        $this->validate([
            'updateVersionId' => 'required|string|max:50',
            'updateType' => 'required|string|max:50',
            'updateTitle' => 'required|string|max:50',
            'updateDescription' => 'required|string',
        ]);

        try{

            ChangeLogs::where('id', $this->editChangeLogId)->update([
                'version_id' => $this->updateVersionId,
                'type' => $this->updateType,
                'title' => $this->updateTitle,
                'description' => $this->updateDescription,
            ]);


            $this->showModelEditChangeLog = false;

            session()->flash('message', '✅ Change Log updated successfully!');

            $this->reset(['updateVersionId', 'updateType', 'updateTitle', 'updateDescription', 'editChangeLogId']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to update title data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Title update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $changelogs = ChangeLogs::orderBy('type', 'desc')->paginate(50);
        return view('livewire.main-tables.main-tables-change-logs', compact('changelogs'));
    }
}
