<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use App\Models\Ethnicity;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MainTablesEthnicities extends Component
{

    public function toggleStatus($id)
    {
        $ethnicity = Ethnicity::find($id);

        if ($ethnicity) {
            // Toggle between 1 and 0
            $ethnicity->active_status = $ethnicity->active_status == '1' ? '0' : '1';
            $ethnicity->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $ethnicity->active_status == '1'
                    ? 'Ethnicity activated successfully!'
                    : 'Ethnicity deactivated successfully!',
            ]);
        }
    }

    public function deleteEthnicity($id)
    {
        $ethnicity = Ethnicity::find($id);

        if ($ethnicity) {
            $ethnicity->delete();
            session()->flash('message', 'Ethnicity deleted successfully!');
        } else {
            session()->flash('message', 'Ethnicity not found!');
        }
    }

    public $showModelNewEthnicity = false;
    public $ethnicityId, $ethnicity;

    protected function rules()
    {
        if($this->editEdnicityId){
            return [
                'updateEthnicityId' => [
                    'required',
                    'string',
                    'regex:/^E\d{2}$/', // Matches E followed by 2 digits (E99)
                    'max:7',
                    Rule::unique('ethnicities', 'ethnicity_id')->ignore($this->editEdnicityId),
                ],
                'updateEthnicity' => 'required|string|max:255',
            ];
        }

        return [
            'ethnicityId' => [
                'required',
                'string',
                'regex:/^E\d{2}$/', // Matches E followed by 2 digits (E99)
                'max:7',
                'unique:ethnicities,ethnicity_id',
            ],
            'ethnicity' => 'required|string|max:255',
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // 🔹 Submit form
    public function addNewEthnicity()
    {
        $validated = $this->validate();

        try{
            Ethnicity::create([
                'ethnicity_id' => $this->ethnicityId,
                'ethnicity_name' => $this->ethnicity,
            ]);

            session()->flash('message', '✅ New Ethnicity added successfully!');

            // ✅ Close modal
            $this->showModelNewEthnicity = false;

            // ✅ Reset form fields (but keep modal control variable)
            $this->reset(['ethnicityId', 'ethnicity']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to save Ethnicity data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Ethnicity creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }

    }

    public $showModelEditEthnicity = false;
    public $editEdnicityId, $updateEthnicityId, $updateEthnicity;


    public function editEthnicity($id)
    {
        $ethnicity = Ethnicity::findOrFail($id);

        $this->editEdnicityId = $ethnicity->id;
        $this->updateEthnicityId = $ethnicity->ethnicity_id;
        $this->updateEthnicity = $ethnicity->ethnicity_name;

        $this->showModelEditEthnicity = true; // ensure modal is open
    }

    public function updateEthnicitylist()
    {
        $this->validate([
            'updateEthnicityId' => [
                'required',
                'string',
                'regex:/^E\d{2}$/', // Matches E followed by 2 digits (E99)
                'max:7',
                Rule::unique('ethnicities', 'ethnicity_id')->ignore($this->editEdnicityId),
            ],
            'updateEthnicity' => 'required|string|max:255',
        ]);

        try{

            Ethnicity::where('id', $this->editEdnicityId)->update([
                'ethnicity_id' => $this->updateEthnicityId,
                'ethnicity_name' => $this->updateEthnicity,
            ]);


            $this->showModelEditEthnicity = false;

            session()->flash('message', '✅ Ethnicity updated successfully!');

            $this->reset(['updateEthnicity', 'updateEthnicityId', 'editEdnicityId']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to update Ethnicity data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Ethnicity update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $ethnicityList = Ethnicity::orderBy('ethnicity_id')->paginate(50);
        return view('livewire.main-tables.main-tables-ethnicities', compact('ethnicityList'));
    }
}
