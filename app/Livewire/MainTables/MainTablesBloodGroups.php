<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use App\Models\BloodGroup;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\WithPagination;

class MainTablesBloodGroups extends Component
{
    use WithPagination;
    public $showModelNewBloodGroup = false;
    public $bloodGroupId, $bloodGroup;

    // 🔹 Validation rules
    protected function rules()
    {
        if ($this->editBloodGroupId) {
            // ✅ Editing existing record
            return [
                'updateBloodGroupId' => [
                    'required',
                    'string',
                    'regex:/^[B]{1}\d{2}$/',
                    Rule::unique('blood_groups', 'blood_group_id')->ignore($this->editBloodGroupId),
                ],
                'updateBloodGroup' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('blood_groups', 'blood_group')->ignore($this->editBloodGroupId),
                ],
            ];
        }

        return [
            'bloodGroupId' => [
                'required',
                'string',
                'regex:/^[B]{1}\d{2}$/',
                Rule::unique('blood_groups', 'blood_group_id'),
            ],
            'bloodGroup' => [
                'required',
                'string',
                'max:50',
                Rule::unique('blood_groups', 'blood_group'),
            ],
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
        $this->resetPage();
    }

    // 🔹 Submit form
    public function addNewBloodGroup()
    {
        $validated = $this->validate();
        $this->resetPage();

        try{
            BloodGroup::create([
                'blood_group_id' => $this->bloodGroupId,
                'blood_group' => $this->bloodGroup,
            ]);

            session()->flash('message', '✅ New Blood Group added successfully!');

            // ✅ Close modal
            $this->showModelNewBloodGroup = false;

            // ✅ Reset form fields (but keep modal control variable)
            $this->reset(['blood_group_id', 'blood_group']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to save blood group data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Blood group creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }

    }

    public function deleteBloodGroup($id)
    {
        $bloodgroup = BloodGroup::find($id);

        if ($bloodgroup) {
            $bloodgroup->delete();
            session()->flash('message', 'Blood Group deleted successfully!');
        } else {
            session()->flash('message', 'Blood Group not found!');
        }
    }

    public function toggleStatus($id)
    {
        $bloodgroup = BloodGroup::find($id);

        if ($bloodgroup) {
            // Toggle between 1 and 0
            $bloodgroup->active_status = $bloodgroup->active_status == '1' ? '0' : '1';
            $bloodgroup->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $bloodgroup->active_status == '1'
                    ? 'Blood Group activated successfully!'
                    : 'Blood Group deactivated successfully!',
            ]);
        }
    }

    public $showModelEditBloodGroup = false;
    public $editBloodGroupId, $updateBloodGroupId, $updateBloodGroup;


    public function editBloodGroup($id)
    {
        $bloodGroup = BloodGroup::findOrFail($id);

        $this->editBloodGroupId = $bloodGroup->id;
        $this->updateBloodGroupId = $bloodGroup->blood_group_id;
        $this->updateBloodGroup = $bloodGroup->blood_group;

        $this->showModelEditBloodGroup = true; // ensure modal is open
    }

    public function updateBloodGroupList()
    {
        $this->validate([
            'updateBloodGroupId' => [
                'required',
                'string',
                'regex:/^[B]{1}\d{2}$/',
                Rule::unique('blood_groups', 'blood_group_id')->ignore($this->editBloodGroupId),
            ],
            'updateBloodGroup' => [
                'required',
                'string',
                'max:50',
                Rule::unique('blood_groups', 'blood_group')->ignore($this->editBloodGroupId),
            ],
        ]);

        $this->resetPage();

        try{

            BloodGroup::where('id', $this->editBloodGroupId)->update([
                'blood_group_id' => $this->updateBloodGroupId,
                'blood_group' => $this->updateBloodGroup,
            ]);


            $this->showModelEditBloodGroup = false;

            session()->flash('message', '✅ Blood Group updated successfully!');

            $this->reset(['updateBloodGroupId', 'updateBloodGroup', 'editBloodGroupId']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to update blood group data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Blood group update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $bloodgroup = BloodGroup::orderBy('blood_group_id')->paginate(50);
        return view('livewire.main-tables.main-tables-blood-groups',compact('bloodgroup'));
    }
}
