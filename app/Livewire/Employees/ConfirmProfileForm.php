<?php

namespace App\Livewire\Employees;

use Livewire\Component;
use App\Models\People;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ConfirmProfileForm extends Component
{
    public $peopleId; // teacher people_id
    public $people;
    public $password;

    public function mount($peopleId)
    {
        $this->peopleId = $peopleId;
        $this->people = People::where('people_id', $this->peopleId)->first();
    }

    public function rules()
    {
        return [
            'password' => 'required|string|max:255',
        ];
    }

    // -------------------------
    // Live Validation on Field Update
    // -------------------------
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function submit()
    {
        $this->validate();

        // Check if the password matches the current user's password
        if (!Hash::check($this->password, Auth::user()->password)) {
            $this->addError('password', 'The provided password does not match your current password.');
            return;
        }

        // Get the active appointment
        $appointment = $this->people->appointment;

        if (!$appointment) {
            session()->flash('error', 'No active appointment found for this teacher.');
            return;
        }

        if ($appointment->is_confirmed) {
            session()->flash('info', 'This profile is already confirmed.');
            return;
        }

        // Update confirmation details
        $appointment->update([
            'is_confirmed' => true,
        ]);

        session()->flash('success', 'Profile confirmed successfully.');
        
        //$this->redirect(route('teacher.profile.index', $this->people->id), navigate: true);
        return $this->redirect(url()->previous(), navigate: true);
    }
    
    public function render()
    {
        return view('livewire.employees.confirm-profile-form');
    }
}
