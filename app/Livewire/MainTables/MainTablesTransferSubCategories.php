<?php

namespace App\Livewire\MainTables;

use App\Models\OfficeLevel;
use App\Models\TeacherTransferSubCategory;
use App\Support\Transfer\TransferSubCategoryRules;
use Illuminate\Validation\Rule;
use Livewire\Component;

class MainTablesTransferSubCategories extends Component
{
    public bool $showEditTransferCategoryModal = false;

    public ?int $editSubCategoryId = null;

    public string $updateTransferSubCategoryId = '';
    public string $updateCode = '';
    public string $updateName = '';
    public string $updateDescription = '';
    public string $updatePolicyOfficeLevelId = '';
    public string $updateFirstBoardOfficeLevelId = '';
    public string $updateSecondBoardOfficeLevelId = '';
    public bool $updateRequiresTargetProvinceSelection = false;
    public string $updateZoneScopeMode = '';
    public string $updateInstitutionScopeMode = '';
    public int|string $updateDisplayOrder = 1;

    public array $zoneScopeOptions = [];
    public array $institutionScopeOptions = [];

    public function mount(): void
    {
        $this->zoneScopeOptions = [
            TransferSubCategoryRules::ZONE_SCOPE_CURRENT_ZONE_ONLY => 'Current zone only',
            TransferSubCategoryRules::ZONE_SCOPE_SOURCE_PROVINCE_ONLY => 'Any zone in current province',
            TransferSubCategoryRules::ZONE_SCOPE_SELECTED_TARGET_PROVINCE => 'Selected target province only',
        ];

        $this->institutionScopeOptions = [
            TransferSubCategoryRules::INSTITUTION_SCOPE_PROVINCIAL_ONLY => 'Provincial institutions only',
            TransferSubCategoryRules::INSTITUTION_SCOPE_NATIONAL_ONLY => 'National schools only',
        ];
    }

    protected function rules(): array
    {
        return [
            'updateName' => ['required', 'string', 'max:255'],
            'updateDescription' => ['nullable', 'string', 'max:500'],
            'updatePolicyOfficeLevelId' => ['required', 'string', Rule::exists('office_levels', 'office_level_id')],
            'updateFirstBoardOfficeLevelId' => ['required', 'string', Rule::exists('office_levels', 'office_level_id')],
            'updateSecondBoardOfficeLevelId' => ['nullable', 'string', Rule::exists('office_levels', 'office_level_id')],
            'updateRequiresTargetProvinceSelection' => ['boolean'],
            'updateZoneScopeMode' => ['required', 'string', Rule::in(array_keys($this->zoneScopeOptions))],
            'updateInstitutionScopeMode' => ['required', 'string', Rule::in(array_keys($this->institutionScopeOptions))],
            'updateDisplayOrder' => ['required', 'integer', 'min:1'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'updateName' => 'category name',
            'updateDescription' => 'description',
            'updatePolicyOfficeLevelId' => 'policy office level',
            'updateFirstBoardOfficeLevelId' => 'first board office level',
            'updateSecondBoardOfficeLevelId' => 'second board office level',
            'updateRequiresTargetProvinceSelection' => 'target province requirement',
            'updateZoneScopeMode' => 'zone scope',
            'updateInstitutionScopeMode' => 'institution scope',
            'updateDisplayOrder' => 'display order',
        ];
    }

    public function updated(string $propertyName): void
    {
        if (!str_starts_with($propertyName, 'update')) {
            return;
        }

        $this->validateOnly($propertyName);
    }

    public function editTransferCategory(int $id): void
    {
        $subCategory = TeacherTransferSubCategory::query()->findOrFail($id);

        $this->editSubCategoryId = $subCategory->id;
        $this->updateTransferSubCategoryId = (string) $subCategory->transfer_sub_category_id;
        $this->updateCode = (string) $subCategory->code;
        $this->updateName = (string) $subCategory->name;
        $this->updateDescription = (string) ($subCategory->description ?? '');
        $this->updatePolicyOfficeLevelId = (string) $subCategory->policy_office_level_id;
        $this->updateFirstBoardOfficeLevelId = (string) $subCategory->first_board_office_level_id;
        $this->updateSecondBoardOfficeLevelId = (string) ($subCategory->second_board_office_level_id ?? '');
        $this->updateRequiresTargetProvinceSelection = (bool) $subCategory->requires_target_province_selection;
        $this->updateZoneScopeMode = (string) $subCategory->zone_scope_mode;
        $this->updateInstitutionScopeMode = (string) $subCategory->institution_scope_mode;
        $this->updateDisplayOrder = (int) $subCategory->display_order;

        $this->resetValidation();
        $this->showEditTransferCategoryModal = true;
    }

    public function updateTransferCategory(): void
    {
        $validated = $this->validate();

        if ($this->updateCode !== TransferSubCategoryRules::CODE_NATIONAL_SCHOOL) {
            $validated['updateSecondBoardOfficeLevelId'] = null;
        }

        TeacherTransferSubCategory::query()
            ->whereKey($this->editSubCategoryId)
            ->update([
                'name' => $validated['updateName'],
                'description' => $validated['updateDescription'],
                'policy_office_level_id' => $validated['updatePolicyOfficeLevelId'],
                'first_board_office_level_id' => $validated['updateFirstBoardOfficeLevelId'],
                'second_board_office_level_id' => $validated['updateSecondBoardOfficeLevelId'],
                'requires_target_province_selection' => $validated['updateRequiresTargetProvinceSelection'],
                'zone_scope_mode' => $validated['updateZoneScopeMode'],
                'institution_scope_mode' => $validated['updateInstitutionScopeMode'],
                'display_order' => $validated['updateDisplayOrder'],
            ]);

        $this->showEditTransferCategoryModal = false;
        $this->resetEditState();

        session()->flash('message', 'Transfer category updated successfully.');
    }

    public function toggleStatus(int $id): void
    {
        $subCategory = TeacherTransferSubCategory::query()->findOrFail($id);

        $subCategory->active_status = !$subCategory->active_status;
        $subCategory->save();

        session()->flash(
            'message',
            $subCategory->active_status
                ? 'Transfer category enabled successfully.'
                : 'Transfer category disabled successfully.'
        );
    }

    public function zoneScopeLabel(?string $mode): string
    {
        return $this->zoneScopeOptions[$mode] ?? 'Not configured';
    }

    public function institutionScopeLabel(?string $mode): string
    {
        return $this->institutionScopeOptions[$mode] ?? 'Not configured';
    }

    public function render()
    {
        return view('livewire.main-tables.main-tables-transfer-sub-categories', [
            'transferSubCategories' => TeacherTransferSubCategory::query()
                ->with(['policyOfficeLevel', 'firstBoardOfficeLevel', 'secondBoardOfficeLevel'])
                ->orderBy('display_order')
                ->orderBy('id')
                ->get(),
            'officeLevels' => OfficeLevel::query()
                ->active()
                ->orderBy('office_level_rank')
                ->get(),
        ]);
    }

    protected function resetEditState(): void
    {
        $this->reset([
            'editSubCategoryId',
            'updateTransferSubCategoryId',
            'updateCode',
            'updateName',
            'updateDescription',
            'updatePolicyOfficeLevelId',
            'updateFirstBoardOfficeLevelId',
            'updateSecondBoardOfficeLevelId',
            'updateRequiresTargetProvinceSelection',
            'updateZoneScopeMode',
            'updateInstitutionScopeMode',
            'updateDisplayOrder',
        ]);

        $this->updateRequiresTargetProvinceSelection = false;
        $this->updateDisplayOrder = 1;
    }
}
