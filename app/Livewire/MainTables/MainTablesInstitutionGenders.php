<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use Illuminate\Validation\Rule;
use App\Models\InstitutionGender;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MainTablesInstitutionGenders extends Component
{
    public $showModelNewInstitutionGender = false;
    public $institutionGenderId, $institutionGender;

    // 🔹 Validation rules
    protected function rules()
    {
        if ($this->editInstitutionGenderId) {
            // ✅ Editing existing record
            return [
                'updateInstitutionGenderId' => [
                    'required',
                    'string',
                    'regex:/^[IGID]{4}\d{2}$/',
                    Rule::unique('institution_genders', 'gender_id')->ignore($this->editInstitutionGenderId),
                ],
                'updateInstitutionGender' => [
                    'required',
                    'string',
                    'max:50',
                ],
            ];
        }

        return [
            'institutionGenderId' => [
                'required',
                'string',
                'regex:/^[IGID]{4}\d{2}$/',
                Rule::unique('institution_genders', 'gender_id'),
            ],
            'institutionGender' => [
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
    public function addNewInstitutionGender()
    {
        $validated = $this->validate();

        try{
            InstitutionGender::create([
                'gender_id' => $this->institutionGenderId,
                'name' => $this->institutionGender,
            ]);

            session()->flash('message', '✅ New Institution Gender added successfully!');

            // ✅ Close modal
            $this->showModelNewInstitutionGender = false;

            // ✅ Reset form fields (but keep modal control variable)
            $this->reset(['institutionGenderId', 'institutionGender']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to save Institution Genders data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Institution Genders creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }

    }

    public function deleteInstitutionGender($id)
    {
        $institutiongender = InstitutionGender::find($id);

        if ($institutiongender) {
            $institutiongender->delete();
            session()->flash('message', '✅ Institution Genders deleted successfully!');
        } else {
            session()->flash('message', 'Institution Genders not found!');
        }
    }

    public function toggleStatus($id)
    {
        $institutiongenders = InstitutionGender::find($id);

        if ($institutiongenders) {
            // Toggle between 1 and 0
            $institutiongenders->active_status = $institutiongenders->active_status == '1' ? '0' : '1';
            $institutiongenders->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $institutiongenders->active_status == '1'
                    ? 'Institution Genders activated successfully!'
                    : 'Institution Genders deactivated successfully!',
            ]);
        }
    }

    public $showModelEditInstitutionGender = false;
    public $editInstitutionGenderId, $updateInstitutionGenderId, $updateInstitutionGender;


    public function editInstitutionGender($id)
    {
        $institutiongender = InstitutionGender::findOrFail($id);

        $this->editInstitutionGenderId = $institutiongender->id;
        $this->updateInstitutionGenderId = $institutiongender->gender_id;
        $this->updateInstitutionGender = $institutiongender->name;

        $this->showModelEditInstitutionGender = true; // ensure modal is open
    }

    public function updateInstitutionGenderList()
    {
        $this->validate([
            'updateInstitutionGenderId' => [
                'required',
                'string',
                'regex:/^[IGID]{4}\d{2}$/',
                Rule::unique('institution_genders', 'gender_id')->ignore($this->editInstitutionGenderId),
            ],
            'updateInstitutionGender' => [
                'required',
                'string',
                'max:50',
            ],
        ]);

        try{

            InstitutionGender::where('id', $this->editInstitutionGenderId)->update([
                'gender_id' => $this->updateInstitutionGenderId,
                'name' => $this->updateInstitutionGender,
            ]);


            $this->showModelEditInstitutionGender = false;

            session()->flash('message', '✅ Institution Genders updated successfully!');

            $this->reset(['updateInstitutionGenderId', 'updateInstitutionGender', 'editInstitutionGenderId']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to update institution genders data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Institution genders update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $institutiongenders = InstitutionGender::orderBy('gender_id')->paginate(50);
        return view('livewire.main-tables.main-tables-institution-genders', compact('institutiongenders'));
    }
}
