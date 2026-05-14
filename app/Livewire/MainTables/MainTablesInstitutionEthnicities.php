<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\InstitutionEthnisity;

class MainTablesInstitutionEthnicities extends Component
{
    public $showModelNewEthnicity = false;
    public $institutionEthnicitiesId, $institutionEthnicities;

    // 🔹 Validation rules
    protected function rules()
    {
        if ($this->editInstitutionEthnicitiesId) {
            // ✅ Editing existing record
            return [
                'updateInstitutionEthnicitiesId' => [
                    'required',
                    'string',
                    'regex:/^[SETH]{4}\d{2}$/',
                    Rule::unique('institution_ethnisities', 'ethnicity_id')->ignore($this->editInstitutionEthnicitiesId),
                ],
                'updateInstitutionEthnicities' => [
                    'required',
                    'string',
                    'max:50',
                ],
            ];
        }

        return [
            'institutionEthnicitiesId' => [
                'required',
                'string',
                'regex:/^[SETH]{4}\d{2}$/',
                Rule::unique('institution_ethnisities', 'ethnicity_id'),
            ],
            'institutionEthnicities' => [
                'required',
                'string',
                'max:50',
            ],
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // 🔹 Submit form
    public function addNewInstitutionEthnicities()
    {
        $validated = $this->validate();

        try{
            InstitutionEthnisity::create([
                'ethnicity_id' => $this->institutionEthnicitiesId,
                'ethnicity_name' => $this->institutionEthnicities,
            ]);

            session()->flash('message', '✅ New Institution Ethnicities added successfully!');

            // ✅ Close modal
            $this->showModelNewInstitutionEthnicities = false;

            // ✅ Reset form fields (but keep modal control variable)
            $this->reset(['institutionEthnicitiesId', 'institutionEthnicities']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to save Institution Ethnicities data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Institution Ethnicities creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }

    }

    public function deleteInstitutionEthnicities($id)
    {
        $institutionethnicities = InstitutionEthnisity::find($id);

        if ($institutionethnicities) {
            $institutionethnicities->delete();
            session()->flash('message', '✅ Institution Ethnicities deleted successfully!');
        } else {
            session()->flash('message', 'Institution Ethnicities not found!');
        }
    }

    public function toggleStatus($id)
    {
        $institutionethnicities = InstitutionEthnisity::find($id);

        if ($institutionethnicities) {
            // Toggle between 1 and 0
            $institutionethnicities->active_status = $institutionethnicities->active_status == '1' ? '0' : '1';
            $institutionethnicities->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $institutionethnicities->active_status == '1'
                    ? 'Institution Ethnicities activated successfully!'
                    : 'Institution Ethnicities deactivated successfully!',
            ]);
        }
    }

    public $showModelEditInstitutionEthnicities = false;
    public $editInstitutionEthnicitiesId, $updateInstitutionEthnicitiesId, $updateInstitutionEthnicities;


    public function editInstitutionEthnicities($id)
    {
        $institutionethnicities = InstitutionEthnisity::findOrFail($id);

        $this->editInstitutionEthnicitiesId = $institutionethnicities->id;
        $this->updateInstitutionEthnicitiesId = $institutionethnicities->ethnicity_id;
        $this->updateInstitutionEthnicities = $institutionethnicities->ethnicity_name;

        $this->showModelEditInstitutionEthnicities = true; // ensure modal is open
    }

    public function updateInstitutionEthnicitiesList()
    {
        $this->validate([
            'updateInstitutionEthnicitiesId' => [
                'required',
                'string',
                'regex:/^[SETH]{4}\d{2}$/',
                Rule::unique('institution_ethnisities', 'ethnicity_id')->ignore($this->editInstitutionEthnicitiesId),
            ],
            'updateInstitutionEthnicities' => [
                'required',
                'string',
                'max:50',
            ],
        ]);

        try{

            InstitutionEthnisity::where('id', $this->editInstitutionEthnicitiesId)->update([
                'ethnicity_id' => $this->updateInstitutionEthnicitiesId,
                'ethnicity_name' => $this->updateInstitutionEthnicities,
            ]);


            $this->showModelEditInstitutionEthnicities = false;

            session()->flash('message', '✅ Institution Ethnicities updated successfully!');

            $this->reset(['updateInstitutionEthnicitiesId', 'updateInstitutionEthnicities', 'editInstitutionEthnicitiesId']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to update institution ethnicities data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Institution ethnicities update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $institutionethnicities = InstitutionEthnisity::orderBy('ethnicity_id')->paginate(50);
        return view('livewire.main-tables.main-tables-institution-ethnicities', compact('institutionethnicities'));
    }
}
