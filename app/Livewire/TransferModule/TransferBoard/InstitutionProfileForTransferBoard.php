<?php

namespace App\Livewire\TransferModule\TransferBoard;

use App\Models\CadreCirculars;
use App\Models\CadreDMSApproved;
use App\Models\EmployerAttachmentAppointment;
use App\Models\EmployerCurrentAppointment;
use App\Models\Institution;
use App\Models\Teacher;
use App\Models\TeacherTransferApplication;
use App\Models\TransferBoard;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;

class InstitutionProfileForTransferBoard extends Component
{
    public $application;
    public $institution;
    public $selectedPreference;
    public $activeCircular;
    public $staffRows;
    public $cadreRows;
    public $inboundApplications;
    public $outboundApplications;
    public array $cadreStats = [];
    public string $board = 'province';
    public string $selectedBoardId = '';

    public function mount($applicationId, $institutionId)
    {
        $this->board = $this->normalizeBoard(request()->query('board'));
        $this->selectedBoardId = (string) request()->query('selectedBoardId', '');
        $this->application = $this->loadApplication($applicationId);
        $this->institution = Institution::with([
            'authority',
            'institutionCategory',
            'institutionType',
            'institutionLanguages',
            'typeByGender',
            'facilities',
            'gradeSpan',
            'district',
            'zonalEducationOffice.provincialEducationOffice',
            'divisionalEducationOffice',
        ])->findOrFail($institutionId);

        $this->selectedPreference = $this->application->preferences
            ->firstWhere('ins_wp_id', $this->institution->workplace_id);

        abort_if(!$this->selectedPreference && !$this->canOpenNeedBasedInstitution(), 404);

        $this->activeCircular = CadreCirculars::active()
            ->orderByDesc('issued_date')
            ->first();

        $this->cadreRows = $this->activeCircular
            ? CadreDMSApproved::institutionCadreVsEmployersWithList(
                $this->activeCircular->circular_id,
                $this->institution->workplace_id
            )
            : collect();

        $this->cadreStats = [
            'approved' => $this->cadreRows->sum('approved_posts'),
            'filled' => $this->cadreRows->sum('filled_posts'),
            'shortage' => $this->cadreRows->sum(fn(array $row) => $row['diff'] < 0 ? abs($row['diff']) : 0),
            'excess' => $this->cadreRows->sum(fn(array $row) => $row['diff'] > 0 ? $row['diff'] : 0),
        ];

        $this->inboundApplications = $this->loadInboundApplications();
        $this->outboundApplications = $this->loadOutboundApplications();
        $this->staffRows = $this->buildStaffRows();
    }

    protected function loadApplication($applicationId)
    {
        return TeacherTransferApplication::with([
            'employee.title',
            'teacher.teacherCategory',
            'teacher.medium',
            'teacher.appointmentSubject',
            'teacher.mainSubject',
            'teacher.currentTeachingSubject',
            'policy',
            'preferences.zonalOffice.zonal',
            'preferences.institution.institution',
            'currentWorkplace.institution.zonalEducationOffice',
            'currentWorkplace.institution.divisionalEducationOffice',
        ])
            ->where(function ($query) use ($applicationId) {
                $query->where('transfer_application_id', $applicationId)
                    ->orWhere('id', $applicationId);
            })
            ->firstOrFail();
    }

    protected function loadInboundApplications(): Collection
    {
        return TeacherTransferApplication::with([
            'policy',
            'employee.title',
            'teacher.medium',
            'teacher.appointmentSubject',
            'teacher.mainSubject',
            'teacher.currentTeachingSubject',
            'currentWorkplace.institution.zonalEducationOffice',
        ])
            ->whereIn('status', ['submitted', 'processing', 'approved'])
            ->whereHas('preferences', function ($query) {
                $query->where('ins_wp_id', $this->institution->workplace_id);
            })
            ->latest()
            ->get();
    }

    protected function loadOutboundApplications(): Collection
    {
        return TeacherTransferApplication::with([
            'policy',
            'employee.title',
            'teacher.medium',
            'teacher.appointmentSubject',
            'teacher.mainSubject',
            'teacher.currentTeachingSubject',
            'preferences.institution.institution',
        ])
            ->whereIn('status', ['submitted', 'processing', 'approved'])
            ->where('current_workplace', $this->institution->workplace_id)
            ->latest()
            ->get();
    }

    protected function buildStaffRows(): Collection
    {
        $permanentAppointments = EmployerCurrentAppointment::with([
            'employee.title',
            'employee.teacher.currentTeachingSubject',
            'employee.teacher.appointmentSubject',
            'employee.teacher.mainSubject',
            'employee.teacher.medium',
            'service',
            'rank',
            'position',
            'workplace',
        ])
            ->where('workplace_id', $this->institution->workplace_id)
            ->orderBy('appoint_date')
            ->get();

        $attachedAppointments = EmployerAttachmentAppointment::with([
            'employee.title',
            'employee.teacher.currentTeachingSubject',
            'employee.teacher.appointmentSubject',
            'employee.teacher.mainSubject',
            'employee.teacher.medium',
            'employee.currentAppointment.service',
            'employee.currentAppointment.rank',
            'employee.currentAppointment.workplace',
            'position',
            'workplace',
        ])
            ->active()
            ->where('workplace_id', $this->institution->workplace_id)
            ->orderBy('appoint_date')
            ->get();

        $employeeIds = $permanentAppointments->pluck('employee_id')
            ->merge($attachedAppointments->pluck('employee_id'))
            ->filter()
            ->unique()
            ->values();

        $activeApplicationsByEmployee = TeacherTransferApplication::with(['preferences'])
            ->whereIn('employee_id', $employeeIds)
            ->whereIn('status', ['submitted', 'processing', 'approved'])
            ->latest()
            ->get()
            ->groupBy('employee_id')
            ->map(fn(Collection $applications) => $applications->first());

        $permanentRows = $permanentAppointments->map(function (EmployerCurrentAppointment $appointment) use ($activeApplicationsByEmployee) {
            $teacher = $appointment->employee?->teacher;
            $application = $activeApplicationsByEmployee->get($appointment->employee_id);

            return [
                'employee_id' => $appointment->employee_id,
                'name' => trim(($appointment->employee?->title?->title_name ?? '') . ' ' . ($appointment->employee?->name_with_initials ?? $appointment->employee?->full_name ?? 'Unknown')),
                'nic' => $appointment->employee?->nic ?? 'N/A',
                'subject' => $this->resolveSubjectLabel($teacher),
                'medium' => $teacher?->medium?->name,
                'service_label' => $appointment->service?->service_name ?? 'N/A',
                'rank_label' => $appointment->rank?->rank_name,
                'position_label' => $appointment->position?->position_name ?? 'N/A',
                'assignment_type' => 'Permanent',
                'assignment_color' => 'green',
                'station_period' => $appointment->service_years ?? $this->formatDuration($appointment->appoint_date),
                'station_start_date' => $appointment->appoint_date,
                'station_sort' => $appointment->appoint_date?->timestamp ?? PHP_INT_MAX,
                'home_station' => $appointment->workplace?->office_name ?? $this->institution->name,
                'transfer_application' => $application,
            ];
        });

        $attachedRows = $attachedAppointments->map(function (EmployerAttachmentAppointment $appointment) use ($activeApplicationsByEmployee) {
            $teacher = $appointment->employee?->teacher;
            $application = $activeApplicationsByEmployee->get($appointment->employee_id);

            return [
                'employee_id' => $appointment->employee_id,
                'name' => trim(($appointment->employee?->title?->title_name ?? '') . ' ' . ($appointment->employee?->name_with_initials ?? $appointment->employee?->full_name ?? 'Unknown')),
                'nic' => $appointment->employee?->nic ?? 'N/A',
                'subject' => $this->resolveSubjectLabel($teacher),
                'medium' => $teacher?->medium?->name,
                'service_label' => $appointment->employee?->currentAppointment?->service?->service_name ?? 'N/A',
                'rank_label' => $appointment->employee?->currentAppointment?->rank?->rank_name,
                'position_label' => $appointment->position?->position_name ?? 'N/A',
                'assignment_type' => 'Attached',
                'assignment_color' => 'amber',
                'station_period' => $this->formatDuration($appointment->appoint_date),
                'station_start_date' => $appointment->appoint_date,
                'station_sort' => $appointment->appoint_date?->timestamp ?? PHP_INT_MAX,
                'home_station' => $appointment->employee?->currentAppointment?->workplace?->office_name ?? 'N/A',
                'transfer_application' => $application,
            ];
        });

        return $permanentRows
            ->concat($attachedRows)
            ->sortBy(function (array $row) {
                $typePriority = $row['assignment_type'] === 'Permanent' ? 0 : 1;

                return sprintf(
                    '%d-%010d-%s',
                    $typePriority,
                    $row['station_sort'],
                    strtolower($row['name'])
                );
            })
            ->values();
    }

    protected function canOpenNeedBasedInstitution(): bool
    {
        $board = $this->selectedBoardId !== ''
            ? TransferBoard::where('board_id', $this->selectedBoardId)->first(['bo_office_level_id', 'bo_workplace_id'])
            : null;

        if ($board && $board->bo_office_level_id === 'OLID004') {
            return (string) $this->institution->zeo_wp_id === (string) $board->bo_workplace_id;
        }

        $provinceWorkplaceId = $board?->bo_workplace_id;

        $provinceWorkplaceId = $provinceWorkplaceId ?: $this->application->target_province;

        if (!$provinceWorkplaceId) {
            return false;
        }

        return (string) $this->institution->zonalEducationOffice?->peo_wp_id === (string) $provinceWorkplaceId;
    }

    protected function resolveSubjectLabel(?Teacher $teacher): string
    {
        return $teacher?->currentTeachingSubject?->name_en
            ?? $teacher?->appointmentSubject?->name_en
            ?? $teacher?->mainSubject?->name_en
            ?? 'Subject not assigned';
    }

    protected function formatDuration($startDate): string
    {
        if (!$startDate) {
            return 'N/A';
        }

        $diff = Carbon::parse($startDate)->diff(now());
        $parts = [];

        if ($diff->y > 0) {
            $parts[] = $diff->y . 'y';
        }

        if ($diff->m > 0) {
            $parts[] = $diff->m . 'm';
        }

        if (empty($parts)) {
            $parts[] = max($diff->d, 1) . 'd';
        }

        return implode(' ', $parts);
    }

    protected function normalizeBoard(?string $board): string
    {
        return in_array($board, ['province', 'zone', 'national'], true)
            ? $board
            : 'province';
    }

    public function getBackRouteProperty(): string
    {
        return match ($this->board) {
            'zone' => route('transfer-board.zone-teacher-transfer', array_filter([
                'selectedBoardId' => $this->selectedBoardId,
            ])),
            'national' => route('transfer-board.ntional-teacher-transfer'),
            default => route('transfer-board.province-teacher-transfer', array_filter([
                'selectedBoardId' => $this->selectedBoardId,
            ])),
        };
    }

    public function getBackLabelProperty(): string
    {
        return match ($this->board) {
            'zone' => 'Back to Zonal Board',
            'national' => 'Back to National Board',
            default => 'Back to Provincial Board',
        };
    }

    public function statusBadge(string $status): array
    {
        return match ($status) {
            'submitted' => ['color' => 'blue', 'label' => 'Pending'],
            'processing' => ['color' => 'amber', 'label' => 'Pending'],
            'approved' => ['color' => 'green', 'label' => 'Approved'],
            default => ['color' => 'zinc', 'label' => ucfirst($status)],
        };
    }

    public function render()
    {
        return view('livewire.transfer-module.transfer-board.institution-profile-for-transfer-board');
    }
}
