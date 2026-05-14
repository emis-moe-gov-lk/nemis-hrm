<?php

namespace App\Livewire\MyProfile;

use App\Models\People;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\PeopleProfileEditRequest;

class ComplaintForm extends Component
{
    public $subject;
    public $complaint;
    public $peopleId;
    public $people;

    public function mount()
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized access');
        }

        $this->peopleId = Auth::user()->people_id;

        $this->people = People::where('people_id', $this->peopleId)->first();

        if (!$this->people) {
            abort(404, 'Profile not found');
        }
    }

    protected function rules()
    {
        return [
            'subject'   => 'required|string|max:255',
            'complaint' => 'required|string|min:10',
        ];
    }

    /* Live validation */
    public function updated($property)
    {
        $this->validateOnly($property);
    }

    public function save()
    {
        $this->validate();

        $pending = PeopleProfileEditRequest::where('people_id', $this->peopleId)
            ->where('status', 1)
            ->exists();

        if ($pending) {
            session()->flash(
                'warning',
                'You already have a pending request. Please wait until it is reviewed.'
            );
            return;
        }

        $ref = 'REQ-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));

        PeopleProfileEditRequest::create([
            'complaint_request_ref' => $ref,
            'people_id'             => $this->peopleId,
            'requested_changes'     => [
                'subject'   => $this->subject,
                'complaint' => $this->complaint,
            ],
            'status' => 1,
        ]);

        session()->flash('success', 'Your request has been submitted successfully.');

        $this->reset(['subject', 'complaint']);

        // Correct redirect
        return redirect()->route('my-profile.index', $this->people->people_id);
    }

    public function render()
    {
        return view('livewire.my-profile.complaint-form');
    }
}
