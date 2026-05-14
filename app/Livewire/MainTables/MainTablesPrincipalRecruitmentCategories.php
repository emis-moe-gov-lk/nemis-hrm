<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\PrincipalRecruitmentCategory;

class MainTablesPrincipalRecruitmentCategories extends Component
{
    public $showModelNewPrincipalRecruitmentCategory = false;
    public $principalRecruitmentCategoryId, $principalRecruitmentCategoryName;

    // 🔹 Validation rules
    protected function rules()
    {
        if ($this->editPrincipalRecruitmentCategoryId) {
            // ✅ Editing existing record
            return [
                'updatePrincipalRecruitmentCategoryId' => [
                    'required',
                    'string',
                    'regex:/^[PRC]{3}\d{3}$/',
                    Rule::unique('principal_recruitment_categories', 'category_id')->ignore($this->editPrincipalRecruitmentCategoryId),
                ],
                'updatePrincipalRecruitmentCategoryName' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('principal_recruitment_categories', 'category_name')->ignore($this->editPrincipalRecruitmentCategoryId),
                ],
            ];
        }

        return [
            'principalRecruitmentCategoryId' => [
                'required',
                'string',
                'regex:/^[PRC]{3}\d{3}$/',
                Rule::unique('principal_recruitment_categories', 'category_id'),
            ],
            'principalRecruitmentCategoryName' => [
                'required',
                'string',
                'max:255',
                Rule::unique('principal_recruitment_categories', 'category_name'),
            ],
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // 🔹 Submit form
    public function addNewPrincipalRecruitmentCategory()
    {
        $validated = $this->validate();

        try{
            PrincipalRecruitmentCategory::create([
                'category_id' => $this->principalRecruitmentCategoryId,
                'category_name' => $this->principalRecruitmentCategoryName,
            ]);

            session()->flash('message', '✅ New Principal Recruitment Category added successfully!');

            // ✅ Close modal
            $this->showModelNewPrincipalRecruitmentCategory = false;

            // ✅ Reset form fields (but keep modal control variable)
            $this->reset(['principal_recruitment_category_id', 'principal_recruitment_category_name']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to save principal recruitment category data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Principal recruitment category creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }

    }

    public function deletePrincipalRecruitmentCategory($id)
    {
        $principalrecruitmentcategory = PrincipalRecruitmentCategory::find($id);

        if ($principalrecruitmentcategory) {
            $principalrecruitmentcategory->delete();
            session()->flash('message', 'Principal Recruitment Category deleted successfully!');
        } else {
            session()->flash('message', 'Principal Recruitment Category not found!');
        }
    }

    public function toggleStatus($id)
    {
        $principalrecruitmentcategory = PrincipalRecruitmentCategory::find($id);

        if ($principalrecruitmentcategory) {
            // Toggle between 1 and 0
            $principalrecruitmentcategory->active_status = $principalrecruitmentcategory->active_status == '1' ? '0' : '1';
            $principalrecruitmentcategory->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $principalrecruitmentcategory->active_status == '1'
                    ? 'Principal Recruitment Category activated successfully!'
                    : 'Principal Recruitment Category deactivated successfully!',
            ]);
        }
    }

    public $showModelEditPrincipalRecruitmentCategory = false;
    public $editPrincipalRecruitmentCategoryId, $updatePrincipalRecruitmentCategoryId, $updatePrincipalRecruitmentCategoryName;


    public function editPrincipalRecruitmentCategory($id)
    {
        $principalrecruitmentcategory = PrincipalRecruitmentCategory::findOrFail($id);

        $this->editPrincipalRecruitmentCategoryId = $principalrecruitmentcategory->id;
        $this->updatePrincipalRecruitmentCategoryId = $principalrecruitmentcategory->category_id;
        $this->updatePrincipalRecruitmentCategoryName = $principalrecruitmentcategory->category_name;

        $this->showModelEditPrincipalRecruitmentCategory = true; // ensure modal is open
    }

    public function updatePrincipalRecruitmentCategoryList()
    {
        $this->validate([
            'updatePrincipalRecruitmentCategoryId' => [
                'required',
                'string',
                'regex:/^[PRC]{3}\d{3}$/',
                Rule::unique('principal_recruitment_categories', 'category_id')->ignore($this->editPrincipalRecruitmentCategoryId),
            ],
            'updatePrincipalRecruitmentCategoryName' => [
                'required',
                'string',
                'max:255',
                Rule::unique('principal_recruitment_categories', 'category_name')->ignore($this->editPrincipalRecruitmentCategoryId),
            ],
        ]);

        try{

            PrincipalRecruitmentCategory::where('id', $this->editPrincipalRecruitmentCategoryId)->update([
                'category_id' => $this->updatePrincipalRecruitmentCategoryId,
                'category_name' => $this->updatePrincipalRecruitmentCategoryName,
            ]);


            $this->showModelEditPrincipalRecruitmentCategory = false;

            session()->flash('message', '✅ Principal Recruitment Category updated successfully!');

            $this->reset(['updatePrincipalRecruitmentCategoryId', 'updatePrincipalRecruitmentCategoryName', 'editPrincipalRecruitmentCategoryId']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to update principal recruitment category data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Principal recruitment category update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $principalrecruitmentcategories = PrincipalRecruitmentCategory::orderBy('category_id', 'asc')->paginate(50);
        return view('livewire.main-tables.main-tables-principal-recruitment-categories', compact('principalrecruitmentcategories'));
    }
}
