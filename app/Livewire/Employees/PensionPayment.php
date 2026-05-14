<?php

namespace App\Livewire\Employees;

use Exception;
use App\Models\People;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class PensionPayment extends Component
{
    public $peopleId;
    public $canEdit;
    public $employee;

    public $wopNo;
    public $paySheetNo;

    public function rules()
    {
        return [
            'wopNo' => 'nullable|string|max:20',
            'paySheetNo' => 'nullable|string|max:20',
        ];
    }

    // -------------------------
    // Live Validation on Field Update
    // -------------------------
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount($peopleId)
    {
        //$this->canEdit = $canEdit;
        $this->employee = People::where('people_id', $peopleId)->first();
        $this->wopNo = $this->employee->appointment->w_op_no;
        $this->paySheetNo = $this->employee->appointment->pay_sheet_no;
    }

    public function updatePensionPayment()
    {
        $this->validate([
            'wopNo' => 'nullable|string|max:255',
            'paySheetNo' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction(); // Start transaction

        // Update pension and payment details
        try{
            $this->employee->appointment->w_op_no = $this->wopNo;
            $this->employee->appointment->pay_sheet_no = $this->paySheetNo;
            $this->employee->appointment->save();

            session()->flash('success', 'Pension and Payment details updated successfully.');
            DB::commit(); // Commit only if all operations succeeded
            return $this->redirect(url()->previous(), navigate: true);
        } catch (Exception $e) {
            DB::rollBack(); // Rollback on any failure
            session()->flash('error', 'An error occurred while updating pension and payment details: ' . $e->getMessage());
        }

    }

    public function render()
    {
        return view('livewire.employees.pension-payment');
    }
}
