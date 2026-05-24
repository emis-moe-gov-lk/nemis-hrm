<?php

namespace App\Livewire\Offices\Pmoe\Profile;

use Livewire\Component;
use App\Models\ProvincialMinistryOfEducationOffice;

class PmoeProfile extends Component
{
    public $id;

    // ── Contact Details ────────────────────────────────────────────
    public ?string $email    = null;
    public ?string $phone    = null;

    // ── Location ──────────────────────────────────────────────────
    public ?string $address      = null;
    public ?string $postal_code  = null;
    public ?string $latitude     = null;
    public ?string $longitude    = null;

    // ── Mission & Vision ──────────────────────────────────────────
    public ?string $mission      = null;
    public ?string $vision       = null;

    public function mount()
    {
        $pmoe = ProvincialMinistryOfEducationOffice::findOrFail($this->id);

        $this->email       = $pmoe->email;
        $this->phone       = $pmoe->phone;
        $this->address     = $pmoe->address;
        $this->postal_code = $pmoe->postal_code;
        $this->latitude    = $pmoe->latitude;
        $this->longitude   = $pmoe->longitude;
        $this->mission     = $pmoe->mission;
        $this->vision      = $pmoe->vision;
    }

    // ── Update Contact Details ────────────────────────────────────
    public function updateContactDetails()
    {
        $this->validate([
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        try {
            ProvincialMinistryOfEducationOffice::findOrFail($this->id)->update([
                'email' => $this->email,
                'phone' => $this->phone,
            ]);
            session()->flash('success', 'Contact details updated successfully.');
            return $this->redirect(url()->previous(), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update contact details: ' . $e->getMessage());
            return $this->redirect(url()->previous(), navigate: true);
        }
    }

    // ── Update Location ───────────────────────────────────────────
    public function updateLocation()
    {
        $this->validate([
            'address'     => 'nullable|string|max:500',
            'postal_code' => 'nullable|string|max:20',
            'latitude'    => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
        ]);

        try {
            ProvincialMinistryOfEducationOffice::findOrFail($this->id)->update([
                'address'     => $this->address,
                'postal_code' => $this->postal_code,
                'latitude'    => $this->latitude,
                'longitude'   => $this->longitude,
            ]);
            session()->flash('success', 'Location updated successfully.');
            return $this->redirect(url()->previous(), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update location: ' . $e->getMessage());
            return $this->redirect(url()->previous(), navigate: true);
        }
    }

    // ── Update Mission & Vision ───────────────────────────────────
    public function updateMissionVision()
    {
        $this->validate([
            'mission' => 'nullable|string|max:1000',
            'vision'  => 'nullable|string|max:1000',
        ]);

        try {
            ProvincialMinistryOfEducationOffice::findOrFail($this->id)->update([
                'mission' => $this->mission,
                'vision'  => $this->vision,
            ]);
            session()->flash('success', 'Mission & Vision updated successfully.');
            return $this->redirect(url()->previous(), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update Mission & Vision: ' . $e->getMessage());
            return $this->redirect(url()->previous(), navigate: true);
        }
    }

    public function render()
    {
        $provincialEducationMinistry = ProvincialMinistryOfEducationOffice::findOrFail($this->id);
        return view('livewire.offices.pmoe.profile.pmoe-profile', compact('provincialEducationMinistry'));
    }
}
