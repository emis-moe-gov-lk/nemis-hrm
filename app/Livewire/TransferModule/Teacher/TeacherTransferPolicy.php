<?php

namespace App\Livewire\TransferModule\Teacher;

use Livewire\Component;
use App\Models\OfficeLevel;
use App\Models\Authority;
use App\Models\Service;
use App\Models\TeacherTransferPolicy as TeacherTransferPolicyModel;
use App\Models\TeacherTransferPolicyStep;
use App\Models\TeacherTransferCategory;
use App\Models\TeacherTransferApplication;
use App\Models\TeacherTransferBoard;
use App\Models\InstitutionalFacility;
use App\Models\TeacherTransferPolicyAchievementLevelScore;
use App\Models\TeacherTransferPolicyFacilityScoreRule;
use App\Models\TeacherTransferPolicyScoreRule;
use App\Models\TeacherTransferScoreCriterion;
use App\Models\TeacherTransferSubCategory;
use App\Models\ProvincesList;
use App\Models\Institution;
use App\Models\DistrictsList;
use App\Support\Transfer\TransferAccess;
use App\Support\Transfer\TransferSubCategoryRules;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;

class TeacherTransferPolicy extends Component
{
    public ?int $policyYear = null;
    public ?string $circularNumber = null;
    public ?string $title = null;
    public ?string $description = null;
    public ?string $effectiveDate = null;
    public ?int $minServiceCurrentSchool = null;
    public ?int $minServiceTotal = null;
    public ?int $maxPreferences = null;
    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?string $policyId = null; // Added for editing
    public int|bool $status = 1;
    public array $approvalSteps = [];
    public array $stepDates = [];
    public array $selectedTransferSubCategoryIds = [];
    public array $selectedTransferCategoryBoardLevels = [];
    public array $loadedTeacherTransferCategoryIds = [];
    public array $scoreRules = [];
    public array $scoreCriteriaOptions = [];
    public array $facilityScoreOptions = [];
    public array $achievementLevels = [
        'zonal' => 'Zonal',
        'district' => 'District',
        'provincial' => 'Provincial',
        'national' => 'National',
    ];

    public Collection|array $hierarchyOptions = [];
    public ?string $transferAuthority = null;
    public ?string $transferType = null;
    public Collection|array $authorityOptions = [];
    public Collection|array $serviceOptions = [];
    public array $transferTypeOptions = [
        ['id' => 'annual', 'name' => 'Annual Transfer'],
        ['id' => 'mutual', 'name' => 'Mutual Transfer'],
        ['id' => 'medical', 'name' => 'Medical Grounds'],
        ['id' => 'humanitarian', 'name' => 'Humanitarian Grounds'],
        ['id' => 'special', 'name' => 'Special Request'],
    ];

    public ?string $myOfficeLevel = null;
    public ?string $myOfficeId = null;
    public Collection $transferSubCategoryOptions;

    public array|Collection $provinces = [];
    public array|Collection $nationalSchools = [];
    public ?string $selectedProvince = '';
    public ?string $selectedNationalSchool = '';
    public bool|int $isNsCategoryConsidered = false;

    public function mount($id = null)
    {
        abort_unless(TransferAccess::canManagePolicies(auth()->user()), 403);

        $this->hierarchyOptions = OfficeLevel::active()
            ->orderByDesc('office_level_rank')
            ->get();

        $this->authorityOptions = Authority::active()->get();
        $this->serviceOptions = Service::active()->get();
        $this->provinces = ProvincesList::active()->get();
        $this->transferSubCategoryOptions = TeacherTransferSubCategory::active()
            ->with(['policyOfficeLevel', 'firstBoardOfficeLevel', 'secondBoardOfficeLevel'])
            ->orderBy('display_order')
            ->get();
        $this->loadScoreRuleOptions();
        $this->scoreRules = $this->defaultScoreRules();

        $this->myOfficeLevel = auth()->user()->workplace_name;
        $this->myOfficeId = auth()->user()->workplace_id;
        $this->transferAuthority = auth()->user()->workplace_id;

        if ($id) {
            $this->policyId = $id;

            $policy = TeacherTransferPolicyModel::where('policy_id', $this->policyId)->firstOrFail();
            abort_unless(TransferAccess::canManagePolicy(auth()->user(), $policy), 403);

            if ($policy->is_locked) {
                session()->flash('error', __('This policy is locked and cannot be edited.'));
                return redirect()->route('transfer.transfer-policies');
            }

            $this->loadPolicyData();
        }
    }

    protected function loadPolicyData()
    {
        $policy = TeacherTransferPolicyModel::with([
            'steps',
            'scoreRules',
            'facilityScoreRules',
            'achievementLevelScores',
        ])->where('policy_id', $this->policyId)->firstOrFail();

        $this->policyYear = $policy->policy_year;
        $this->circularNumber = $policy->circular_number;
        $this->title = $policy->title;
        $this->description = $policy->description;
        $this->minServiceCurrentSchool = $policy->min_service_current_school;
        $this->minServiceTotal = $policy->min_service_total;
        $this->maxPreferences = $policy->max_preferences;
        $this->effectiveDate = $policy->effective_date->format('Y-m-d');
        $this->startDate = $policy->application_start_date->format('Y-m-d');
        $this->endDate = $policy->application_end_date->format('Y-m-d');
        $this->transferAuthority = $policy->transfer_authority;
        $this->transferType = $policy->transfer_type;
        $this->selectedProvince = $policy->province_id;
        $this->selectedNationalSchool = $policy->special_institution_id;
        $this->isNsCategoryConsidered = (bool) $policy->is_ns_category_considered;
        $this->status = (int) $policy->active_status;

        if ($this->selectedProvince) {
            $this->loadNationalSchools();
        }

        $this->approvalSteps = [];
        $this->stepDates = [];

        foreach ($policy->steps->sortBy('step_order') as $step) {
            $this->approvalSteps[] = $step->office_level_id;
            $this->stepDates[$step->office_level_id] = [
                'start' => $step->start_date ? $step->start_date->format('Y-m-d') : '',
                'end' => $step->end_date ? $step->end_date->format('Y-m-d') : '',
            ];
        }

        $categories = $policy->categoriesQuery()
            ->with('transferSubCategory')
            ->get();
        $this->loadedTeacherTransferCategoryIds = $categories
            ->whereNotNull('transfer_sub_category_id')
            ->pluck('transfer_category_id')
            ->filter()
            ->values()
            ->all();
        $this->selectedTransferSubCategoryIds = $categories
            ->pluck('transfer_sub_category_id')
            ->filter()
            ->unique()
            ->values()
            ->all();
        $this->selectedTransferCategoryBoardLevels = [];

        foreach ($categories as $category) {
            if (!$category->transfer_sub_category_id) {
                continue;
            }

            $this->selectedTransferCategoryBoardLevels[$category->transfer_sub_category_id] =
                $this->normalizeBoardOfficeLevelForSubCategory(
                    $category->transferSubCategory,
                    $category->office_level_id
                );
        }

        foreach ($this->selectedTransferSubCategoryIds as $transferSubCategoryId) {
            if (isset($this->selectedTransferCategoryBoardLevels[$transferSubCategoryId])) {
                continue;
            }

            $subCategory = $this->transferSubCategoryOptions
                ->firstWhere('transfer_sub_category_id', $transferSubCategoryId);

            $this->selectedTransferCategoryBoardLevels[$transferSubCategoryId] =
                $this->defaultBoardOfficeLevelForSubCategory($subCategory);
        }

        $this->loadPolicyScoreRules($policy);
    }

    public function rules()
    {
        $rules = [
            'policyYear' => 'required|integer|min:2020',
            'circularNumber' => [
                'required',
                'string',
                'max:255',
                Rule::unique('teacher_transfer_policies', 'circular_number')->ignore($this->policyId, 'policy_id'),
            ],
            'title' => 'required|string|max:255',
            'effectiveDate' => 'required|date',
            'minServiceCurrentSchool' => 'required|integer|min:0',
            'minServiceTotal' => 'required|integer|min:0',
            'maxPreferences' => 'required|integer|min:1|max:10',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after:startDate',
            'approvalSteps' => 'required|array|min:1',
            'transferAuthority' => 'required',
            'transferType' => 'required',
        ];

        foreach ($this->approvalSteps as $stepId) {
            $rules["stepDates.{$stepId}.start"] = 'required|date';
            $rules["stepDates.{$stepId}.end"] = 'required|date|after_or_equal:stepDates.' . $stepId . '.start';
        }

        $rules['selectedTransferSubCategoryIds'] = 'required|array|min:1';
        $rules['selectedTransferSubCategoryIds.*'] = 'required|exists:teacher_transfer_sub_categories,transfer_sub_category_id';

        foreach ($this->selectedTransferSubCategoryIds as $transferSubCategoryId) {
            $subCategory = $this->transferSubCategoryOptions
                ->firstWhere('transfer_sub_category_id', $transferSubCategoryId);

            $allowedOfficeLevelIds = $subCategory
                ? TransferSubCategoryRules::allowedPolicyBoardOfficeLevelIdsForCode($subCategory->code)
                : [];

            $rules["selectedTransferCategoryBoardLevels.{$transferSubCategoryId}"] = [
                'required',
                'string',
                Rule::in($allowedOfficeLevelIds),
            ];
        }

        foreach ($this->scoreRules as $criteriaKey => $rule) {
            $rules["scoreRules.{$criteriaKey}.enabled"] = 'boolean';

            if (!($rule['enabled'] ?? false)) {
                continue;
            }

            if ($criteriaKey === 'distance_current_workplace') {
                $rules["scoreRules.{$criteriaKey}.score_per_unit"] = 'required|numeric|min:0';
            }

            if (in_array($criteriaKey, ['age', 'current_station_years'], true)) {
                $rules["scoreRules.{$criteriaKey}.base_value"] = 'required|numeric|min:0';
            }

            if (in_array($criteriaKey, ['current_difficulty_years', 'previous_difficulty_years'], true)) {
                foreach ($this->facilityScoreOptions as $facility) {
                    $rules["scoreRules.{$criteriaKey}.facility_scores.{$facility['id']}"] = 'nullable|numeric|min:0';
                }
            }

            if ($criteriaKey === 'achievements') {
                foreach (array_keys($this->achievementLevels) as $level) {
                    $rules["scoreRules.{$criteriaKey}.level_scores.{$level}"] = 'nullable|numeric|min:0';
                }
            }
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'circularNumber.required' => 'Please enter the circular number for this transfer policy.',
            'circularNumber.unique' => 'This circular number is already used by another transfer policy. Please enter a different circular number before saving.',
        ];
    }

    public function updatedPolicyYear(mixed $value): void
    {
        $this->policyYear = $this->sanitizeWholeNumber($value);
    }

    public function updatedMinServiceCurrentSchool(mixed $value): void
    {
        $this->minServiceCurrentSchool = $this->sanitizeWholeNumber($value);
    }

    public function updatedMinServiceTotal(mixed $value): void
    {
        $this->minServiceTotal = $this->sanitizeWholeNumber($value);
    }

    public function updatedMaxPreferences(mixed $value): void
    {
        $this->maxPreferences = $this->sanitizeWholeNumber($value, 1, 10);
    }

    protected function sanitizeWholeNumber(mixed $value, int $min = 0, ?int $max = null): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $normalized = preg_replace('/[^0-9]/', '', (string) $value);

        if ($normalized === '') {
            return null;
        }

        $number = (int) $normalized;
        $number = max($min, $number);

        if ($max !== null) {
            $number = min($max, $number);
        }

        return $number;
    }

    public function toggleTransferSubCategory(string $transferSubCategoryId): void
    {
        if (!collect($this->transferSubCategoryOptions)->contains('transfer_sub_category_id', $transferSubCategoryId)) {
            return;
        }

        if (in_array($transferSubCategoryId, $this->selectedTransferSubCategoryIds, true)) {
            $this->selectedTransferSubCategoryIds = array_values(array_filter(
                $this->selectedTransferSubCategoryIds,
                fn(string $selectedId) => $selectedId !== $transferSubCategoryId
            ));
            unset($this->selectedTransferCategoryBoardLevels[$transferSubCategoryId]);
        } else {
            $this->selectedTransferSubCategoryIds[] = $transferSubCategoryId;
            $this->selectedTransferSubCategoryIds = collect($this->selectedTransferSubCategoryIds)
                ->unique()
                ->values()
                ->all();

            $subCategory = $this->transferSubCategoryOptions
                ->firstWhere('transfer_sub_category_id', $transferSubCategoryId);

            $this->selectedTransferCategoryBoardLevels[$transferSubCategoryId] =
                $this->defaultBoardOfficeLevelForSubCategory($subCategory);
        }
    }

    public function updatedSelectedTransferCategoryBoardLevels(mixed $value, string $transferSubCategoryId): void
    {
        $subCategory = $this->transferSubCategoryOptions
            ->firstWhere('transfer_sub_category_id', $transferSubCategoryId);

        if (!$subCategory || !$this->isTransferSubCategorySelected($transferSubCategoryId)) {
            unset($this->selectedTransferCategoryBoardLevels[$transferSubCategoryId]);

            return;
        }

        $this->selectedTransferCategoryBoardLevels[$transferSubCategoryId] =
            $this->normalizeBoardOfficeLevelForSubCategory($subCategory, (string) $value);
    }

    public function setTransferCategoryBoardLevel(string $transferSubCategoryId, string $officeLevelId): void
    {
        $subCategory = $this->transferSubCategoryOptions
            ->firstWhere('transfer_sub_category_id', $transferSubCategoryId);

        if (!$subCategory || !$this->isTransferSubCategorySelected($transferSubCategoryId)) {
            return;
        }

        if ($this->isFixedBoardLevelSubCategory($subCategory)) {
            return;
        }

        $this->selectedTransferCategoryBoardLevels[$transferSubCategoryId] =
            $this->normalizeBoardOfficeLevelForSubCategory($subCategory, $officeLevelId);
    }

    public function isTransferSubCategorySelected(string $transferSubCategoryId): bool
    {
        return in_array($transferSubCategoryId, $this->selectedTransferSubCategoryIds, true);
    }

    public function toggleApprovalStep(string $stepId)
    {
        if (in_array($stepId, $this->approvalSteps)) {
            $this->approvalSteps = array_diff($this->approvalSteps, [$stepId]);
            unset($this->stepDates[$stepId]);
        } else {
            $this->approvalSteps[] = $stepId;
            $this->stepDates[$stepId] = ['start' => '', 'end' => ''];
        }

        // Re-order based on hierarchyOptions to maintain sequence
        $orderedSteps = [];
        foreach ($this->hierarchyOptions as $option) {
            if (in_array($option->office_level_id, $this->approvalSteps)) {
                $orderedSteps[] = $option->office_level_id;
            }
        }
        $this->approvalSteps = $orderedSteps;
    }

    public function isApprovalStepSelected(string $stepId): bool
    {
        return in_array($stepId, $this->approvalSteps, true);
    }

    public function approvalStepNumber(string $stepId): ?int
    {
        $index = array_search($stepId, $this->approvalSteps, true);

        return $index === false ? null : $index + 1;
    }


    public function save()
    {
        $this->validate();
        $this->validateSelectedTransferSubCategories();

        try {

            DB::beginTransaction();

            $data = [
                'policy_year' => $this->policyYear,
                'circular_number' => $this->circularNumber,
                'title' => $this->title,
                'description' => $this->description,
                'min_service_current_school' => $this->minServiceCurrentSchool,
                'min_service_total' => $this->minServiceTotal,
                'max_preferences' => $this->maxPreferences,
                'effective_date' => $this->effectiveDate,
                'application_start_date' => $this->startDate,
                'application_end_date' => $this->endDate,
                'transfer_authority' => $this->transferAuthority,
                'transfer_type' => $this->transferType,
                'province_id' => $this->selectedProvince,
                'special_institution_id' => $this->selectedNationalSchool,
                'is_ns_category_considered' => (bool) $this->isNsCategoryConsidered,
                'active_status' => (bool) $this->status,
            ];

            if ($this->policyId) {
                $policy = TeacherTransferPolicyModel::where('policy_id', $this->policyId)->firstOrFail();
                $policy->update($data);
                // Clear existing steps and recreate
                TeacherTransferPolicyStep::where('policy_id', $this->policyId)->delete();
            } else {
                $policy = TeacherTransferPolicyModel::create($data);
            }

            foreach ($this->approvalSteps as $index => $stepId) {
                TeacherTransferPolicyStep::create([
                    'policy_id'      => $policy->policy_id,
                    'office_level_id' => $stepId,
                    'step_order'     => $index + 1,
                    'start_date'     => $this->stepDates[$stepId]['start'] ?? null,
                    'end_date'       => $this->stepDates[$stepId]['end'] ?? null,
                ]);
            }

            $this->syncTransferCategories($policy);

            $this->syncPolicyScoreRules($policy);


            DB::commit();

            session()->flash('success', $this->policyId ? 'Transfer Policy updated successfully.' : 'Transfer Policy created successfully.');

            return redirect()->route('transfer.transfer-policies');
        } catch (\Throwable $e) {

            DB::rollBack();

            report($e);

            $action = $this->policyId ? 'update' : 'create';
            session()->flash('error', 'Failed to ' . $action . ' Transfer Policy. Please check the policy details and try again.');
        }
    }

    protected function transferCategoryDeleteBlocks(array $categoryIds): array
    {
        $categoryIds = array_values(array_unique(array_filter($categoryIds)));

        if (empty($categoryIds)) {
            return [];
        }

        $categories = TeacherTransferCategory::query()
            ->whereIn('transfer_category_id', $categoryIds)
            ->get()
            ->keyBy('transfer_category_id');

        $applicationCounts = TeacherTransferApplication::query()
            ->whereIn('transfer_category', $categoryIds)
            ->pluck('transfer_category')
            ->countBy();

        $boardCounts = TeacherTransferBoard::query()
            ->whereIn('transfer_category_id', $categoryIds)
            ->pluck('transfer_category_id')
            ->countBy();

        return collect($categoryIds)
            ->map(function (string $categoryId) use ($categories, $applicationCounts, $boardCounts) {
                $applicationCount = (int) ($applicationCounts[$categoryId] ?? 0);
                $boardCount = (int) ($boardCounts[$categoryId] ?? 0);

                if ($applicationCount === 0 && $boardCount === 0) {
                    return null;
                }

                $category = $categories->get($categoryId);

                return [
                    'id' => $categoryId,
                    'name' => $category?->transfer_category_name ?? $categoryId,
                    'applications' => $applicationCount,
                    'boards' => $boardCount,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    protected function formatTeacherTransferCategoryDeleteBlockMessage(array $deleteBlocks): string
    {
        $details = collect($deleteBlocks)
            ->map(function (array $block) {
                $usage = [];

                if ($block['applications'] > 0) {
                    $usage[] = $block['applications'] . ' transfer application(s)';
                }

                if ($block['boards'] > 0) {
                    $usage[] = $block['boards'] . ' transfer board(s)';
                }

                return $block['name'] . ' (' . $block['id'] . ') is used by ' . implode(' and ', $usage);
            })
            ->implode('; ');

        return 'Cannot remove transfer category because it is already in use. ' . $details . '. Keep this category in the policy for existing records.';
    }

    protected function formatTeacherTransferCategoryBoardLevelChangeBlockMessage(array $blocks): string
    {
        $details = collect($blocks)
            ->map(function (array $block) {
                $usage = [];

                if ($block['applications'] > 0) {
                    $usage[] = $block['applications'] . ' transfer application(s)';
                }

                if ($block['boards'] > 0) {
                    $usage[] = $block['boards'] . ' transfer board(s)';
                }

                return $block['name'] . ' (' . $block['id'] . ') is used by ' . implode(' and ', $usage);
            })
            ->implode('; ');

        return 'Cannot change the board level for this transfer category because it is already in use. ' . $details . '. Create a new policy or keep the current board level for existing records.';
    }

    protected function validateSelectedTransferSubCategories(): void
    {
        $invalidSelections = [];

        foreach ($this->selectedTransferSubCategoryIds as $transferSubCategoryId) {
            $subCategory = $this->transferSubCategoryOptions
                ->firstWhere('transfer_sub_category_id', $transferSubCategoryId);

            if (!$subCategory) {
                $invalidSelections[] = $transferSubCategoryId;
                continue;
            }

            $officeLevelId = $this->selectedBoardOfficeLevelForSubCategory($subCategory);
            $this->selectedTransferCategoryBoardLevels[$transferSubCategoryId] = $officeLevelId;

            if (!TransferSubCategoryRules::isAllowedPolicyBoardOfficeLevelForCode($subCategory->code, $officeLevelId)) {
                $invalidSelections[] = $transferSubCategoryId;
            }
        }

        if (!empty($invalidSelections)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'selectedTransferSubCategoryIds' => __('One or more selected categories are no longer valid for this policy.'),
            ]);
        }

        if (!$this->policyId) {
            return;
        }

        $existingCategories = TeacherTransferCategory::query()
            ->where('policy_id', $this->policyId)
            ->whereIn('transfer_sub_category_id', $this->selectedTransferSubCategoryIds)
            ->with('transferSubCategory')
            ->get();

        foreach ($existingCategories as $category) {
            $subCategory = $category->transferSubCategory;
            $selectedOfficeLevelId = $this->selectedTransferCategoryBoardLevels[$category->transfer_sub_category_id] ?? null;

            if (!$subCategory || $category->office_level_id === $selectedOfficeLevelId) {
                continue;
            }

            $blocks = $this->transferCategoryDeleteBlocks([$category->transfer_category_id]);

            if (!empty($blocks)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "selectedTransferCategoryBoardLevels.{$category->transfer_sub_category_id}" => $this->formatTeacherTransferCategoryBoardLevelChangeBlockMessage($blocks),
                ]);
            }
        }
    }

    protected function syncTransferCategories(TeacherTransferPolicyModel $policy): void
    {
        $existingManagedCategories = $policy->categoriesQuery()
            ->whereNotNull('transfer_sub_category_id')
            ->get()
            ->keyBy('transfer_sub_category_id');

        $selectedTransferSubCategoryIds = collect($this->selectedTransferSubCategoryIds)
            ->filter()
            ->values()
            ->all();

        $removedCategoryIds = $existingManagedCategories
            ->reject(fn(TeacherTransferCategory $category) => in_array($category->transfer_sub_category_id, $selectedTransferSubCategoryIds, true))
            ->pluck('transfer_category_id')
            ->values()
            ->all();

        $deleteBlocks = $this->transferCategoryDeleteBlocks($removedCategoryIds);

        if (!empty($deleteBlocks)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'selectedTransferSubCategoryIds' => $this->formatTeacherTransferCategoryDeleteBlockMessage($deleteBlocks),
            ]);
        }

        foreach ($selectedTransferSubCategoryIds as $transferSubCategoryId) {
            /** @var TeacherTransferSubCategory|null $subCategory */
            $subCategory = collect($this->transferSubCategoryOptions)
                ->firstWhere('transfer_sub_category_id', $transferSubCategoryId);

            if (!$subCategory) {
                continue;
            }

            $officeLevelId = $this->selectedBoardOfficeLevelForSubCategory($subCategory);

            $payload = [
                'policy_id' => $policy->policy_id,
                'office_level_id' => $officeLevelId,
                'transfer_sub_category_id' => $subCategory->transfer_sub_category_id,
                'transfer_owner_workplace_id' => $policy->transfer_authority,
                'transfer_category_name' => $subCategory->name,
                'description' => $subCategory->description,
                'active_status' => true,
            ];

            $existingCategory = $existingManagedCategories->get($transferSubCategoryId);

            if ($existingCategory) {
                if ($existingCategory->office_level_id !== $officeLevelId) {
                    $blocks = $this->transferCategoryDeleteBlocks([$existingCategory->transfer_category_id]);

                    if (!empty($blocks)) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            "selectedTransferCategoryBoardLevels.{$transferSubCategoryId}" => $this->formatTeacherTransferCategoryBoardLevelChangeBlockMessage($blocks),
                        ]);
                    }
                }

                $existingCategory->update($payload);
                continue;
            }

            TeacherTransferCategory::create($payload);
        }

        if (!empty($removedCategoryIds)) {
            TeacherTransferCategory::whereIn('transfer_category_id', $removedCategoryIds)->delete();
        }
    }

    public function getSelectedTransferSubCategoriesProperty()
    {
        return collect($this->transferSubCategoryOptions)
            ->whereIn('transfer_sub_category_id', $this->selectedTransferSubCategoryIds)
            ->sortBy('display_order')
            ->values();
    }

    protected function defaultBoardOfficeLevelForSubCategory(?TeacherTransferSubCategory $subCategory): string
    {
        return TransferSubCategoryRules::defaultPolicyBoardOfficeLevelForCode($subCategory?->code)
            ?? (string) ($subCategory?->policy_office_level_id ?? TransferSubCategoryRules::OFFICE_LEVEL_PEO);
    }

    protected function normalizeBoardOfficeLevelForSubCategory(
        ?TeacherTransferSubCategory $subCategory,
        ?string $officeLevelId
    ): string {
        $defaultOfficeLevelId = $this->defaultBoardOfficeLevelForSubCategory($subCategory);

        if (!$subCategory) {
            return $defaultOfficeLevelId;
        }

        return TransferSubCategoryRules::isAllowedPolicyBoardOfficeLevelForCode($subCategory->code, $officeLevelId)
            ? (string) $officeLevelId
            : $defaultOfficeLevelId;
    }

    public function selectedBoardOfficeLevelForSubCategory(TeacherTransferSubCategory $subCategory): string
    {
        return $this->normalizeBoardOfficeLevelForSubCategory(
            $subCategory,
            $this->selectedTransferCategoryBoardLevels[$subCategory->transfer_sub_category_id] ?? null
        );
    }

    public function policyBoardLevelOptionsForSubCategory(TeacherTransferSubCategory $subCategory): array
    {
        return collect(TransferSubCategoryRules::allowedPolicyBoardOfficeLevelIdsForCode($subCategory->code))
            ->map(fn (string $officeLevelId) => [
                'id' => $officeLevelId,
                'name' => $this->boardOfficeLevelName($officeLevelId),
            ])
            ->all();
    }

    public function boardOfficeLevelName(?string $officeLevelId): string
    {
        return match ($officeLevelId) {
            TransferSubCategoryRules::OFFICE_LEVEL_ZEO => __('Zonal Education Office'),
            TransferSubCategoryRules::OFFICE_LEVEL_PEO => __('Provincial Education Office'),
            TransferSubCategoryRules::OFFICE_LEVEL_PMOE => __('Provincial Ministry of Education'),
            default => __('Not configured'),
        };
    }

    public function boardOfficeLevelHint(?string $officeLevelId): string
    {
        return match ($officeLevelId) {
            TransferSubCategoryRules::OFFICE_LEVEL_ZEO => __('Zonal board'),
            TransferSubCategoryRules::OFFICE_LEVEL_PEO => __('Provincial board'),
            TransferSubCategoryRules::OFFICE_LEVEL_PMOE => __('Ministry board'),
            default => __('Board level'),
        };
    }

    public function isFixedBoardLevelSubCategory(TeacherTransferSubCategory $subCategory): bool
    {
        return $subCategory->code === TransferSubCategoryRules::CODE_INTER_ZONE;
    }

    public function descriptionForSubCategory(TeacherTransferSubCategory $subCategory): string
    {
        return match ($subCategory->code) {
            TransferSubCategoryRules::CODE_NATIONAL_SCHOOL => __('Transfers to National Schools under the selected provincial board level.'),
            default => $subCategory->description ?? __('System configured transfer category.'),
        };
    }

    public function officeLevelLabelForSubCategory(TeacherTransferSubCategory $subCategory): string
    {
        return $this->boardOfficeLevelName($this->selectedBoardOfficeLevelForSubCategory($subCategory));
    }

    public function boardWorkflowLabelForSubCategory(TeacherTransferSubCategory $subCategory): string
    {
        $stage = TransferSubCategoryRules::stageForOfficeLevel(
            $this->selectedBoardOfficeLevelForSubCategory($subCategory)
        );

        return TransferSubCategoryRules::displayLabelForStage($stage);
    }

    public function zoneScopeSummaryForSubCategory(TeacherTransferSubCategory $subCategory): string
    {
        return match ($subCategory->zone_scope_mode) {
            TransferSubCategoryRules::ZONE_SCOPE_CURRENT_ZONE_ONLY => __('Only the teacher\'s current zone'),
            TransferSubCategoryRules::ZONE_SCOPE_SOURCE_PROVINCE_ONLY => __('Any zone in the teacher\'s current province'),
            TransferSubCategoryRules::ZONE_SCOPE_SELECTED_TARGET_PROVINCE => __('Choose a target province first, then pick a zone'),
            default => __('System configured'),
        };
    }

    protected function loadScoreRuleOptions(): void
    {
        $this->scoreCriteriaOptions = TeacherTransferScoreCriterion::active()
            ->orderBy('display_order')
            ->get()
            ->map(fn($criterion) => [
                'key' => $criterion->criteria_key,
                'name' => $criterion->name,
                'description' => $criterion->description,
            ])
            ->all();

        $this->facilityScoreOptions = InstitutionalFacility::active()
            ->orderBy('facilities_id')
            ->get()
            ->map(fn($facility) => [
                'id' => $facility->facilities_id,
                'name' => $facility->name,
            ])
            ->all();
    }

    protected function defaultScoreRules(): array
    {
        $facilityScores = collect($this->facilityScoreOptions)
            ->mapWithKeys(fn($facility) => [$facility['id'] => '0'])
            ->all();

        return [
            'distance_current_workplace' => [
                'enabled' => false,
                'score_per_unit' => '',
            ],
            'current_difficulty_years' => [
                'enabled' => false,
                'facility_scores' => $facilityScores,
            ],
            'previous_difficulty_years' => [
                'enabled' => false,
                'facility_scores' => $facilityScores,
            ],
            'age' => [
                'enabled' => false,
                'base_value' => '55',
            ],
            'current_station_years' => [
                'enabled' => false,
                'base_value' => '5',
            ],
            'achievements' => [
                'enabled' => false,
                'level_scores' => [
                    'zonal' => '0',
                    'district' => '0',
                    'provincial' => '0',
                    'national' => '0',
                ],
            ],
        ];
    }

    protected function loadPolicyScoreRules(TeacherTransferPolicyModel $policy): void
    {
        $this->scoreRules = $this->defaultScoreRules();

        foreach ($policy->scoreRules as $rule) {
            if (!array_key_exists($rule->criteria_key, $this->scoreRules)) {
                continue;
            }

            $this->scoreRules[$rule->criteria_key]['enabled'] = (bool) $rule->active_status;

            if ($rule->score_per_unit !== null) {
                $this->scoreRules[$rule->criteria_key]['score_per_unit'] = (string) $rule->score_per_unit;
            }

            if ($rule->base_value !== null) {
                $this->scoreRules[$rule->criteria_key]['base_value'] = (string) $rule->base_value;
            }
        }

        foreach ($policy->facilityScoreRules as $rule) {
            if (!isset($this->scoreRules[$rule->criteria_key]['facility_scores'])) {
                continue;
            }

            $this->scoreRules[$rule->criteria_key]['facility_scores'][$rule->facilities_id] = (string) $rule->score_per_year;
        }

        foreach ($policy->achievementLevelScores as $rule) {
            $this->scoreRules['achievements']['level_scores'][$rule->achievement_level] = (string) $rule->score_per_achievement;
        }
    }

    protected function syncPolicyScoreRules(TeacherTransferPolicyModel $policy): void
    {
        TeacherTransferPolicyScoreRule::where('policy_id', $policy->policy_id)->delete();
        TeacherTransferPolicyFacilityScoreRule::where('policy_id', $policy->policy_id)->delete();
        TeacherTransferPolicyAchievementLevelScore::where('policy_id', $policy->policy_id)->delete();

        foreach ($this->scoreRules as $criteriaKey => $rule) {
            if (!($rule['enabled'] ?? false)) {
                continue;
            }

            TeacherTransferPolicyScoreRule::create([
                'policy_id' => $policy->policy_id,
                'criteria_key' => $criteriaKey,
                'score_per_unit' => $criteriaKey === 'distance_current_workplace'
                    ? $this->nullableDecimal($rule['score_per_unit'] ?? null)
                    : null,
                'base_value' => in_array($criteriaKey, ['age', 'current_station_years'], true)
                    ? $this->nullableDecimal($rule['base_value'] ?? null)
                    : null,
                'active_status' => true,
            ]);

            if (in_array($criteriaKey, ['current_difficulty_years', 'previous_difficulty_years'], true)) {
                foreach (($rule['facility_scores'] ?? []) as $facilityId => $score) {
                    TeacherTransferPolicyFacilityScoreRule::create([
                        'policy_id' => $policy->policy_id,
                        'criteria_key' => $criteriaKey,
                        'facilities_id' => $facilityId,
                        'score_per_year' => $this->nullableDecimal($score) ?? 0,
                    ]);
                }
            }

            if ($criteriaKey === 'achievements') {
                foreach (($rule['level_scores'] ?? []) as $level => $score) {
                    TeacherTransferPolicyAchievementLevelScore::create([
                        'policy_id' => $policy->policy_id,
                        'achievement_level' => $level,
                        'score_per_achievement' => $this->nullableDecimal($score) ?? 0,
                    ]);
                }
            }
        }
    }

    protected function nullableDecimal($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return round((float) $value, 2);
    }

    public function updatedSelectedProvince($value)
    {
        $this->selectedNationalSchool = '';
        $this->loadNationalSchools();
    }

    protected function loadNationalSchools()
    {
        if (!$this->selectedProvince) {
            $this->nationalSchools = [];
            return;
        }

        $this->nationalSchools = Institution::active()
            ->national()
            ->whereIn('district_id', DistrictsList::where('province_id', $this->selectedProvince)->pluck('district_id'))
            ->get();
    }

    public function render()
    {
        return view('livewire.transfer-module.teacher.teacher-transfer-policy');
    }
}
