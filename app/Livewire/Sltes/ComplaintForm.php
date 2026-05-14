<?php

namespace App\Livewire\Sltes;

use App\Models\People;
use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\PeopleProfileEditRequest;

class ComplaintForm extends Component
{
    public $subject;
    public $complaint;
    public $peopleId; // Education Administrator Officer people_id
    public $people;

    public function mount($peopleId)
    {
        $this->peopleId = $peopleId;
        $this->people = People::where('people_id', $this->peopleId)->first();
    }

    public function rules()
    {
        return [
            'subject' => 'required|string|max:255',
            'complaint' => 'required|string|min:10',
        ];
    }

    // -------------------------
    // Live Validation on Field Update
    // -------------------------
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $this->validate();

        // Check if user already has a pending request
        $pending = PeopleProfileEditRequest::where('people_id', $this->peopleId)
            ->where('status', '1')   // 1 = pending
            ->exists();

        if ($pending) {
            session()->flash('warning', 'You already have a pending request. Please wait until it is reviewed.');
            return;
        }

        // Generate unique reference no: REQ-YYYYMMDD-XXXX
        $ref = 'REQ-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));

        PeopleProfileEditRequest::create([
            'complaint_request_ref' => $ref,
            'people_id' => $this->peopleId,
            'requested_changes' => [
                'subject'   => $this->subject,
                'complaint' => $this->complaint,
            ],
            'status' => '1',
        ]);

        session()->flash('success', 'Your request has been submitted successfully!');

        $this->reset(['subject', 'complaint']);
        $this->redirect(route('sltes.profile.index', $this->people->id), navigate: true);
    }
    
    public function render()
    {
        return view('livewire.sltes.complaint-form');
    }
}
