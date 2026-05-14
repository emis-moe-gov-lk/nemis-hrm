<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use App\Models\Versions;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MainTablesVersions extends Component
{
    public $showModelNewVersion = false;
    public $versionId, $version, $releaseDate, $title, $description, $isLatest = false;


    // 🔹 Validation rules
    protected function rules()
    {
        if ($this->editVersionId) {
            // ✅ Editing existing record
            return [
                'updateVersionId' => [
                    'required',
                    'string',
                    'regex:/^[VER]{3}\d{3}$/',
                    Rule::unique('versions', 'version_id')->ignore($this->editVersionId),
                ],
                'updateVersion' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('versions', 'version_id')->ignore($this->editVersionId),
                ],
                'updateReleaseDate' => 'required|date',
                'updateTitle' => 'required|string|max:50',
                'updateDescription' => 'required|string',
                'updateIsLatest' => 'required|boolean',
            ];
        }

        return [
            'versionId' => [
                'required',
                'string',
                'regex:/^[VER]{3}\d{3}$/',
                Rule::unique('versions', 'version_id'),
            ],
            'version' => [
                'required',
                'string',
                'max:50',
                Rule::unique('versions', 'version'),
            ],
            'releaseDate' => 'required|date',
            'title' => 'required|string|max:50',
            'description' => 'required|string',
            'isLatest' => 'required|boolean',
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // 🔹 Submit form
    public function addNewVersion()
    {
        $validated = $this->validate();

        try{
            Versions::create([
                'version_id' => $this->versionId,
                'version' => $this->version,
                'release_date' => $this->releaseDate,
                'title' => $this->title,
                'description' => $this->description,
                'is_latest' => $this->isLatest,
            ]);

            if($this->isLatest){
                Versions::where('is_latest', 1)
                ->where('version_id', '!=', $this->versionId)
                ->update(['is_latest' => 0]);
            }

            session()->flash('message', '✅ New Version added successfully!');

            // ✅ Close modal
            $this->showModelNewVersion = false;

            // ✅ Reset form fields (but keep modal control variable)
            $this->reset(['versionId', 'version', 'releaseDate', 'title', 'description', 'isLatest']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to save version data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Version creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }

    }

    public function deleteVersion($id)
    {
        $version = Versions::find($id);

        if ($version) {
            $version->delete();
            session()->flash('message', '✅ Version deleted successfully!');
        } else {
            session()->flash('message', 'Title not found!');
        }
    }

    public function toggleStatus($id)
    {
        DB::transaction(function () use ($id) {

        $version = Versions::find($id);

        if (! $version) {
            return;
        }

        // If activating → deactivate all others
        if ($version->is_latest == 0) {
            Versions::where('is_latest', 1)
                ->where('id', '!=', $id)
                ->update(['is_latest' => 0]);

            $version->is_latest = 1;
        } 
        // If deactivating
        else {
            $version->is_latest = 0;
        }

        $version->save();

        // Front-end notification
        $this->dispatch('status-updated', [
            'message' => $version->is_latest
                ? 'Latest version activated successfully!'
                : 'Latest version deactivated successfully!',
        ]);
    });
    }

    public $showModelEditVersion = false;
    public $editVersionId, $updateVersionId, $updateVersion, $updateReleaseDate, $updateTitle, $updateDescription, $updateIsLatest;


    public function editVersion($id)
    {
        $version = Versions::findOrFail($id);

        $this->editVersionId = $version->id;
        $this->updateVersionId = $version->version_id;
        $this->updateVersion = $version->version;
        $this->updateReleaseDate = $version->release_date->format('Y-m-d');
        $this->updateTitle = $version->title;
        $this->updateDescription = $version->description;
        $this->updateIsLatest = $version->is_latest;

        $this->showModelEditVersion = true; // ensure modal is open
    }

    public function updateVersionList()
    {
        $this->validate([
            'updateVersionId' => [
                'required',
                'string',
                'regex:/^[VER]{3}\d{3}$/',
                Rule::unique('versions', 'version_id')->ignore($this->editVersionId),
            ],
            'updateVersion' => [
                'required',
                'string',
                'max:50',
                Rule::unique('versions', 'version')->ignore($this->editVersionId),
            ],
            'updateReleaseDate' => 'required|date',
            'updateTitle' => 'required|string|max:50',
            'updateDescription' => 'required|string',
            'updateIsLatest' => 'required|boolean',
        ]);

        try{

            Versions::where('id', $this->editVersionId)->update([
                'version_id' => $this->updateVersionId,
                'version' => $this->updateVersion,
                'release_date' => $this->updateReleaseDate,
                'title' => $this->updateTitle,
                'description' => $this->updateDescription,
                'is_latest' => $this->updateIsLatest,
            ]);


            $this->showModelEditVersion = false;

            session()->flash('message', '✅ Version updated successfully!');

            $this->reset(['updateVersionId', 'updateVersion', 'updateReleaseDate', 'updateTitle', 'updateDescription', 'updateIsLatest', 'editVersionId']);

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
        $versions = Versions::orderBy('version_id')->paginate(50);
        return view('livewire.main-tables.main-tables-versions', compact('versions'));
    }
}
