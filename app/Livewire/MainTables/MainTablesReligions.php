<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use App\Models\Religion;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MainTablesReligions extends Component
{
    public $showModelNewReligion = false;
    public $religionId, $religionName;

    // 🔹 Validation rules
    protected function rules()
    {
        if ($this->editReligionId) {
            // ✅ Editing existing record
            return [
                'updateReligionId' => [
                    'required',
                    'string',
                    'regex:/^[R]{1}\d{2}$/',
                    Rule::unique('religions', 'religion_id')->ignore($this->editReligionId),
                ],
                'updateReligionName' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('religions', 'religion_name')->ignore($this->editReligionId),
                ],
            ];
        }

        return [
            'religionId' => [
                'required',
                'string',
                'regex:/^[R]{1}\d{2}$/',
                Rule::unique('religions', 'religion_id'),
            ],
            'religionName' => [
                'required',
                'string',
                'max:255',
                Rule::unique('religions', 'religion_name'),
            ],
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // 🔹 Submit form
    public function addNewReligion()
    {
        $validated = $this->validate();

        try{
            Religion::create([
                'religion_id' => $this->religionId,
                'religion_name' => $this->religionName,
            ]);

            session()->flash('message', '✅ New Religion added successfully!');

            // ✅ Close modal
            $this->showModelNewReligion = false;

            // ✅ Reset form fields (but keep modal control variable)
            $this->reset(['religion_id', 'religion_name']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to save religion data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Religion creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }

    }

    public function deleteReligion($id)
    {
        $religion = Religion::find($id);

        if ($religion) {
            $religion->delete();
            session()->flash('message', 'Religion deleted successfully!');
        } else {
            session()->flash('message', 'Religion not found!');
        }
    }

    public function toggleStatus($id)
    {
        $religion = Religion::find($id);

        if ($religion) {
            // Toggle between 1 and 0
            $religion->active_status = $religion->active_status == '1' ? '0' : '1';
            $religion->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $religion->active_status == '1'
                    ? 'Religion activated successfully!'
                    : 'Religion deactivated successfully!',
            ]);
        }
    }

    public $showModelEditReligion = false;
    public $editReligionId, $updateReligionId, $updateReligionName;


    public function editReligion($id)
    {
        $religion = Religion::findOrFail($id);

        $this->editReligionId = $religion->id;
        $this->updateReligionId = $religion->religion_id;
        $this->updateReligionName = $religion->religion_name;

        $this->showModelEditReligion = true; // ensure modal is open
    }

    public function updateReligionList()
    {
        $this->validate([
            'updateReligionId' => [
                'required',
                'string',
                'regex:/^[R]{1}\d{2}$/',
                Rule::unique('religions', 'religion_id')->ignore($this->editReligionId),
            ],
            'updateReligionName' => [
                'required',
                'string',
                'max:255',
                Rule::unique('religions', 'religion_name')->ignore($this->editReligionId),
            ],
        ]);

        try{

            Religion::where('id', $this->editReligionId)->update([
                'religion_id' => $this->updateReligionId,
                'religion_name' => $this->updateReligionName,
            ]);


            $this->showModelEditReligion = false;

            session()->flash('message', '✅ Religion updated successfully!');

            $this->reset(['updateReligionId', 'updateReligionName', 'editReligionId']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to update Sreligion data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Religion update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $religion = Religion::orderBy('religion_id')->paginate(50);
        return view('livewire.main-tables.main-tables-religions', compact('religion'));
    }
}
