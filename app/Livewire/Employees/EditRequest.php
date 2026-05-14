<?php

namespace App\Livewire\Employees;

use App\Models\PeopleProfileEditRequest;
use App\Models\People;
use Livewire\Component;

class EditRequest extends Component
{
    public $peopleId;
    public $employee;
    public $editRequests;

    public function mount($peopleId)
    {
        $this->peopleId = $peopleId;
        
        // Load employee safely
        $this->employee = People::where('people_id', $peopleId)->firstOrFail();

        // Load edit requests safely & cleanly
        $this->editRequests = PeopleProfileEditRequest::where('people_id', $peopleId)
            ->latest() // same as orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.employees.edit-request');
    }
}
