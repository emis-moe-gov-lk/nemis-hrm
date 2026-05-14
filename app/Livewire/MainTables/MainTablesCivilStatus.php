<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use App\Models\CivilStatus;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MainTablesCivilStatus extends Component
{
    public $showModelNewCivilStatus = false;
    public $civilStatusId, $civilStatusName;

    protected function rules()
    {
        if($this->editCivilStatusId){
            return [
                'updateCivilStatusId' => [
                    'required',
                    'string',
                    'regex:/^C\d{2}$/', // Matches CT followed by 5 digits (C99)
                    'max:3',
                    Rule::unique('civil_statuses', 'civil_status_id')->ignore($this->editCivilStatusId),
                ],
                'updateCivilStatus' => 'required|string|max:50',
            ];
        }

        return [
            'civilStatusId' => [
                'required',
                'string',
                'regex:/^C\d{2}$/', // Matches CT followed by 5 digits (C99)
                'max:3',
                'unique:civil_statuses,civil_status_id',
            ],
            'civilStatusName' => 'required|string|max:50',
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // 🔹 Submit form
    public function addNewCivilStatus()
    {
        $validated = $this->validate();

        try{
            CivilStatus::create([
                'civil_status_id' => $this->civilStatusId,
                'civil_status_name' => $this->civilStatusName,
            ]);

            session()->flash('message', '✅ New Civil Status added successfully!');

            // ✅ Close modal
            $this->showModelNewCivilStatus = false;

            // ✅ Reset form fields (but keep modal control variable)
            $this->reset(['civilStatusId', 'civilStatusName']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to save Civil Status data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Civil Status creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }

    }

    public function toggleStatus($id)
    {
        $staCivilStatus = CivilStatus::find($id);

        if ($staCivilStatus) {
            // Toggle between 1 and 0
            $staCivilStatus->active_status = $staCivilStatus->active_status == '1' ? '0' : '1';
            $staCivilStatus->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $staCivilStatus->active_status == '1'
                    ? 'Civil Status activated successfully!'
                    : 'Civil Status deactivated successfully!',
            ]);
        }
    }

    public function deleteCivilStatus($id)
    {
        $delcivilStatus = CivilStatus::find($id);

        if ($delcivilStatus) {
            $delcivilStatus->delete();
            session()->flash('message', 'Civil Status deleted successfully!');
        } else {
            session()->flash('message', 'Civil Status not found!');
        }
    }

    public $showModelEditCivilStatus = false;
    public $updateCivilStatusId, $updateCivilStatus;
    public $editCivilStatusId;

    public function editCivilStatus($id)
    {

        $civilstatus = CivilStatus::findOrFail($id);

        $this->editCivilStatusId = $civilstatus->id;

        $this->updateCivilStatusId = $civilstatus->civil_status_id;
        $this->updateCivilStatus = $civilstatus->civil_status_name;

        $this->showModelEditCivilStatus = true; // ensure modal is open
    }

    public function updateCivilStatusList()
    {
        try{

            $this->validate([
                'updateCivilStatusId' => [
                    'required',
                    'string',
                    'regex:/^C\d{2}$/', // Matches CT followed by 5 digits (C99)
                    'max:3',
                    Rule::unique('civil_statuses', 'civil_status_id')->ignore($this->editCivilStatusId),
                ],
                'updateCivilStatus' => 'required|string|max:50',

            ]);

            CivilStatus::where('id', $this->editCivilStatusId)->update([
                'civil_status_id' => $this->updateCivilStatusId,
                'civil_status_name' => $this->updateCivilStatus,
            ]);


            $this->showModelEditCivilStatus = false;

            session()->flash('message', '✅ Civil Status information updated successfully!');

            $this->reset(['updateCivilStatus', 'updateCivilStatusId', 'editCivilStatusId']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to update Civil Status data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Civil Status update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }
    }


    public function render()
    {
        $civilStatus = CivilStatus::orderBy('civil_status_id')->paginate(50);
        return view('livewire.main-tables.main-tables-civil-status',compact('civilStatus'));
    }
}
