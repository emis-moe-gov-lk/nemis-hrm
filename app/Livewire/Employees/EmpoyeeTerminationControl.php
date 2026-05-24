<?php

namespace App\Livewire\Employees;

use Livewire\Component;
use App\Models\People;
use App\Models\ReasonsForTerminationOfService;
use Illuminate\Support\Facades\DB;
use App\Models\EmployerAppointment;
use App\Models\EmployerCurrentAppointment;

class EmpoyeeTerminationControl extends Component
{
    public string $employeeId;
    public ?People $person = null;

    // Termination Form State
    public ?string $termination_date = null;
    public $termination_reason = '';
    public $remarks = '';
    public $reasons = [];

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
        $this->reasons = ReasonsForTerminationOfService::active()->get();
    }

    public function processTermination()
    {
        $this->validate([
            'termination_date' => 'required|date',
            'termination_reason' => 'required',
        ]);

        try {
            DB::transaction(function () {
                $appointmentId = $this->person->currentAppointment?->appointment_id;

                if ($appointmentId) {
                    // 1. Update main appointment
                    EmployerAppointment::where('appointment_id', $appointmentId)->update([
                        'termination_id' => $this->termination_reason,
                        'termination_date' => $this->termination_date,
                        'active_status' => 0, // IMPORTANT (not boolean)
                    ]);

                    // 2. Remove from current appointments
                    EmployerCurrentAppointment::where('appointment_id', $appointmentId)->delete();
                }
            });

            session()->flash('message', 'Service termination processed successfully.');
            return redirect()->route('teacher.overview');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to process termination: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.employees.empoyee-termination-control');
    }
}
