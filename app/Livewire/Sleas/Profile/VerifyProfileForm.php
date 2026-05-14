<?php

namespace App\Livewire\Sleas\Profile;

use Livewire\Component;
use App\Models\People;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class VerifyProfileForm extends Component
{
    use AuthorizesRequests;
    
    public $peopleId; // Development Officer people_id
    public $people;
    public $password;

    public function mount($peopleId)
    {
        $this->peopleId = $peopleId;

        $people = People::where('people_id', $this->peopleId)->first();
        $this->authorize('viewRestrict', $people);

        $this->people = $people;
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
            session()->flash('error', 'No active appointment found for this development Officer.');
            return;
        }

        if ($appointment->is_verified) {
            session()->flash('info', 'This profile is already confirmed.');
            return;
        }

        // Update confirmation details
        $appointment->update([
            'is_verified' => true,
        ]);

        session()->flash('success', 'Profile confirmed successfully.');
        
        //$this->redirect(route('dos.profile.index', $this->people->id), navigate: true);
        return $this->redirect(url()->previous(), navigate: true);
    }

    public function render()
    {
        return view('livewire.slas.profile.verify-profile-form');
    }
}
