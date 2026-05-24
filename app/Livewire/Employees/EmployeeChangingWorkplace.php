<?php

namespace App\Livewire\Employees;

use App\Helpers\NicHelper;
use App\Models\EmployerAppointmentHistory;
use App\Models\InstitutionCategory;
use App\Models\OfficeLevel;
use App\Models\People;
use App\Models\User;
use App\Models\Workplaces;
use App\Models\ZonalEducationOffice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class EmployeeChangingWorkplace extends Component
{
    use WithPagination;

    // ── Search ────────────────────────────────────────────────────────────
    public string $search = '';

    // ── Jurisdiction ──────────────────────────────────────────────────────
    public array $allowedWorkplaceIds = [];

    // ── Modal state (Change Workplace) ────────────────────────────────────
    public ?string $selectedEmployeeId = null;
    public ?People $selectedPerson     = null;
    public bool    $showModal          = false;

    // ── Modal state (Release to Pool) ─────────────────────────────────────
    public bool    $showReleaseModal    = false;
    public string  $releaseSearchNic    = '';
    public ?People $employeeToRelease   = null;
    public ?string $releaseErrorMessage = null;

    // ── Cascading workplace selector ──────────────────────────────────────
    public ?string $officeLevel           = null;   // e.g. OLID006
    public ?string $zonalEducationOffice  = null;   // ZEO workplace_id
    public ?string $institutionCategory   = null;   // institution_category_id
    public ?string $workingPlace          = null;   // final selected workplace_id

    // ── Dropdown option lists (computed reactively) ───────────────────────
    public $officeLevelOption          = [];
    public $zonalEducationOfficeOption = [];
    public $institutionCategoryOption  = [];
    public $workingPlaceOption         = [];

    // ── Other form fields ─────────────────────────────────────────────────
    public ?string $effectiveDate = null;
    public ?string $refLetterNo   = null;

    // ── Validation ────────────────────────────────────────────────────────
    protected $rules = [
        'workingPlace'  => 'required|string',
        'effectiveDate' => 'required|date',
        'refLetterNo'   => 'required|string|max:100',
    ];

    protected $messages = [
        'workingPlace.required'  => 'Please select a workplace.',
        'effectiveDate.required' => 'Please provide the effective date.',
        'refLetterNo.required'   => 'Please provide the reference letter number.',
    ];

    // ── Mount ─────────────────────────────────────────────────────────────
    public function mount(): void
    {
        /** @var User $user */
        $user      = Auth::user();
        $workplace = $user?->workplace;

        $this->allowedWorkplaceIds = $workplace
            ? $workplace->getAllChildWorkplaces()
            : [];

        // Static option lists that don't change per-selection
        $this->officeLevelOption         = OfficeLevel::active()->orderBy('office_level_rank')->get();
        $this->zonalEducationOfficeOption = ZonalEducationOffice::active()->orderBy('short_name')->get();
        $this->institutionCategoryOption  = InstitutionCategory::active()->orderBy('institution_category_name')->get();
    }

    // ── Cascading reactivity ──────────────────────────────────────────────

    /**
     * Fires when officeLevel changes. Resets downstream selections
     * and repopulates the workplace list.
     */
    public function updatedOfficeLevel(): void
    {
        $this->zonalEducationOffice = null;
        $this->institutionCategory  = null;
        $this->workingPlace         = null;
        $this->refreshWorkplacesOptions();
    }

    /**
     * Fires when ZEO filter changes (only relevant for OLID006 = Institution level).
     */
    public function updatedZonalEducationOffice(): void
    {
        $this->institutionCategory = null;
        $this->workingPlace        = null;
        $this->refreshWorkplacesOptions();
    }

    /**
     * Fires when institution category filter changes.
     */
    public function updatedInstitutionCategory(): void
    {
        $this->workingPlace = null;
        $this->refreshWorkplacesOptions();
    }

    /**
     * Rebuilds $workingPlaceOption based on current filter state.
     * Only shows workplaces within the logged-in user's jurisdiction.
     */
    protected function refreshWorkplacesOptions(): void
    {
        if (!$this->officeLevel) {
            $this->workingPlaceOption = [];
            return;
        }

        $query = Workplaces::whereIn('workplace_id', $this->allowedWorkplaceIds)
            ->where('office_level_id', $this->officeLevel);

        // For institutions (OLID006), apply additional ZEO / category filters
        if ($this->officeLevel === 'OLID006') {

            if ($this->zonalEducationOffice) {
                $query->whereHas('institution', function ($q) {
                    $q->where('zeo_wp_id', $this->zonalEducationOffice);
                });
            }

            if ($this->institutionCategory) {
                $query->whereHas('institution', function ($q) {
                    $q->where('institution_category_id', $this->institutionCategory);
                });
            }
        }

        $this->workingPlaceOption = $query
            ->with(['institution', 'zonal', 'divisional'])
            ->get()
            ->sortBy(fn($w) => $w->office_name)
            ->values();
    }

    // ── Modal helpers ─────────────────────────────────────────────────────
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openModal(string $employeeId): void
    {
        $this->reset([
            'officeLevel',
            'zonalEducationOffice',
            'institutionCategory',
            'workingPlace',
            'effectiveDate',
            'refLetterNo',
            'workingPlaceOption',
        ]);
        $this->resetValidation();

        $this->selectedEmployeeId = $employeeId;
        $this->selectedPerson     = People::with([
            'title',
            'currentAppointment.rank',
            'currentAppointment.position',
            'currentAppointment.workplace',
            'currentAppointment.appointment',
            'currentAppointment.service',
        ])->where('people_id', $employeeId)->firstOrFail();

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal        = false;
        $this->selectedPerson   = null;
        $this->selectedEmployeeId = null;
        $this->reset([
            'officeLevel',
            'zonalEducationOffice',
            'institutionCategory',
            'workingPlace',
            'effectiveDate',
            'refLetterNo',
            'workingPlaceOption',
        ]);
        $this->resetValidation();
    }

    // ── Confirm transfer ──────────────────────────────────────────────────
    public function confirmChange(): void
    {
        $this->validate();

        $person  = $this->selectedPerson;

        if (!$person) {
            return;
        }

        $current = $person->currentAppointment;

        if (!$current) {
            session()->flash('error', 'This employee has no active appointment.');
            $this->closeModal();
            return;
        }

        if ($current->workplace_id === $this->workingPlace) {
            $this->addError('workingPlace', 'The selected workplace is already the current workplace.');
            return;
        }

        try {
            DB::transaction(function () use ($current) {
                // 1. Archive old state
                EmployerAppointmentHistory::create([
                    'appointment_id'        => $current->appointment_id,
                    'employee_id'           => $current->employee_id,
                    'appointment_letter_no' => $this->refLetterNo,
                    'appoint_date'          => $current->appoint_date,
                    'end_date'              => $this->effectiveDate,
                    'service_id'            => $current->appointment?->service_id,
                    'rank_id'               => $current->rank_id,
                    'position_id'           => $current->position_id,
                    'office_level_id'       => $current->office_level_id,
                    'workplace_id'          => $current->workplace_id,
                    'updated_type'          => '2', // 2 = workplace change
                ]);

                // 2. Apply new workplace
                $newWorkplace = Workplaces::where('workplace_id', $this->workingPlace)->firstOrFail();

                $current->update([
                    'workplace_id'          => $this->workingPlace,
                    'office_level_id'       => $newWorkplace->office_level_id,
                    'appoint_date'          => $this->effectiveDate,
                    'appointment_letter_no' => $this->refLetterNo,
                    'is_released_to_pool'   => 0,
                ]);
            });

            session()->flash('success', 'Workplace changed successfully.');
            $this->closeModal();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to change workplace: ' . $e->getMessage());
        }
    }

    // ── Release to Pool Logic ─────────────────────────────────────────────
    public function openReleaseModal(): void
    {
        $this->reset(['releaseSearchNic', 'employeeToRelease', 'releaseErrorMessage']);
        $this->showReleaseModal = true;
    }

    public function closeReleaseModal(): void
    {
        $this->showReleaseModal = false;
        $this->reset(['releaseSearchNic', 'employeeToRelease', 'releaseErrorMessage']);
    }

    public function findEmployeeToRelease(): void
    {
        $this->releaseErrorMessage = null;
        $this->employeeToRelease = null;

        $raw = trim($this->releaseSearchNic);
        if (empty($raw)) {
            $this->releaseErrorMessage = 'Please enter a valid NIC.';
            return;
        }

        if (!NicHelper::isValid($raw)) {
            $this->releaseErrorMessage = 'Invalid NIC format.';
            return;
        }

        $hash = NicHelper::hash(NicHelper::normalize($raw));
        $person = People::with(['title', 'currentAppointment.workplace'])
            ->where('nic_hash', $hash)
            ->first();

        if (!$person) {
            $this->releaseErrorMessage = 'Employee not found.';
            return;
        }

        if (!$person->currentAppointment) {
            $this->releaseErrorMessage = 'This employee has no active appointment.';
            return;
        }

        if ($person->currentAppointment->is_released_to_pool) {
            $this->releaseErrorMessage = 'This employee is already released to the pool.';
            return;
        }

        // Verify jurisdiction
        if (!in_array($person->currentAppointment->workplace_id, $this->allowedWorkplaceIds)) {
            $this->releaseErrorMessage = 'This employee is outside your jurisdiction.';
            return;
        }

        $this->employeeToRelease = $person;
    }

    public function confirmReleaseToPool(): void
    {
        if (!$this->employeeToRelease || !$this->employeeToRelease->currentAppointment) {
            return;
        }

        try {
            DB::transaction(function () {
                $current = $this->employeeToRelease->currentAppointment;

                $current->update([
                    'is_released_to_pool' => 1,
                ]);
            });

            // Close the modal and reset state
            $this->showReleaseModal = false;
            $this->releaseSearchNic = '';
            $this->employeeToRelease = null;
            $this->releaseErrorMessage = null;
            $this->dispatch('modal-close', name: 'release-employee-modal');

            // Dispatch a success event (if you have a listener for this, or a toast)
            $this->dispatch('toast', message: 'Employee successfully released to pool.', type: 'success');
        } catch (\Exception $e) {
            $this->releaseErrorMessage = 'An error occurred while releasing the employee: ' . $e->getMessage();
        }
    }

    // ── Render ────────────────────────────────────────────────────────────
    public function render()
    {
        $query = People::with([
            'title',
            'currentAppointment.rank',
            'currentAppointment.position',
            'currentAppointment.workplace',
            'currentAppointment.appointment',
        ])
            ->whereHas('currentAppointment', function ($q) {
                $q->whereIn('workplace_id', $this->allowedWorkplaceIds)
                    ->where('is_released_to_pool', 1);
            })
            ->active();

        if (!empty($this->search)) {
            $raw   = trim($this->search);
            $isNic = NicHelper::isValid($raw);

            $query->where(function ($q) use ($raw, $isNic) {
                $q->where('people_id', 'like', "%{$raw}%");

                if ($isNic) {
                    $hash = NicHelper::hash(NicHelper::normalize($raw));
                    $q->orWhere('nic_hash', $hash);
                } else {
                    $q->orWhere('phone', 'like', "%{$raw}%")
                        ->orWhere('email', 'like', "%{$raw}%");
                }
            });
        }

        return view('livewire.employees.employee-changing-workplace', [
            'employees' => $query->paginate(15),
        ]);
    }
}
