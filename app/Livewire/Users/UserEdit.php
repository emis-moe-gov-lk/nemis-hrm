<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class UserEdit extends Component
{
    public $name;
    public $email;
    public $nic;
    public $contact;
    public $roles = [];

    public $allRole;
    public $user;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user->id),
            ],

            'nic' => [
                'required',
                'string',
                'regex:/^(\d{9}[vVxX]|\d{12})$/',
                function ($attribute, $value, $fail) {
                    $hashedNic = hash('sha256', strtoupper($value));
                    $exists = User::where('nic_hash', $hashedNic)
                        ->when($this->user, fn($q) => $q->where('id', '!=', $this->user->id))
                        ->exists();

                    if ($exists) {
                        $fail('This NIC is already registered.');
                    }
                },
            ],

            'contact' => [
                'required',
                'string',
                'max:10',
                Rule::unique('users', 'contact')->ignore($this->user->id),
            ],

            'roles' => 'required|array|min:1',
        ];
    }

    public function mount($id)
    {
        $this->user = User::findOrFail($id);

        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->nic = $this->user->nic;
        $this->contact = $this->user->contact;
        $this->allRole = Role::all();
        $this->roles = $this->user->roles()->pluck('name')->toArray();
    }

    public function render()
    {
        return view('livewire.users.user-edit');
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function userUpdate()
    {
        $this->validate();

        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
            'nic' => $this->nic,
            'contact' => $this->contact,
        ]);

        $this->user->syncRoles($this->roles);

        //$this->redirectRoute('users.index', navigate: true);
        return redirect()->route('users.index')->with('message', 'User update successfully.');
    }
}
