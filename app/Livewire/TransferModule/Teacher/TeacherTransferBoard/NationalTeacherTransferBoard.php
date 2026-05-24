<?php

namespace App\Livewire\TransferModule\Teacher\TeacherTransferBoard;

use App\Models\TeacherTransferApplication;
use App\Models\TeacherTransferCategory;
use App\Models\Workplaces;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class NationalTeacherTransferBoard extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $category = '';
    public $activeTab = 'incoming';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'category' => ['except' => ''],
        'activeTab' => ['except' => 'incoming'],
    ];

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['search', 'status', 'category', 'activeTab'])) {
            $this->resetPage();
        }
    }

    protected function currentNationalWorkplace(): ?Workplaces
    {
        $userWorkplaceId = auth()->user()?->workplace_id;

        if (!$userWorkplaceId) {
            return null;
        }

        $workplace = Workplaces::find($userWorkplaceId);

        if (!$workplace || $workplace->office_level_id !== 'OLID001') {
            return null;
        }

        return $workplace;
    }

    protected function emptyApplicationsQuery(): Builder
    {
        return TeacherTransferApplication::query()->whereIn('id', []);
    }

    protected function incomingApplicationsQuery(): Builder
    {
        if (!$this->currentNationalWorkplace()) {
            return $this->emptyApplicationsQuery();
        }

        return TeacherTransferApplication::query()
            ->whereHas('preferences', function (Builder $query) {
                $query->whereHas('institution.institution', function (Builder $institutionQuery) {
                    $institutionQuery->national();
                });
            });
    }

    protected function outgoingApplicationsQuery(): Builder
    {
        if (!$this->currentNationalWorkplace()) {
            return $this->emptyApplicationsQuery();
        }

        return TeacherTransferApplication::query()
            ->whereHas('currentWorkplace.institution', function (Builder $query) {
                $query->national();
            });
    }

    protected function applyCommonFilters(Builder $query): Builder
    {
        if ($this->search) {
            $query->where(function (Builder $searchQuery) {
                $searchQuery->where('transfer_application_id', 'like', '%' . $this->search . '%')
                    ->orWhereHas('employee', function (Builder $employeeQuery) {
                        $employeeQuery->where('full_name', 'like', '%' . $this->search . '%')
                            ->orWhere('nic', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->category) {
            $query->where('transfer_category', $this->category);
        }

        return $query;
    }

    public function getApplicationsProperty()
    {
        $query = $this->activeTab === 'outgoing'
            ? $this->outgoingApplicationsQuery()
            : $this->incomingApplicationsQuery();

        return $this->applyCommonFilters(
            $query->with(['employee', 'currentWorkplace', 'category', 'targetProvince', 'policy'])
        )
            ->latest()
            ->paginate(10);
    }

    public function getStatsProperty(): array
    {
        $incomingQuery = $this->incomingApplicationsQuery();
        $outgoingQuery = $this->outgoingApplicationsQuery();

        $incomingCount = (clone $incomingQuery)->count();
        $outgoingCount = (clone $outgoingQuery)->count();
        $incomingPending = (clone $incomingQuery)->whereIn('status', ['submitted', 'processing'])->count();
        $outgoingPending = (clone $outgoingQuery)->whereIn('status', ['submitted', 'processing'])->count();

        return [
            'total' => $incomingCount + $outgoingCount,
            'incoming' => $incomingCount,
            'outgoing' => $outgoingCount,
            'pending' => $incomingPending + $outgoingPending,
        ];
    }

    public function render()
    {
        return view('livewire.transfer-module.teacher.teacher-transfer-board.national-teacher-transfer-board', [
            'applications' => $this->applications,
            'stats' => $this->stats,
            'categories' => TeacherTransferCategory::active()->orderBy('transfer_category_name')->get(),
        ]);
    }
}
