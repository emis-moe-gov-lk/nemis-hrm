<?php

namespace App\Livewire\Offices\Moe\Profile;

use Livewire\Component;
use App\Models\MinistryOfEducationOffice;

class Moeprofile extends Component
{
    public $officeId;

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

    public function mount($id)
    {
        $this->officeId = $id;

        $moe = MinistryOfEducationOffice::findOrFail($this->officeId);

        $this->email       = $moe->email;
        $this->phone       = $moe->phone;
        $this->address     = $moe->address;
        $this->postal_code = $moe->postal_code;
        $this->latitude    = $moe->latitude;
        $this->longitude   = $moe->longitude;
        $this->mission     = $moe->mission;
        $this->vision      = $moe->vision;
    }

    // ── Update Contact Details ────────────────────────────────────
    public function updateContactDetails()
    {
        $this->validate([
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        try {
            MinistryOfEducationOffice::findOrFail($this->officeId)->update([
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
            MinistryOfEducationOffice::findOrFail($this->officeId)->update([
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
            MinistryOfEducationOffice::findOrFail($this->officeId)->update([
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
        $EducationMinistry = MinistryOfEducationOffice::findOrFail($this->officeId);
        return view('livewire.offices.moe.profile.moeprofile', compact('EducationMinistry'));
    }
}
