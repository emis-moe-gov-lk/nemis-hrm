<?php

namespace App\Livewire\TransferModule\Teacher;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\SubjectList;
use App\Models\ZonalEducationOffice;
use App\Models\TeacherTransferPolicy;
use App\Models\TeacherTransferApplication;
use App\Models\TeacherTransferBoard;
use App\Support\Transfer\TransferSubCategoryRules;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TeacherTransferController extends Component
{
    use WithPagination;

    public int $id;
    public TeacherTransferPolicy $policy;
    public string $search = '';
    public string $filterZone = '';
    public string $filterSubject = '';
    public ?int $minServiceYears = null;
    #[Url]
    public string $activeTab = 'overview';

    public function mount(int $id)
    {
        $this->id = $id;
        $this->policy = TeacherTransferPolicy::with(['authority'])->findOrFail($id);
        $this->abortIfPolicyOutsideUserScope();
    }

    public function createTransferBoard()
    {
        $routeOfficeLevelId = $this->createBoardRouteOfficeLevelId();

        if (!$routeOfficeLevelId) {
            return;
        }

        $route = $this->boardWorkspaceRouteFor($routeOfficeLevelId);

        return redirect()->to(route($route, [
            'createPolicyId' => $this->policy->policy_id,
            'showCreate' => true
        ]));
    }

    public function createAppealBoard()
    {
        $routeOfficeLevelId = $this->createBoardRouteOfficeLevelId();

        if (!$routeOfficeLevelId) {
            return;
        }

        $route = $this->boardWorkspaceRouteFor($routeOfficeLevelId, true);

        return redirect()->to(route($route, [
            'createPolicyId' => $this->policy->policy_id,
            'showCreate' => true,
        ]));
    }

    public function render()
    {
        $applicationsQuery = TeacherTransferApplication::query()
            ->where('policy_id', $this->policy->policy_id);

        $this->applyApplicationScope($applicationsQuery);

        $applications = (clone $applicationsQuery)
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $searchQuery) {
                    $searchQuery->where('transfer_application_id', 'like', '%' . $this->search . '%')
                        ->orWhereHas('employee', function (Builder $employeeQuery) {
                            $employeeQuery->where('full_name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->filterZone, function (Builder $query) {
                $query->whereHas('currentWorkplace', function (Builder $q) {
                    $q->where('zeo_wp_id', $this->filterZone)
                        ->orWhere('workplace_id', $this->filterZone);
                });
            })
            ->when($this->filterSubject, function (Builder $query) {
                $query->whereHas('teacher', function (Builder $q) {
                    $q->where('main_subject', $this->filterSubject);
                });
            })
            ->when($this->minServiceYears, function (Builder $query) {
                $query->where('current_workplace_join_date', '<=', now()->subYears($this->minServiceYears));
            })
            ->with(['employee', 'currentWorkplace', 'teacher.mainSubject'])
            ->paginate(20);

        $stats = [
            'total' => (clone $applicationsQuery)->count(),
            'rejected' => (clone $applicationsQuery)->where('status', 'rejected')->count(),
            'completed' => (clone $applicationsQuery)->where('status', 'approved')->count(),
            'pending' => (clone $applicationsQuery)->whereIn('status', ['submitted', 'processing'])->count(),
        ];

        $boardsQuery = TeacherTransferBoard::query()
            ->where('policy_id', $this->policy->policy_id);

        $this->applyBoardScope($boardsQuery);

        $allBoards = $boardsQuery
            ->with(['officeLevel', 'category.transferSubCategory', 'transferSubCategory'])
            ->latest()
            ->get();

        $mapBoard = function ($board) {
            $isAppeal = $board->board_type === 'appeal';
            $route = $this->boardWorkspaceRouteFor($board->bo_office_level_id, $isAppeal);

            return [
                'label' => $board->board_name,
                'desc' => $board->officeLevel->office_level_name ?? ($isAppeal ? 'Appeal Board' : 'Transfer Board'),
                'icon' => $isAppeal ? 'chat-bubble-left-right' : 'building-office-2',
                'route' => route($route, ['selectedBoardId' => $board->board_id]),
                'report_route' => $board->isClosed()
                    ? route(
                        $isAppeal
                            ? 'transfer.transfer-board.appeal-report.download'
                            : 'transfer.transfer-board.decision-report.download',
                        ['boardId' => $board->board_id]
                    )
                    : null,
                'gradient' => $isAppeal ? 'from-rose-500 to-red-600' : 'from-indigo-500 to-blue-600',
                'shadow' => $isAppeal ? 'shadow-rose-200' : 'shadow-indigo-200',
                'text' => $isAppeal ? 'text-rose-600' : 'text-indigo-600',
                'status' => $board->board_status,
                'category' => $board->category?->transfer_category_name,
                'sub_category' => $board->transferSubCategory?->name ?? $board->category?->transferSubCategory?->name,
                'stage_label' => filled($board->board_stage)
                    ? TransferSubCategoryRules::displayLabelForBoardStage($board->board_stage, $board->bo_office_level_id)
                    : null,
                'is_closed' => $board->isClosed(),
            ];
        };

        $boards = $allBoards->filter(fn($b) => $b->board_type !== 'appeal')->map($mapBoard);
        $appeals = $allBoards->filter(fn($b) => $b->board_type === 'appeal')->map($mapBoard);

        $zonesQuery = ZonalEducationOffice::query()
            ->active()
            ->orderBy('name');

        $this->applyZoneScope($zonesQuery);

        $zones = $zonesQuery
            ->get(['workplace_id', 'name as office_name']);

        $subjects = SubjectList::orderBy('name_en')
            ->get(['subject_id', 'name_en as subject_name']);

        return view('livewire.transfer-module.teacher.teacher-transfer-controller', [
            'applications' => $applications,
            'stats' => $stats,
            'boards' => $boards,
            'appeals' => $appeals,
            'zones' => $zones,
            'subjects' => $subjects,
        ]);
    }

    protected function currentUser()
    {
        return Auth::user()->loadMissing('workplace');
    }

    protected function abortIfPolicyOutsideUserScope(): void
    {
        $user = $this->currentUser();

        if ($user->hasRole('super admin')) {
            return;
        }

        $workplace = $user->workplace;
        $authority = $this->policy->authority;

        if (!$workplace || !$authority) {
            abort(403);
        }

        if ($workplace->office_level_id === 'OLID001' || $authority->office_level_id === 'OLID001') {
            return;
        }

        $userScope = $workplace->getAllChildWorkplaces();
        $policyScope = $authority->getAllChildWorkplaces();

        if (empty(array_intersect($userScope, $policyScope))) {
            abort(403);
        }
    }

    protected function createBoardRouteOfficeLevelId(): ?string
    {
        $user = $this->currentUser();

        if ($user->hasRole('super admin')) {
            return $this->policy->authority?->office_level_id;
        }

        return $user->workplace?->office_level_id;
    }

    protected function boardWorkspaceRouteFor(?string $officeLevelId, bool $appeal = false): string
    {
        if ($appeal) {
            return match($officeLevelId) {
                'OLID002' => 'transfer-board.provincial-ministry-teacher-appeal',
                'OLID004' => 'transfer-board.zone-teacher-appeal',
                default => 'transfer-board.province-teacher-appeal',
            };
        }

        return match($officeLevelId) {
            'OLID001' => 'transfer-board.national-teacher-transfer',
            'OLID002' => 'transfer-board.provincial-ministry-teacher-transfer',
            'OLID004' => 'transfer-board.zone-teacher-transfer',
            default => 'transfer-board.province-teacher-transfer',
        };
    }

    protected function applyApplicationScope(Builder $query): void
    {
        $policyWorkplaceIds = $this->policyScopedWorkplaceIds();
        if ($policyWorkplaceIds !== null) {
            if ($policyWorkplaceIds === []) {
                $query->whereRaw('1 = 0');
                return;
            }

            $query->whereIn('current_workplace', $policyWorkplaceIds);
        }

        $userWorkplaceIds = $this->userScopedWorkplaceIds();
        if ($userWorkplaceIds !== null) {
            if ($userWorkplaceIds === []) {
                $query->whereRaw('1 = 0');
                return;
            }

            $query->whereIn('current_workplace', $userWorkplaceIds);
        }
    }

    protected function applyBoardScope(Builder $query): void
    {
        $policyWorkplaceIds = $this->policyScopedWorkplaceIds();
        if ($policyWorkplaceIds !== null) {
            if ($policyWorkplaceIds === []) {
                $query->whereRaw('1 = 0');
                return;
            }

            $query->whereIn('bo_workplace_id', $policyWorkplaceIds);
        }

        $userWorkplaceIds = $this->userScopedWorkplaceIds();
        if ($userWorkplaceIds !== null) {
            if ($userWorkplaceIds === []) {
                $query->whereRaw('1 = 0');
                return;
            }

            $query->whereIn('bo_workplace_id', $userWorkplaceIds);
        }
    }

    protected function applyZoneScope(Builder $query): void
    {
        $authority = $this->policy->authority;
        $userWorkplace = $this->currentUser()->workplace;

        if ($authority) {
            match ($authority->office_level_id) {
                'OLID002' => $query->whereIn('workplace_id', $authority->getAllChildWorkplaces()),
                'OLID003' => $query->where('peo_wp_id', $authority->workplace_id),
                'OLID004' => $query->where('workplace_id', $authority->workplace_id),
                default => null,
            };
        }

        if ($this->currentUser()->hasRole('super admin')) {
            return;
        }

        if (!$userWorkplace) {
            $query->whereRaw('1 = 0');
            return;
        }

        match ($userWorkplace->office_level_id) {
            'OLID002' => $query->whereIn('workplace_id', $userWorkplace->getAllChildWorkplaces()),
            'OLID003' => $query->where('peo_wp_id', $userWorkplace->workplace_id),
            'OLID004' => $query->where('workplace_id', $userWorkplace->workplace_id),
            'OLID001' => null,
            default => $query->whereRaw('1 = 0'),
        };
    }

    protected function policyScopedWorkplaceIds(): ?array
    {
        $authority = $this->policy->authority;

        if (!$authority || $authority->office_level_id === 'OLID001') {
            return null;
        }

        return $authority->getAllChildWorkplaces();
    }

    protected function userScopedWorkplaceIds(): ?array
    {
        $user = $this->currentUser();

        if ($user->hasRole('super admin')) {
            return null;
        }

        $workplace = $user->workplace;

        if (!$workplace) {
            return [];
        }

        return match ($workplace->office_level_id) {
            'OLID001' => null,
            'OLID002', 'OLID003', 'OLID004' => $workplace->getAllChildWorkplaces(),
            default => [],
        };
    }
}
