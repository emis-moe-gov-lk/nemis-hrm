<?php

namespace App\Livewire\Employees;

use App\Models\EmployerAppointment;
use App\Models\EmployerAppointmentPositionHistory;
use App\Models\People;
use App\Models\Position;
use Livewire\Component;

class PositionHistory extends Component
{
    public string $peopleId;
    public bool $canCreate = false;
    public bool $canDelete = false;

    // Form fields
    public $appointmentId;
    public $positionId;
    public $startDate;
    public $endDate;
    public $refLetterNo;
    public $remarks;

    // Options
    public $appointments = [];
    public $positions = [];
    public ?int $historyIdToDelete = null;

    public function mount()
    {
        // Super Admin Bypass
        if (auth()->check() && auth()->user()->hasRole('super-admin')) {
            $this->canCreate = true;
            $this->canDelete = true;
        }

        $people = People::where('people_id', $this->peopleId)->first();
        if ($people) {
            $this->appointments = EmployerAppointment::where('employee_id', $people->people_id)->with('service')->get();
            if (count($this->appointments) > 0) {
                $this->appointmentId = $this->appointments[0]->appointment_id;
                $this->updatedAppointmentId($this->appointmentId);
            }
        }
    }

    public function updatedAppointmentId($value)
    {
        if ($value) {
            $appointment = EmployerAppointment::where('appointment_id', $value)->first();
            if ($appointment) {
                $this->positions = Position::where('service_id', $appointment->service_id)->get()->sortBy('position_name')->values();
            } else {
                $this->positions = collect();
            }
        } else {
            $this->positions = collect();
        }
        $this->positionId = '';
    }

    public function savePositionHistory()
    {
        $this->validate([
            'appointmentId' => 'required',
            'positionId' => 'required',
            'startDate' => 'required|date',
            'endDate' => 'nullable|date|after_or_equal:startDate',
            'refLetterNo' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        EmployerAppointmentPositionHistory::create([
            'appointment_id' => $this->appointmentId,
            'employee_id' => $this->peopleId,
            'position_id' => $this->positionId,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'ref_letter_no' => $this->refLetterNo,
            'remarks' => $this->remarks,
            'is_active' => false,
        ]);

        $this->reset(['positionId', 'startDate', 'endDate', 'refLetterNo', 'remarks']);
        $this->dispatch('modal-close', name: 'add-position-history');
        session()->flash('success', 'Position history added successfully.');
    }

    public function confirmDelete($id)
    {
        $this->historyIdToDelete = $id;
        $this->dispatch('modal-show', name: 'delete-position-history-confirmation');
    }

    public function deleteHistory()
    {
        if (!$this->historyIdToDelete) return;

        $record = EmployerAppointmentPositionHistory::find($this->historyIdToDelete);
        if ($record && !$record->is_active) {
            $record->delete();
            session()->flash('success', 'Position history record deleted.');
        } else {
            session()->flash('error', 'Cannot delete active position records.');
        }

        $this->historyIdToDelete = null;
        $this->dispatch('modal-close', name: 'delete-position-history-confirmation');
    }

    public function render()
    {
        // Load the position history records for this employee
        $positionHistory = EmployerAppointmentPositionHistory::where('employee_id', $this->peopleId)
            ->with(['position', 'appointment.service'])
            ->orderBy('start_date', 'desc')
            ->get();

        return view('livewire.employees.position-history', compact('positionHistory'));
    }
}
