<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use App\Models\TeacherType;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MainTablesTeacherTypes extends Component
{
    public $showModelNewTeacherType = false;
    public $teacherTypeId, $teacherType;

    // 🔹 Validation rules
    protected function rules()
    {
        if ($this->editTeacherTypeId) {
            // ✅ Editing existing record
            return [
                'updateTeacherTypeId' => [
                    'required',
                    'string',
                    'regex:/^[TCHTYPE]{7}\d{3}$/',
                    Rule::unique('teacher_types', 'teacher_types_id')->ignore($this->editTeacherTypeId),
                ],
                'updateTeacherType' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('teacher_types', 'type_name')->ignore($this->editTeacherTypeId),
                ],
            ];
        }

        return [
            'teacherTypeId' => [
                'required',
                'string',
                'regex:/^[TCHTYPE]{7}\d{3}$/',
                Rule::unique('teacher_types', 'teacher_types_id'),
            ],
            'teacherType' => [
                'required',
                'string',
                'max:50',
                Rule::unique('teacher_types', 'type_name'),
            ],
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // 🔹 Submit form
    public function addNewTeacherType()
    {
        $validated = $this->validate();

        try{
            TeacherType::create([
                'teacher_types_id' => $this->teacherTypeId,
                'type_name' => $this->teacherType,
            ]);

            session()->flash('message', '✅ New Teacher Type added successfully!');

            // ✅ Close modal
            $this->showModelNewTeacherType = false;

            // ✅ Reset form fields (but keep modal control variable)
            $this->reset(['teacherTypeId', 'teacherType']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to save teacher type data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Teacher type creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }

    }

    public function deleteTeacherType($id)
    {
        $teacherType = TeacherType::find($id);

        if ($teacherType) {
            $teacherType->delete();
            session()->flash('message', '✅ Teacher Type deleted successfully!');
        } else {
            session()->flash('message', 'Teacher Type not found!');
        }
    }

    public function toggleStatus($id)
    {
        $teacherType = TeacherType::find($id);

        if ($teacherType) {
            // Toggle between 1 and 0
            $teacherType->active_status = $teacherType->active_status == '1' ? '0' : '1';
            $teacherType->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $teacherType->active_status == '1'
                    ? 'Teacher Type activated successfully!'
                    : 'Teacher Type deactivated successfully!',
            ]);
        }
    }

    public $showModelEditTeacherType = false;
    public $editTeacherTypeId, $updateTeacherTypeId, $updateTeacherType;


    public function editTeacherType($id)
    {
        $teacherType = TeacherType::findOrFail($id);

        $this->editTeacherTypeId = $teacherType->id;
        $this->updateTeacherTypeId = $teacherType->teacher_types_id;
        $this->updateTeacherType = $teacherType->type_name;

        $this->showModelEditTeacherType = true; // ensure modal is open
    }

    public function updateTeacherTypeList()
    {
        $this->validate([
            'updateTeacherTypeId' => [
                'required',
                'string',
                'regex:/^[TCHTYPE]{7}\d{3}$/',
                Rule::unique('teacher_types', 'teacher_types_id')->ignore($this->editTeacherTypeId),
            ],
            'updateTeacherType' => [
                'required',
                'string',
                'max:50',
                Rule::unique('teacher_types', 'type_name')->ignore($this->editTeacherTypeId),
            ],
        ]);

        try{

            TeacherType::where('id', $this->editTeacherTypeId)->update([
                'teacher_types_id' => $this->updateTeacherTypeId,
                'type_name' => $this->updateTeacherType,
            ]);


            $this->showModelEditTeacherType = false;

            session()->flash('message', '✅ Teacher Type updated successfully!');

            $this->reset(['updateTeacherTypeId', 'updateTeacherType', 'editTeacherTypeId']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to update teacher type data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Teacher type update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $teacherTypes = TeacherType::orderBy('teacher_types_id')->paginate(50);
        return view('livewire.main-tables.main-tables-teacher-types', compact('teacherTypes'));
    }
}
