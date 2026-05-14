<?php

namespace App\Livewire\Institutions\Profile;

use Livewire\Component;
use App\Models\Institution;

class MissionVision extends Component
{
    public $institution;

    public $mission;
    public $vision;

    public function rules()
    {
        return [
            'mission' => [
                'nullable',
                'string',
                'max:500',
                'regex:/^[A-Za-z0-9\s\.\,\!\?\:\;\(\)\'\"\-]+$/',
            ],

            'vision' => [
                'nullable',
                'string',
                'max:500',
                'regex:/^[A-Za-z0-9\s\.\,\!\?\:\;\(\)\'\"\-]+$/',
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
        $this->mission = strtolower($this->institution->mission);
        $this->vision = strtolower($this->institution->vision);
    }

    public function render()
    {
        return view('livewire.institutions.profile.mission-vision');
    }

    public function updateMissionVision()
    {
        $this->validate();
        try {
            $this->institution->update([
                'mission' => $this->mission,
                'vision' => $this->vision,
            ]);
            session()->flash('success', 'Mission and Vision Updated Successfully.');
            return $this->redirect(url()->previous(), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update Mission and Vision: ' . $e->getMessage());
            return $this->redirect(url()->previous(), navigate: true);
        }
    }

    public function resetForm()
    {
        $this->mount($this->institution->id);
    }
}
