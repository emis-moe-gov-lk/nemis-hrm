<?php

namespace App\Livewire\Employees;

use Livewire\Component;
use App\Models\People;
use App\Models\ServiceRank;
use App\Models\Position;
use App\Models\EmployerAppointmentRankHistory;
use App\Models\EmployerAppointmentPositionHistory;
use Illuminate\Support\Facades\DB;

class EmployeePromotionControl extends Component
{
    public string $employeeId;
    public ?People $person = null;
    public ?string $NewRankId = null;
    public ?string $newPositionId = null;
    public ?string $rankRefLetterNo = null;
    public ?string $rankStartDate = null;
    public ?string $positionRefLetterNo = null;
    public ?string $positionStartDate = null;
    public $availableRanks = [];
    public $availablePositions = [];
    public $rankHistory = [];
    public $positionHistory = [];

    public function mount(string $employee)
    {
        $this->employeeId = $employee;
        $this->loadData();
    }

    public function loadData()
    {
        $this->person = People::with([
            'title',
            'currentAppointment.rank',
            'currentAppointment.workplace',
            'currentAppointment.position',
            'currentAppointment.service',
            'currentAppointment.appointment'
        ])->where('people_id', $this->employeeId)->firstOrFail();

        $serviceId = $this->person->currentAppointment?->appointment?->service_id;
        if ($serviceId) {
            $this->availableRanks = ServiceRank::where('service_id', $serviceId)->get();
            $this->availablePositions = Position::where('service_id', $serviceId)->active()->get();
        }

        $this->rankHistory = EmployerAppointmentRankHistory::with('rank')
            ->where('employee_id', $this->employeeId)
            ->orderBy('start_date', 'desc')
            ->get();

        $this->positionHistory = EmployerAppointmentPositionHistory::with('position')
            ->where('employee_id', $this->employeeId)
            ->orderBy('start_date', 'desc')
            ->get();
    }

    public function updateRank()
    {
        $this->validate([
            'NewRankId' => 'required',
            'rankRefLetterNo' => 'required',
            'rankStartDate' => 'required|date',
        ]);

        try {
            DB::transaction(function () {
                // 1. Deactivate current active rank history
                EmployerAppointmentRankHistory::where('employee_id', $this->employeeId)
                    ->where('is_active', 1)
                    ->update([
                        'is_active' => 0,
                        'end_date' => $this->rankStartDate,
                    ]);

                // 2. Create new rank history
                EmployerAppointmentRankHistory::create([
                    'appointment_id' => $this->person->currentAppointment?->appointment_id,
                    'employee_id' => $this->employeeId,
                    'rank_id' => $this->NewRankId,
                    'ref_letter_no' => $this->rankRefLetterNo,
                    'start_date' => $this->rankStartDate,
                    'is_active' => 1,
                ]);

                // 3. Update current appointment
                if ($this->person->currentAppointment) {
                    $this->person->currentAppointment->update([
                        'rank_id' => $this->NewRankId,
                    ]);
                }
            });

            $this->reset(['NewRankId', 'rankRefLetterNo', 'rankStartDate']);
            $this->loadData();
            session()->flash('message', 'Rank updated successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update rank: ' . $e->getMessage());
        }
    }

    public function updatePosition()
    {
        $this->validate([
            'newPositionId' => 'required',
            'positionRefLetterNo' => 'required',
            'positionStartDate' => 'required|date',
        ]);

        try {
            DB::transaction(function () {
                // 1. Deactivate current active position history
                EmployerAppointmentPositionHistory::where('employee_id', $this->employeeId)
                    ->where('is_active', 1)
                    ->update([
                        'is_active' => 0,
                        'end_date' => $this->positionStartDate,
                    ]);

                // 2. Create new position history
                EmployerAppointmentPositionHistory::create([
                    'appointment_id' => $this->person->currentAppointment?->appointment_id,
                    'employee_id' => $this->employeeId,
                    'position_id' => $this->newPositionId,
                    'ref_letter_no' => $this->positionRefLetterNo,
                    'start_date' => $this->positionStartDate,
                    'is_active' => 1,
                ]);

                // 3. Update current appointment
                if ($this->person->currentAppointment) {
                    $this->person->currentAppointment->update([
                        'position_id' => $this->newPositionId,
                    ]);
                }
            });

            $this->reset(['newPositionId', 'positionRefLetterNo', 'positionStartDate']);
            $this->loadData();
            session()->flash('message', 'Position updated successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update position: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.employees.employee-promotion-control');
    }
}
