<?php

namespace App\Livewire\Peoples\Profile;

use App\Models\People;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class PeopleProfile extends Component
{
    public People $person;

    public bool $canEdit = false;

    public bool $showDeleteModal = false;

    public string $deletePassword = '';

    public function mount(int|string $id): void
    {
        $this->person = People::query()
            ->with([
                'appointment.service',
                'appointment.rank',
                'appointment.position',
                'appointment.workplace',
                'currentAppointment.service',
                'currentAppointment.workplace',
                'currentAppointment.rank',
                'currentAppointment.position',
                'familiesAsHusband.memberB',
                'familiesAsHusband.children.gender',
                'familiesAsWife.memberA',
                'familiesAsWife.children.gender',
            ])
            ->findOrFail($id);

        $this->canEdit = ! $this->person->myAppointments()->exists()
            && ! $this->person->currentAppointment()->exists()
            && ! $this->person->familiesAsHusband()->exists()
            && ! $this->person->familiesAsWife()->exists();
    }

    public function confirmDelete(): void
    {
        // Double-check before showing modal
        if ($this->person->myAppointments()->exists() || $this->person->currentAppointment()->exists()) {
            $this->addError('delete', 'මෙම පුද්ගලයා සතුව රැකියා ලේඛනයක් ඇති නිසා delete කළ නොහැක.');
            return;
        }

        if ($this->person->familiesAsHusband()->exists() || $this->person->familiesAsWife()->exists()) {
            $this->addError('delete', 'මෙම පුද්ගලයා පවුල් ලේඛනයකට සම්බන්ධ ඇති නිසා delete කළ නොහැක.');
            return;
        }

        $this->deletePassword = '';
        $this->showDeleteModal = true;
    }

    public function deletePerson(): void
    {
        // Validate password field is filled
        $this->validate([
            'deletePassword' => ['required'],
        ], [
            'deletePassword.required' => 'Password is required to confirm deletion.',
        ]);

        // Verify the logged-in user's password
        if (! Hash::check($this->deletePassword, Auth::user()->password)) {
            $this->addError('deletePassword', 'The password you entered is incorrect.');
            $this->deletePassword = '';
            return;
        }

        // Final safety check before deleting
        if ($this->person->myAppointments()->exists() || $this->person->currentAppointment()->exists()) {
            $this->showDeleteModal = false;
            $this->deletePassword = '';
            $this->addError('delete', 'මෙම පුද්ගලයා සතුව රැකියා ලේඛනයක් ඇති නිසා delete කළ නොහැක.');
            return;
        }

        if ($this->person->familiesAsHusband()->exists() || $this->person->familiesAsWife()->exists()) {
            $this->showDeleteModal = false;
            $this->deletePassword = '';
            $this->addError('delete', 'මෙම පුද්ගලයා පවුල් ලේඛනයකට සම්බන්ධ ඇති නිසා delete කළ නොහැක.');
            return;
        }

        $this->person->delete();

        $this->deletePassword = '';

        session()->flash('success', 'Person deleted successfully.');

        $this->redirect(route('peoples.list'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.peoples.profile.people-profile');
    }
}

