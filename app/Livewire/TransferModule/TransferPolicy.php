<?php

namespace App\Livewire\TransferModule;

use Livewire\Component;
use App\Models\OfficeLevel;
use App\Models\Authority;
use App\Models\Service;
use App\Models\TransferPolicy as TransferPolicyModel;
use App\Models\TransferPolicyStep;
use App\Models\TransferCategory;
use App\Models\TeacherTransferApplication;
use App\Models\TransferBoard;
use App\Models\InstitutionalFacility;
use App\Models\TransferPolicyAchievementLevelScore;
use App\Models\TransferPolicyFacilityScoreRule;
use App\Models\TransferPolicyScoreRule;
use App\Models\TransferScoreCriterion;
use App\Models\ProvincesList;
use App\Models\Institution;
use App\Models\DistrictsList;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TransferPolicy extends Component
{
    public $policyYear;
    public $circularNumber;
    public $title;
    public $description;
    public $effectiveDate;
    public $minServiceCurrentSchool;
    public $minServiceTotal;
    public $maxPreferences;
    public $startDate;
    public $endDate;
    public $policyId; // Added for editing
    public $status = 1;
    public $approvalSteps = [];
    public $stepDates = [];
    public $transferCategories = [];
    public $loadedTransferCategoryIds = [];
    public array $scoreRules = [];
    public array $scoreCriteriaOptions = [];
    public array $facilityScoreOptions = [];
    public array $achievementLevels = [
        'zonal' => 'Zonal',
        'district' => 'District',
        'provincial' => 'Provincial',
        'national' => 'National',
    ];

    // Category Modal State
    public $categoryModalOfficeLevel = '';
    public $categoryModalName = '';
    public $categoryModalDescription = '';
    public $editingCategoryIndex = null;
    public $showCategoryModal = false;

    public $hierarchyOptions = [];
    public $transferAuthority;
    public $transferType;
    public $service;
    public $authorityOptions = [];
    public $serviceOptions = [];
    public $transferTypeOptions = [
        ['id' => 'annual', 'name' => 'Annual Transfer'],
        ['id' => 'mutual', 'name' => 'Mutual Transfer'],
        ['id' => 'medical', 'name' => 'Medical Grounds'],
        ['id' => 'humanitarian', 'name' => 'Humanitarian Grounds'],
        ['id' => 'special', 'name' => 'Special Request'],
    ];

    public $myOfficeLevel;
    public $myOfficeId;

    public $provinces = [];
    public $nationalSchools = [];
    public $selectedProvince = '';
    public $selectedNationalSchool = '';
    public $isNsCategoryConsidered = false;

    public function mount($id = null)
    {
        $this->hierarchyOptions = OfficeLevel::active()
            ->orderByDesc('office_level_rank')
            ->get();

        $this->authorityOptions = Authority::active()->get();
        $this->serviceOptions = Service::active()->get();
        $this->provinces = ProvincesList::active()->get();
        $this->loadScoreRuleOptions();
        $this->scoreRules = $this->defaultScoreRules();

        $this->myOfficeLevel = auth()->user()->workplace_name;
        $this->myOfficeId = auth()->user()->workplace_id;
        $this->transferAuthority = auth()->user()->workplace_id;

        if ($id) {
            $this->policyId = $id;

            $policy = TransferPolicyModel::where('policy_id', $this->policyId)->firstOrFail();
            if ($policy->is_locked) {
                session()->flash('error', __('This policy is locked and cannot be edited.'));
                return redirect()->route('transfer.transfer-policies');
            }

            $this->loadPolicyData();
        }
    }

    protected function loadPolicyData()
    {
        $policy = TransferPolicyModel::with([
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
        $this->service = $policy->service_id;
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

        $categories = $policy->categoriesQuery()->get();
        $this->loadedTransferCategoryIds = $categories
            ->pluck('transfer_category_id')
            ->filter()
            ->values()
            ->all();

        $this->transferCategories = [];
        foreach ($categories as $category) {
            $this->transferCategories[] = [
                'id' => $category->transfer_category_id,
                'office_level' => $category->office_level_id,
                'name' => $category->transfer_category_name,
                'description' => $category->description,
            ];
        }
        if (empty($this->transferCategories)) {
            $this->addTransferCategory();
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
                Rule::unique('transfer_policies', 'circular_number')->ignore($this->policyId, 'policy_id'),
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
            'service' => 'required',
        ];

        foreach ($this->approvalSteps as $stepId) {
            $rules["stepDates.{$stepId}.start"] = 'required|date';
            $rules["stepDates.{$stepId}.end"] = 'required|date|after_or_equal:stepDates.' . $stepId . '.start';
        }

        $rules['transferCategories'] = 'required|array|min:1';
        $rules['transferCategories.*.office_level'] = 'required';
        $rules['transferCategories.*.name'] = 'required|string|max:255';
        $rules['transferCategories.*.description'] = 'nullable|string';

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

    public function addTransferCategory()
    {
        $this->resetCategoryModal();
        $this->editingCategoryIndex = null;
        $this->showCategoryModal = true;
    }

    public function editTransferCategory($index)
    {
        $category = $this->transferCategories[$index];
        $this->categoryModalOfficeLevel = $category['office_level'];
        $this->categoryModalName = $category['name'];
        $this->categoryModalDescription = $category['description'];
        $this->editingCategoryIndex = $index;

        $this->showCategoryModal = true;
    }

    public function saveCategoryModal()
    {
        $this->validate([
            'categoryModalOfficeLevel' => 'required',
            'categoryModalName' => 'required|string|max:255',
            'categoryModalDescription' => 'nullable|string',
        ], [], [
            'categoryModalOfficeLevel' => __('Office Level'),
            'categoryModalName' => __('Category Name'),
        ]);

        $categoryData = [
            'id' => $this->editingCategoryIndex !== null ? $this->transferCategories[$this->editingCategoryIndex]['id'] : '',
            'office_level' => $this->categoryModalOfficeLevel,
            'name' => $this->categoryModalName,
            'description' => $this->categoryModalDescription,
        ];

        if ($this->editingCategoryIndex !== null) {
            $this->transferCategories[$this->editingCategoryIndex] = $categoryData;
        } else {
            $this->transferCategories[] = $categoryData;
        }

        $this->resetCategoryModal();
        $this->showCategoryModal = false;
    }

    public function resetCategoryModal()
    {
        $this->categoryModalOfficeLevel = '';
        $this->categoryModalName = '';
        $this->categoryModalDescription = '';
        $this->editingCategoryIndex = null;
        $this->showCategoryModal = false;
    }

    public function removeTransferCategory($index)
    {
        $categoryId = $this->transferCategories[$index]['id'] ?? null;

        if ($categoryId) {
            $deleteBlocks = $this->transferCategoryDeleteBlocks([$categoryId]);

            if (!empty($deleteBlocks)) {
                session()->flash('error', $this->formatTransferCategoryDeleteBlockMessage($deleteBlocks));

                return;
            }
        }

        unset($this->transferCategories[$index]);
        $this->transferCategories = array_values($this->transferCategories);
    }

    public function toggleApprovalStep($stepId)
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


    public function save()
    {
        $this->validate();

        $existingCatIds = $this->retainedTransferCategoryIds();
        $removedCategoryIds = $this->removedTransferCategoryIds($existingCatIds);
        $deleteBlocks = $this->transferCategoryDeleteBlocks($removedCategoryIds);

        if (!empty($deleteBlocks)) {
            session()->flash('error', $this->formatTransferCategoryDeleteBlockMessage($deleteBlocks));

            return;
        }

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
                'service_id' => $this->service,
                'province_id' => $this->selectedProvince,
                'special_institution_id' => $this->selectedNationalSchool,
                'is_ns_category_considered' => (bool) $this->isNsCategoryConsidered,
                'active_status' => (bool) $this->status,
            ];

            if ($this->policyId) {
                $policy = TransferPolicyModel::where('policy_id', $this->policyId)->firstOrFail();
                $policy->update($data);
                // Clear existing steps and recreate
                TransferPolicyStep::where('policy_id', $this->policyId)->delete();
            } else {
                $policy = TransferPolicyModel::create($data);
            }

            foreach ($this->approvalSteps as $index => $stepId) {
                TransferPolicyStep::create([
                    'policy_id'      => $policy->policy_id,
                    'office_level_id' => $stepId,
                    'step_order'     => $index + 1,
                    'start_date'     => $this->stepDates[$stepId]['start'] ?? null,
                    'end_date'       => $this->stepDates[$stepId]['end'] ?? null,
                ]);
            }

            foreach ($this->transferCategories as $cat) {
                if (!empty($cat['id'])) {
                    $categoryUpdateData = [
                        'policy_id' => $policy->policy_id,
                        'office_level_id' => $cat['office_level'],
                        'transfer_owner_workplace_id' => $policy->transfer_authority,
                        'transfer_category_name' => $cat['name'],
                        'description' => $cat['description'],
                    ];

                    TransferCategory::where('transfer_category_id', $cat['id'])->update($categoryUpdateData);
                } else {
                    $newCategoryData = [
                        'policy_id' => $policy->policy_id,
                        'office_level_id' => $cat['office_level'],
                        'transfer_owner_workplace_id' => $policy->transfer_authority,
                        'transfer_category_name' => $cat['name'],
                        'description' => $cat['description'],
                        'active_status' => true,
                    ];

                    $newCat = TransferCategory::create($newCategoryData);
                    $existingCatIds[] = $newCat->transfer_category_id;
                }
            }

            if (!empty($removedCategoryIds)) {
                TransferCategory::whereIn('transfer_category_id', $removedCategoryIds)->delete();
            }

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

    protected function retainedTransferCategoryIds(): array
    {
        return collect($this->transferCategories)
            ->pluck('id')
            ->filter()
            ->values()
            ->all();
    }

    protected function removedTransferCategoryIds(array $retainedCategoryIds): array
    {
        return array_values(array_diff($this->loadedTransferCategoryIds, $retainedCategoryIds));
    }

    protected function transferCategoryDeleteBlocks(array $categoryIds): array
    {
        $categoryIds = array_values(array_unique(array_filter($categoryIds)));

        if (empty($categoryIds)) {
            return [];
        }

        $categories = TransferCategory::query()
            ->whereIn('transfer_category_id', $categoryIds)
            ->get()
            ->keyBy('transfer_category_id');

        $applicationCounts = TeacherTransferApplication::query()
            ->whereIn('transfer_category', $categoryIds)
            ->pluck('transfer_category')
            ->countBy();

        $boardCounts = TransferBoard::query()
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

    protected function formatTransferCategoryDeleteBlockMessage(array $deleteBlocks): string
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

    protected function loadScoreRuleOptions(): void
    {
        $this->scoreCriteriaOptions = TransferScoreCriterion::active()
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

    protected function loadPolicyScoreRules(TransferPolicyModel $policy): void
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

    protected function syncPolicyScoreRules(TransferPolicyModel $policy): void
    {
        TransferPolicyScoreRule::where('policy_id', $policy->policy_id)->delete();
        TransferPolicyFacilityScoreRule::where('policy_id', $policy->policy_id)->delete();
        TransferPolicyAchievementLevelScore::where('policy_id', $policy->policy_id)->delete();

        foreach ($this->scoreRules as $criteriaKey => $rule) {
            if (!($rule['enabled'] ?? false)) {
                continue;
            }

            TransferPolicyScoreRule::create([
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
                    TransferPolicyFacilityScoreRule::create([
                        'policy_id' => $policy->policy_id,
                        'criteria_key' => $criteriaKey,
                        'facilities_id' => $facilityId,
                        'score_per_year' => $this->nullableDecimal($score) ?? 0,
                    ]);
                }
            }

            if ($criteriaKey === 'achievements') {
                foreach (($rule['level_scores'] ?? []) as $level => $score) {
                    TransferPolicyAchievementLevelScore::create([
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
        return view('livewire.transfer-module.transfer-policy');
    }
}
