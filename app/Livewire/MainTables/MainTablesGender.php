<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use App\Models\GenderList;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MainTablesGender extends Component
{
    public function toggleStatus($id)
    {
        $gender = GenderList::find($id);

        if ($gender) {
            // Toggle between 1 and 0
            $gender->active_status = $gender->active_status == '1' ? '0' : '1';
            $gender->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $gender->active_status == '1'
                    ? 'Gender activated successfully!'
                    : 'Gender deactivated successfully!',
            ]);
        }
    }

    public function deleteGender($id)
    {
        $gender = GenderList::find($id);

        if ($gender) {
            $gender->delete();
            session()->flash('message', 'Gender deleted successfully!');
        } else {
            session()->flash('message', 'Gender not found!');
        }
    }

    public $showModelNewGender = false;
    public $genderId, $gender;

    protected function rules()
    {
        // if($this->editGenderId){
        //     return [
        //         'updateGenderId' => [
        //             'required',
        //             'string',
        //             'regex:/^G\d{2}$/', // Matches G followed by 2 digits (G99)
        //             'max:3',
        //             Rule::unique('gender_lists', 'gender_id')->ignore($this->editGenderId),
        //         ],
        //         'updateGender' => 'required|string|max:255',
        //     ];
        // }

        return [
            'genderId' => [
                'required',
                'string',
                'regex:/^G\d{2}$/', // Matches G followed by 2 digits (G99)
                'max:3',
                'unique:gender_lists,gender_id',
            ],
            'gender' => 'required|string|max:255',
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // 🔹 Submit form
    public function addNewGender()
    {
        $validated = $this->validate();

        try{
            GenderList::create([
                'gender_id' => $this->genderId,
                'gender_name' => $this->gender,
            ]);

            session()->flash('message', '✅ New Gender added successfully!');

            // ✅ Close modal
            $this->showModelNewGender = false;

            // ✅ Reset form fields (but keep modal control variable)
            $this->reset(['genderId', 'gender']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to save Gender data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gender creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }

    }


    public $showModelEditGender = false;
    public $editGenderId, $updateGenderId, $updateGender;


    public function editGenderList($id)
    {
        $gender = GenderList::findOrFail($id);

        $this->editGenderId = $gender->id;
        $this->updateGenderId = $gender->gender_id;
        $this->updateGender = $gender->gender_name;

        $this->showModelEditGender = true; // ensure modal is open
    }

    public function updateGenderList()
    {
        $this->validate([
            'updateGenderId' => [
                'required',
                'string',
                'regex:/^G\d{2}$/', // Matches G followed by 2 digits (G99)
                'max:7',
                Rule::unique('gender_lists', 'gender_id')->ignore($this->editGenderId),
            ],
            'updateGender' => 'required|string|max:255',
        ]);

        try{

            GenderList::where('id', $this->editGenderId)->update([
                'gender_id' => $this->updateGenderId,
                'gender_name' => $this->updateGender,
            ]);


            $this->showModelEditGender = false;

            session()->flash('message', '✅ Gender updated successfully!');

            $this->reset(['updateGender', 'updateGenderId', 'editGenderId']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to update Gender data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gender update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }
    }


    public function render()
    {
        $genderList = GenderList::orderBy('gender_id')->paginate(50);
        return view('livewire.main-tables.main-tables-gender', compact('genderList'));
    }
}
