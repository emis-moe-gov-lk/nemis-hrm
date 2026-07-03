<?php

namespace App\Livewire\MainTables;

use App\Models\OfficeLevel;
use App\Models\TeacherTransferApplicationRecommendation;
use App\Models\TeacherTransferRecommendationList;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class MainTablesTeacherTransferRecommendations extends Component
{
    use WithPagination;

    public string $search = '';
    public string $officeLevelFilter = '';

    public bool $showNewRecommendationModal = false;
    public bool $showEditRecommendationModal = false;

    public string $officeLevelId = '';
    public string $decision = '';
    public bool $rejectsApplication = false;
    public bool $activeStatus = true;

    public ?int $editRecommendationId = null;
    public string $updateRecommendationListId = '';
    public string $updateOfficeLevelId = '';
    public string $updateDecision = '';
    public bool $updateRejectsApplication = false;
    public bool $updateActiveStatus = true;

    protected function createRules(): array
    {
        return [
            'officeLevelId' => ['required', 'string', Rule::exists('office_levels', 'office_level_id')],
            'decision' => [
                'required',
                'string',
                'max:500',
                Rule::unique('teacher_transfer_recommendation_lists', 'decision')
                    ->where(fn ($query) => $query->where('office_level_id', $this->officeLevelId)),
            ],
            'rejectsApplication' => ['boolean'],
            'activeStatus' => ['boolean'],
        ];
    }

    protected function updateRules(): array
    {
        return [
            'updateOfficeLevelId' => ['required', 'string', Rule::exists('office_levels', 'office_level_id')],
            'updateDecision' => [
                'required',
                'string',
                'max:500',
                Rule::unique('teacher_transfer_recommendation_lists', 'decision')
                    ->where(fn ($query) => $query->where('office_level_id', $this->updateOfficeLevelId))
                    ->ignore($this->editRecommendationId),
            ],
            'updateRejectsApplication' => ['boolean'],
            'updateActiveStatus' => ['boolean'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'officeLevelId' => 'office level',
            'decision' => 'recommendation text',
            'rejectsApplication' => 'workflow effect',
            'activeStatus' => 'active status',
            'updateOfficeLevelId' => 'office level',
            'updateDecision' => 'recommendation text',
            'updateRejectsApplication' => 'workflow effect',
            'updateActiveStatus' => 'active status',
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedOfficeLevelFilter(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetCreateState();
        $this->resetValidation();
        $this->showNewRecommendationModal = true;
    }

    public function addRecommendation(): void
    {
        $validated = $this->validate($this->createRules());

        TeacherTransferRecommendationList::create([
            'office_level_id' => $validated['officeLevelId'],
            'decision' => trim($validated['decision']),
            'rejects_application' => $validated['rejectsApplication'],
            'active_status' => $validated['activeStatus'],
        ]);

        $this->showNewRecommendationModal = false;
        $this->resetCreateState();
        $this->resetPage();

        session()->flash('message', 'Teacher transfer recommendation added successfully.');
    }

    public function editRecommendation(int $id): void
    {
        $recommendation = TeacherTransferRecommendationList::query()->findOrFail($id);

        $this->editRecommendationId = $recommendation->id;
        $this->updateRecommendationListId = (string) $recommendation->transfer_recommendation_list_id;
        $this->updateOfficeLevelId = (string) $recommendation->office_level_id;
        $this->updateDecision = (string) $recommendation->decision;
        $this->updateRejectsApplication = $recommendation->rejectsApplication();
        $this->updateActiveStatus = (bool) $recommendation->active_status;

        $this->resetValidation();
        $this->showEditRecommendationModal = true;
    }

    public function updateRecommendation(): void
    {
        $validated = $this->validate($this->updateRules());

        TeacherTransferRecommendationList::query()
            ->whereKey($this->editRecommendationId)
            ->update([
                'office_level_id' => $validated['updateOfficeLevelId'],
                'decision' => trim($validated['updateDecision']),
                'rejects_application' => $validated['updateRejectsApplication'],
                'active_status' => $validated['updateActiveStatus'],
            ]);

        $this->showEditRecommendationModal = false;
        $this->resetEditState();

        session()->flash('message', 'Teacher transfer recommendation updated successfully.');
    }

    public function toggleStatus(int $id): void
    {
        $recommendation = TeacherTransferRecommendationList::query()->findOrFail($id);
        $recommendation->active_status = ! $recommendation->active_status;
        $recommendation->save();

        session()->flash(
            'message',
            $recommendation->active_status
                ? 'Teacher transfer recommendation enabled successfully.'
                : 'Teacher transfer recommendation disabled successfully.'
        );
    }

    public function deleteRecommendation(int $id): void
    {
        $recommendation = TeacherTransferRecommendationList::query()->findOrFail($id);

        $usageCount = TeacherTransferApplicationRecommendation::query()
            ->where('transfer_recommendation_list_id', $recommendation->transfer_recommendation_list_id)
            ->count();

        if ($usageCount > 0) {
            session()->flash(
                'error',
                "This recommendation is already used by {$usageCount} transfer application recommendation(s). Disable it instead of deleting it."
            );

            return;
        }

        $recommendation->delete();
        $this->resetPage();

        session()->flash('message', 'Teacher transfer recommendation deleted successfully.');
    }

    public function render()
    {
        $recommendations = TeacherTransferRecommendationList::query()
            ->with('officeLevel')
            ->withCount('applicationRecommendations')
            ->when($this->officeLevelFilter !== '', function ($query) {
                $query->where('office_level_id', $this->officeLevelFilter);
            })
            ->when(trim($this->search) !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('transfer_recommendation_list_id', 'like', "%{$search}%")
                        ->orWhere('decision', 'like', "%{$search}%")
                        ->orWhereHas('officeLevel', function ($officeQuery) use ($search) {
                            $officeQuery
                                ->where('office_level_name', 'like', "%{$search}%")
                                ->orWhere('short_name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('office_level_id')
            ->orderBy('id')
            ->paginate(20);

        return view('livewire.main-tables.main-tables-teacher-transfer-recommendations', [
            'recommendations' => $recommendations,
            'officeLevels' => OfficeLevel::query()
                ->active()
                ->orderBy('office_level_rank')
                ->orderBy('office_level_name')
                ->get(),
        ]);
    }

    protected function resetCreateState(): void
    {
        $this->reset([
            'officeLevelId',
            'decision',
            'rejectsApplication',
            'activeStatus',
        ]);

        $this->rejectsApplication = false;
        $this->activeStatus = true;
    }

    protected function resetEditState(): void
    {
        $this->reset([
            'editRecommendationId',
            'updateRecommendationListId',
            'updateOfficeLevelId',
            'updateDecision',
            'updateRejectsApplication',
            'updateActiveStatus',
        ]);

        $this->updateRejectsApplication = false;
        $this->updateActiveStatus = true;
    }
}
