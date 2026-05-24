<?php

namespace App\Livewire\Offices\Zeo\Profile;

use Livewire\Component;
use App\Models\ZonalEducationOffice;

class ZeoProfile extends Component
{
    public $id;

    // ── Contact Details ────────────────────────────────────────────
    public ?string $email    = null;
    public ?string $phone    = null;

    // ── Location ──────────────────────────────────────────────────
    public ?string $address   = null;
    public ?string $latitude  = null;
    public ?string $longitude = null;

    public function mount()
    {
        $zeo = ZonalEducationOffice::findOrFail($this->id);

        $this->email     = $zeo->email;
        $this->phone     = $zeo->phone;
        $this->address   = $zeo->address;
        $this->latitude  = $zeo->latitude;
        $this->longitude = $zeo->longitude;
    }

    // ── Update Contact Details ────────────────────────────────────
    public function updateContactDetails()
    {
        $this->validate([
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        try {
            ZonalEducationOffice::findOrFail($this->id)->update([
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
            'address'   => 'nullable|string|max:500',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        try {
            ZonalEducationOffice::findOrFail($this->id)->update([
                'address'   => $this->address,
                'latitude'  => $this->latitude,
                'longitude' => $this->longitude,
            ]);
            session()->flash('success', 'Location updated successfully.');
            return $this->redirect(url()->previous(), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update location: ' . $e->getMessage());
            return $this->redirect(url()->previous(), navigate: true);
        }
    }

    public function render()
    {
        $zonalEducationOffice = ZonalEducationOffice::findOrFail($this->id);
        return view('livewire.offices.zeo.profile.zeo-profile', compact('zonalEducationOffice'));
    }
}
