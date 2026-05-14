<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\MediumOfInstruction;
use Illuminate\Support\Facades\Log;

class MainTablesMediumOfInstructions extends Component
{
    public $showModelNewMediumOfInstructions = false;
    public $mediumOfInstructionsId, $mediumOfInstructions;

    // 🔹 Validation rules
    protected function rules()
    {
        if ($this->editMediumOfInstructionsId) {
            // ✅ Editing existing record
            return [
                'updateMediumOfInstructionsId' => [
                    'required',
                    'string',
                    'regex:/^[MED]{3}\d{2}$/',
                    Rule::unique('medium_of_instructions', 'medium_id')->ignore($this->editMediumOfInstructionsId),
                ],
                'updateMediumOfInstructions' => [
                    'required',
                    'string',
                    'max:50',
                ],
            ];
        }

        return [
            'mediumOfInstructionsId' => [
                'required',
                'string',
                'regex:/^[MED]{3}\d{2}$/',
                Rule::unique('medium_of_instructions', 'medium_id'),
            ],
            'mediumOfInstructions' => [
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
    public function addNewMediumOfInstructions()
    {
        $validated = $this->validate();

        try{
            MediumOfInstruction::create([
                'medium_id' => $this->mediumOfInstructionsId,
                'name' => $this->mediumOfInstructions,
            ]);

            session()->flash('message', '✅ New Medium Of Instruction added successfully!');

            // ✅ Close modal
            $this->showModelNewMediumOfInstructions = false;

            // ✅ Reset form fields (but keep modal control variable)
            $this->reset(['mediumOfInstructionsId', 'mediumOfInstructions']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to save Medium Of Instructions data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Medium Of Instructions creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }

    }

    public function deleteMediumOfInstructions($id)
    {
        $mediumofinstructions = MediumOfInstruction::find($id);

        if ($mediumofinstructions) {
            $mediumofinstructions->delete();
            session()->flash('message', '✅ Medium Of Instructions deleted successfully!');
        } else {
            session()->flash('message', 'Medium Of Instructions not found!');
        }
    }

    public function toggleStatus($id)
    {
        $mediumofinstructions = MediumOfInstruction::find($id);

        if ($mediumofinstructions) {
            // Toggle between 1 and 0
            $mediumofinstructions->active_status = $mediumofinstructions->active_status == '1' ? '0' : '1';
            $mediumofinstructions->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $mediumofinstructions->active_status == '1'
                    ? 'Medium Of Instructions activated successfully!'
                    : 'Medium Of Instructions deactivated successfully!',
            ]);
        }
    }

    public $showModelEditMediumOfInstructions = false;
    public $editMediumOfInstructionsId, $updateMediumOfInstructionsId, $updateMediumOfInstructions;


    public function editMediumOfInstructions($id)
    {
        $mediumofinstructions = MediumOfInstruction::findOrFail($id);

        $this->editMediumOfInstructionsId = $mediumofinstructions->id;
        $this->updateMediumOfInstructionsId = $mediumofinstructions->medium_id;
        $this->updateMediumOfInstructions = $mediumofinstructions->name;

        $this->showModelEditMediumOfInstructions = true; // ensure modal is open
    }

    public function updateMediumOfInstructionList()
    {
        $this->validate([
            'updateMediumOfInstructionsId' => [
                'required',
                'string',
                'regex:/^[MED]{3}\d{2}$/',
                Rule::unique('medium_of_instructions', 'medium_id')->ignore($this->editMediumOfInstructionsId),
            ],
            'updateMediumOfInstructions' => [
                'required',
                'string',
                'max:50',
            ],
        ]);

        try{

            MediumOfInstruction::where('id', $this->editMediumOfInstructionsId)->update([
                'medium_id' => $this->updateMediumOfInstructionsId,
                'name' => $this->updateMediumOfInstructions,
            ]);


            $this->showModelEditMediumOfInstructions = false;

            session()->flash('message', '✅ Medium Of Instructions updated successfully!');

            $this->reset(['updateMediumOfInstructionsId', 'updateMediumOfInstructions', 'editMediumOfInstructionsId']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to update medium of instructions data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Medium of instructions update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $mediumofinstructions = MediumOfInstruction::orderBy('medium_id')->paginate(50);
        return view('livewire.main-tables.main-tables-medium-of-instructions', compact('mediumofinstructions'));
    }
}
