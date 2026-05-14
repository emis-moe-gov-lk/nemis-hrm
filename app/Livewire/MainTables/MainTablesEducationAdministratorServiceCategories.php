<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\EducationAdministratorServiceCategory;

class MainTablesEducationAdministratorServiceCategories extends Component
{
    public function deleteSleasCategory($id)
    {
        $sleasCategory = EducationAdministratorServiceCategory::find($id);

        if ($sleasCategory) {
            $sleasCategory->delete();
            session()->flash('message', 'SLEAS Category deleted successfully!');
        } else {
            session()->flash('message', 'SLEAS Category not found!');
        }
    }

    public function toggleStatus($id)
    {
        $sleasCategory = EducationAdministratorServiceCategory::find($id);

        if ($sleasCategory) {
            // Toggle between 1 and 0
            $sleasCategory->active_status = $sleasCategory->active_status == '1' ? '0' : '1';
            $sleasCategory->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $sleasCategory->active_status == '1'
                    ? 'SLEAS Category activated successfully!'
                    : 'SLEAS Category deactivated successfully!',
            ]);
        }
    }

    public $showModelNewSleasCategory = false;
    public $sleasCategoryId, $sleasCategory;

    // 🔹 Validation rules
    protected function rules()
    {
        if ($this->editSleasCategoryId) {
            // ✅ Editing existing record
            return [
                'updateSleasCategoryId' => [
                    'required',
                    'string',
                    'regex:/^[EASC]{4}\d{3}$/',
                    Rule::unique('education_administrator_service_categories', 'category_id')->ignore($this->editSleasCategoryId),
                ],
                'updateSleasCategory' => [
                    'required',
                    'string',
                ],
            ];
        }

        return [
            'sleasCategoryId' => [
                'required',
                'string',
                'regex:/^[EASC]{4}\d{3}$/',
                Rule::unique('education_administrator_service_categories', 'category_id'),
            ],
            'sleasCategory' => [
                'required',
                'string',
            ],
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // 🔹 Submit form
    public function addNewSleasCategory()
    {
        $validated = $this->validate();

        try{
            EducationAdministratorServiceCategory::create([
                'category_id' => $this->sleasCategoryId,
                'category_name' => $this->sleasCategory,
            ]);

            session()->flash('message', '✅ New SLEAS Category added successfully!');

            // ✅ Close modal
            $this->showModelNewSleasCategory = false;

            // ✅ Reset form fields (but keep modal control variable)
            $this->reset(['sleasCategoryId', 'sleasCategory']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to save SLEAS Category data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('SLEAS Category creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }

    }

    public $showModelEditSleasCategory = false;
    public $editSleasCategoryId, $updateSleasCategoryId, $updateSleasCategory;


    public function editSleasCategory($id)
    {
        $sleasCategory = EducationAdministratorServiceCategory::findOrFail($id);

        $this->editSleasCategoryId = $sleasCategory->id;
        $this->updateSleasCategoryId = $sleasCategory->category_id;
        $this->updateSleasCategory = $sleasCategory->category_name;

        $this->showModelEditSleasCategory = true; // ensure modal is open
    }

    public function updateSleasCategoryList()
    {
        $this->validate([
            'updateSleasCategoryId' => [
                'required',
                'string',
                'regex:/^[EASC]{4}\d{3}$/',
                Rule::unique('education_administrator_service_categories', 'category_id')->ignore($this->editSleasCategoryId),
            ],
            'updateSleasCategory' => [
                'required',
                'string',
            ],
        ]);

        try{

            EducationAdministratorServiceCategory::where('id', $this->editSleasCategoryId)->update([
                'category_id' => $this->updateSleasCategoryId,
                'category_name' => $this->updateSleasCategory,
            ]);


            $this->showModelEditSleasCategory = false;

            session()->flash('message', '✅ SLEAS Category updated successfully!');

            $this->reset(['updateSleasCategoryId', 'updateSleasCategory', 'editSleasCategoryId']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to update SLEAS Category data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('SLEAS Category update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $sleasCategories = EducationAdministratorServiceCategory::orderBy('category_id')->paginate(50);
        return view('livewire.main-tables.main-tables-education-administrator-service-categories', compact('sleasCategories'));
    }
}
