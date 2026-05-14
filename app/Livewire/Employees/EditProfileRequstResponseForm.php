<?php

namespace App\Livewire\Employees;

use App\Models\People;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class EditProfileRequstResponseForm extends Component
{
    public $peopleId;
    public $people;
    public $complaint;

    public $response;   // approve/reject
    public $reply;      // reply message

    public $responseOption;
    public $replyOption;

    public function rules()
    {
        return [
            'response' => 'required|in:2,3',   // 2 = approve, 3 = reject
            'reply'    => 'required|string|max:255',
        ];
    }

    public function mount($peopleId)
    {
        $this->peopleId = $peopleId;

        // Teacher exists?
        $this->people = People::where('people_id', $peopleId)->firstOrFail();

        // Load only ONE active pending request
        $this->complaint = $this->people->profileEditRequests()
            ->where('status', 1) // Pending
            ->latest()
            ->first();

        // If no complaint, protect view from errors
        if (!$this->complaint) {
            //session()->flash('info', 'There is no pending profile update request.');
        }

        // Response dropdown options
        $this->responseOption = [
            '2' => 'Approve',
            '3' => 'Reject',
        ];

        // Predefined outcomes
        $this->replyOption = [
            'Your request has been completed.' => 'Your request has been completed.',
            'Your appeal is unclear.' => 'Your appeal is unclear.',
            'Information not updated. Report to the regional office.' => 'Information not updated. Report to the regional office.',
            'There is no related information in your personal file.' => 'There is no related information in your personal file.',
            'It is mandatory to meet with the officer in charge of your personal files.' => 'It is mandatory to meet with the officer in charge of your personal files.',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function submit()
    {
        $this->validate();

        if (!$this->complaint) {
            session()->flash('error', 'No pending profile update request found.');
            return;
        }

        // Prevent processing same request twice
        if ($this->complaint->status != 1) {
            session()->flash('warning', 'This request has already been processed.');
            return;
        }

        // Ensure reviewer exists
        $reviewer = Auth::user()->people_id ?? null;

        // Update safely
        $this->complaint->update([
            'status'          => $this->response, // 2 = approve, 3 = reject
            'review_comments' => $this->reply,
            'reviewed_by'     => $reviewer,
            'responded_at'    => now(),
        ]);

        session()->flash('success', 'Profile update request processed successfully.');

        return $this->redirect(url()->previous(), navigate: true);
    }

    public function render()
    {
        return view('livewire.employees.edit-profile-requst-response-form');
    }
}
