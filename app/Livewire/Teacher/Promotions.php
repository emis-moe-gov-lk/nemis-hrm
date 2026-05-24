<?php

namespace App\Livewire\Teacher;

use App\Helpers\NicHelper;
use App\Models\EmployerAppointmentHistory;
use App\Models\EmployerCurrentAppointment;
use App\Models\Institution;
use App\Models\People;
use App\Models\ServiceRank;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Promotions extends Component
{
    use WithPagination;
    public $search = '';
    public $ranks;
    public $allowedWorkplaceIds;
    public $institutions;
    public $gradeFilter = '';
    public $institutionFilter = '';
    public $teacher;

    public $showModalPromoteTeacher = false;

    public $promotion_grade, $promotion_letter_no, $promotion_effect_date;

    public $editAppointmentId;

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
            return view('livewire.teacher.teacher-list', compact('employees'));
        }

        $this->allowedWorkplaceIds = $workplace->getAllChildWorkplaces();

        $this->ranks = ServiceRank::where('service_id', 'SER001')->get();
        $this->institutions = Institution::whereIn('workplace_id', $this->allowedWorkplaceIds)->get();
    }

    public function promoteTeacher($id)
    {
        $this->teacher = People::whereHas('appointment', function ($q) {
            $q->where('service_id', 'SER001');
        })
            ->whereHas('currentAppointment', function ($q) {
                $q->whereIn('workplace_id', $this->allowedWorkplaceIds);
            })
            ->where('id', $id)
            ->active()
            ->first();

        $this->editAppointmentId = $this->teacher->currentAppointment->appointment_id;

        $this->showModalPromoteTeacher = true; // ensure modal is open
    }

    public function updateTeacherPromotion()
    {
        $this->validate([
            'promotion_grade' => [
                'required',
                'string',
                'regex:/^[RANK]{4}\d{3}$/' // Example: RANK001
            ],
            'promotion_letter_no' => [
                'required',
                'string',
                'max:255',
            ],
            'promotion_effect_date' => 'required|date',
        ]);

        $this->resetPage();

        EmployerAppointmentHistory::create([
            'appointment_id' => $this->editAppointmentId,
            'employee_id' => $this->teacher->people_id,
            'appointment_letter_no' => $this->teacher->currentAppointment->appointment_letter_no,
            'appoint_date' => $this->teacher->currentAppointment->appoint_date,
            'end_date' => $this->promotion_effect_date,
            'service_id' => $this->teacher->currentAppointment->appointment->service_id,
            'rank_id' => $this->teacher->currentAppointment->rank_id,
            'position_id' => $this->teacher->currentAppointment->position_id,
            'office_level_id' => $this->teacher->currentAppointment->office_level_id,
            'workplace_id' => $this->teacher->currentAppointment->workplace_id,
            'updated_type' => '0',
        ]);

        EmployerCurrentAppointment::where('appointment_id', $this->editAppointmentId)->update([
            'rank_id' => $this->promotion_grade,
            'appointment_letter_no' => $this->promotion_letter_no,
            'appoint_date' => $this->promotion_effect_date,
        ]);

        $this->showModalPromoteTeacher = false;

        session()->flash('message', '✅ Teacher promoted successfully!');

        $this->reset(['promotion_grade', 'promotion_letter_no', 'promotion_effect_date', 'editAppointmentId', 'teacher']);
    }

    public function render()
    {

        // ✅ Build query FIRST
        $query = People::with([
            'currentAppointment',
            'currentAppointment.rank',
            'currentAppointment.appointment',
            'currentAppointment.position',
            'currentAppointment.workplace'
        ])
            ->whereHas('appointment', function ($q) {
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

        return view('livewire.teacher.promotions', compact('employees'));
    }
}
