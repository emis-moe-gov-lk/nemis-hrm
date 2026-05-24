<?php

namespace App\Livewire\Teacher;

use App\Helpers\NicHelper;
use App\Models\EmployerAppointment;
use App\Models\EmployerAppointmentHistory;
use App\Models\EmployerCurrentAppointment;
use App\Models\Institution;
use App\Models\People;
use App\Models\ReasonsForTerminationOfService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PensionSystem extends Component
{
    use WithPagination;
    public $search = '';
    public $reasons;
    public $allowedWorkplaceIds;
    public $institutions;
    public $gradeFilter = '';
    public $institutionFilter = '';
    public $teacher, $editAppointmentId;

    public $pension_reason, $pension_effect_date;


    public $showModalPensionTeacher = false;

    protected $updatesQueryString = ['search'];

    public function resetFilters()
    {
        $this->search = '';
        $this->gradeFilter = '';
        $this->institutionFilter = '';
    }

    public function mount()
    {
        $logged = Auth::user()->load('workplace');
        $workplace = $logged->workplace;

        if (!$workplace) {
            $employees = People::where('id', 0)->paginate(10);
            return view('livewire.teacher.pension-system', compact('employees'));
        }

        $this->allowedWorkplaceIds = $workplace->getAllChildWorkplaces();

        $this->reasons = ReasonsForTerminationOfService::all();
        $this->institutions = Institution::whereIn('workplace_id', $this->allowedWorkplaceIds)->get();
    }

    public function pensionTeacher($id)
    {
        $this->teacher = People::with(['currentAppointment.appointment'])
            ->whereHas('appointment', function ($q) {
                $q->where('service_id', 'SER001');
            })
            ->whereHas('currentAppointment', function ($q) {
                $q->whereIn('workplace_id', $this->allowedWorkplaceIds);
            })
            ->where('id', $id)
            ->active()
            ->first();

        if (! $this->teacher || ! $this->teacher->currentAppointment) {
            session()->flash('error', 'Teacher not found or no active current appointment available.');
            return;
        }

        $this->editAppointmentId = $this->teacher->currentAppointment->appointment_id;

        $this->showModalPensionTeacher = true; // ensure modal is open
    }

    public function updateTeacherPension()
    {
        $this->resetPage();

        // Get current appointment safely
        $current = $this->teacher?->currentAppointment;

        if (! $current) {
            session()->flash('error', 'No active appointment found!');
            return;
        }

        $appointment = $current->appointment;

        if (! $appointment) {
            session()->flash('error', 'Parent appointment record is missing for this teacher.');
            return;
        }

        try {
            DB::transaction(function () use ($current, $appointment) {

                // 1. Update main appointment
                EmployerAppointment::where('appointment_id', $this->editAppointmentId)->update([
                    'termination_id' => $this->pension_reason,
                    'termination_date' => $this->pension_effect_date,
                    'active_status' => 0, // IMPORTANT (not boolean)
                ]);

                // 2. Insert into history
                EmployerAppointmentHistory::create([
                    'appointment_id' => $this->editAppointmentId,
                    'employee_id' => $this->teacher->people_id,
                    'appointment_letter_no' => $current->appointment_letter_no,
                    'appoint_date' => $current->appoint_date,
                    'end_date' => $this->pension_effect_date,
                    'service_id' => $appointment->service_id,
                    'rank_id' => $current->rank_id,
                    'position_id' => $current->position_id,
                    'office_level_id' => $current->office_level_id,
                    'workplace_id' => $current->workplace_id,
                    'updated_type' => '0',
                ]);

                // 3. Remove from current appointments
                EmployerCurrentAppointment::where('appointment_id', $this->editAppointmentId)->delete();
            });

            // UI Updates
            $this->showModalPensionTeacher = false;

            session()->flash('message', '✅ Teacher pensioned successfully!');

            $this->reset([
                'pension_reason',
                'pension_effect_date',
                'editAppointmentId',
                'teacher'
            ]);

        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong! ' . $e->getMessage());
        }
    }

    public function render()
    {
        // ✅ Build query FIRST
        $query = People::whereHas('appointment', function ($q) {
            $q->where('service_id', 'SER001');
        })
            ->whereHas('currentAppointment', function ($q) {
                $q->whereIn('workplace_id', $this->allowedWorkplaceIds);
            })
            ->active();

        // Apply search BEFORE paginate
        if (!empty($this->search)) {

            $search = trim($this->search);
            $isNic = NicHelper::isValid($search);

            $query->where(function ($q) use ($search, $isNic) {

                if ($isNic) {
                    $normalizedNic = NicHelper::normalize($search);
                    $hashSearch = NicHelper::hash($normalizedNic);

                    $q->where('nic_hash', $hashSearch);
                }

                $q->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if (!empty($this->gradeFilter)) {
            $query->whereHas('currentAppointment', function ($q) {
                $q->where('rank_id', $this->gradeFilter);
            });
        }

        if (!empty($this->institutionFilter)) {
            $query->whereHas('currentAppointment', function ($q) {
                $q->where('workplace_id', $this->institutionFilter);
            });
        }

        //paginate at the END
        $employees = $query->paginate(20);

        return view('livewire.teacher.pension-system', compact('employees'));
    }
}
