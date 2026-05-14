<?php

namespace App\Livewire\Institutions\Profile;

use App\Models\Institution;
use Livewire\Component;
use Illuminate\Validation\Rule;

class ContactInformation extends Component
{
    public $institution;
    public $intituteNumber;
    public $cencesNumber;
    public $email;
    public $phone;

    public function rules()
    {
        return [
            'intituteNumber' => [
                'required',
                'numeric',
                Rule::unique('institutions', 'intituteNumber')->ignore($this->institution->id),
            ],
            'cencesNumber' => [
                'required',
                'numeric',
                Rule::unique('institutions', 'cencesNumber')->ignore($this->institution->id),
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('institutions', 'email')->ignore($this->institution->id),
            ],

            'phone' => [
                'required',
                'numeric',
                Rule::unique('institutions', 'phone')->ignore($this->institution->id),
            ],
        ];
    }

    public function updated($property)
    {
        $this->validateOnly($property);
    }

    public function mount($institutionId)
    {
        $this->institution = Institution::find($institutionId);
        $this->intituteNumber = $this->institution->workplace_id;
        $this->cencesNumber = $this->institution->census_no;
        $this->email = $this->institution->email;
        $this->phone = $this->institution->phone;
    }

    public function render()
    {
        return view('livewire.institutions.profile.contact-information');
    }

    public function updateContactInformation()
    {
        $this->validate();
        try {
            $this->institution->update([
                'email' => $this->email,
                'phone' => $this->phone,
            ]);
            session()->flash('success', 'Contact Information Updated Successfully.');
            return $this->redirect(url()->previous(), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update Contact Information: ' . $e->getMessage());
            return $this->redirect(url()->previous(), navigate: true);
        }
    }

    public function resetForm()
    {
        $this->mount($this->institution->id);
    }
}
