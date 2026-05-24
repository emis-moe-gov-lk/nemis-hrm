<?php

namespace App\Livewire\Employees;

use App\Models\EmployerAppointmentWorkplaceHistory;
use App\Models\Institution;
use App\Models\InstitutionCategory;
use App\Models\OfficeLevel;
use App\Models\People;
use App\Models\User;
use App\Models\Workplaces;
use App\Models\ZonalEducationOffice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class EmployeeWorkplaceChangeForm extends Component
{
    // ── Route param ───────────────────────────────────────────────────────
    public string  $employeeId;
    public ?People $person = null;

    // ── Cascading workplace selector ──────────────────────────────────────
    public ?string $officeLevel          = null;
    public ?string $zonalEducationOffice = null;
    public ?string $institutionCategory  = null;
    public ?string $workingPlace         = null;

    // ── Dropdown option lists ─────────────────────────────────────────────
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
    public function mount(string $employee): void
    {
        $this->employeeId = $employee;

        $this->person = People::with([
            'title',
            'currentAppointment.rank',
            'currentAppointment.position',
            'currentAppointment.workplace',
            'currentAppointment.appointment',
            'currentAppointment.service',
        ])->findOrFail($employee);

        $this->officeLevelOption = OfficeLevel::active()->orderBy('office_level_rank')->get();
        $this->zonalEducationOfficeOption = ZonalEducationOffice::active()->orderBy('short_name')->get();
        $this->institutionCategoryOption  = InstitutionCategory::active()->orderBy('institution_category_name')->get();
        $this->workingPlaceOption = collect();
    }

    // ── Jurisdiction (cached) ──────────────────────────────────────────────
    protected function jurisdictionIds(): array
    {
        $userId = Auth::id();

        return Cache::remember("user_{$userId}_jurisdiction_ids", 600, function () {
            /** @var User $user */
            $user      = Auth::user();
            $workplace = $user?->workplace;

            return $workplace ? $workplace->getAllChildWorkplaces() : [];
        });
    }

    // ── Cascading reactivity ──────────────────────────────────────────────

    public function updatedOfficeLevel(): void
    {
        $this->zonalEducationOffice = null;
        $this->institutionCategory  = null;
        $this->workingPlace         = null;
        $this->refreshWorkplacesOptions();
    }

    public function updatedZonalEducationOffice(): void
    {
        $this->institutionCategory = null;
        $this->workingPlace        = null;
        $this->refreshWorkplacesOptions();
    }

    public function updatedInstitutionCategory(): void
    {
        $this->workingPlace = null;
        $this->refreshWorkplacesOptions();
    }

    protected function refreshWorkplacesOptions(): void
    {
        if (!$this->officeLevel) {
            $this->workingPlaceOption = collect();
            return;
        }

        $ids = $this->jurisdictionIds();

        if ($this->officeLevel === 'OLID006') {
            if ($this->zonalEducationOffice && $this->institutionCategory) {
                // Load schools similar to WorkingPlaceHistory but restricted by jurisdiction
                $this->workingPlaceOption = Institution::whereIn('workplace_id', $ids)
                    ->where('zeo_wp_id', $this->zonalEducationOffice)
                    ->where('institution_category_id', $this->institutionCategory)
                    ->orderBy('name', 'asc')
                    ->get()
                    ->map(function ($item) {
                        return (object) [
                            'workplace_id' => $item->workplace_id,
                            'office_name' => $item->census_no . ' - ' . $item->name
                        ];
                    });
            } else {
                $this->workingPlaceOption = collect();
            }
        } else {
            // Load other offices restricted by jurisdiction
            $this->workingPlaceOption = Workplaces::whereIn('workplace_id', $ids)
                ->where('office_level_id', $this->officeLevel)
                ->with(['zonal', 'divisional']) // Eager load to prevent N+1 on office_name accessor
                ->get()
                ->map(function ($item) {
                    return (object) [
                        'workplace_id' => $item->workplace_id,
                        'office_name' => $item->office_name
                    ];
                })
                ->sortBy('office_name')
                ->values();
        }
    }

    // ── Confirm ───────────────────────────────────────────────────────────
    public function confirmChange(): void
    {
        $this->validate();

        if (!$this->person) {
            session()->flash('error', 'Employee data is missing.');
            return;
        }

        $current = $this->person->currentAppointment;

        if (!$current) {
            session()->flash('error', 'This employee has no active appointment.');
            return;
        }

        if ($current->workplace_id === $this->workingPlace) {
            $this->addError('workingPlace', 'The selected workplace is already the current workplace.');
            return;
        }

        try {
            DB::transaction(function () use ($current) {
                // 1. Archive old state
                EmployerAppointmentWorkplaceHistory::create([
                    'appointment_id'        => $current->appointment_id,
                    'employee_id'           => $current->employee_id,
                    'workplace_id'          => $current->workplace_id,
                    'office_level_id'       => $current->office_level_id,
                    'start_date'            => $current->appoint_date,
                    'end_date'              => $this->effectiveDate,
                    'ref_letter_no'         => $current->appointment_letter_no,
                    'remarks'               => 'Transferred to new workplace via system',
                    'is_active'             => false,
                ]);

                // 2. Apply new workplace
                $newWorkplace = Workplaces::where('workplace_id', $this->workingPlace)->firstOrFail();

                $current->update([
                    'workplace_id'          => $this->workingPlace,
                    'office_level_id'       => $newWorkplace->office_level_id,
                    'appoint_date'          => $this->effectiveDate,
                    'appointment_letter_no' => $this->refLetterNo,
                ]);
            });

            // Bust jurisdiction cache so changes are reflected immediately
            Cache::forget('user_' . Auth::id() . '_jurisdiction_ids');

            session()->flash('success', 'Workplace changed successfully.');
            $this->redirect(route('employees.changing-workplace'), navigate: true);

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to change workplace: ' . $e->getMessage());
        }
    }

    // ── Render ────────────────────────────────────────────────────────────
    public function render()
    {
        return view('livewire.employees.employee-workplace-change-form');
    }
}
