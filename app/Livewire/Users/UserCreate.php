<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Models\People;
use Livewire\Component;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Validate;
use App\Rules\UniqueHashedNicUser;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class UserCreate extends Component
{
    public $name = '';
    public $email = '';
    public $nic = '';
    public $contact = '';
    public $roles = [];
    public $password = '';
    public string $password_confirmation = '';

    public $allRole;


    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'nic' => ['required', 'string', 'regex:/^(\d{9}[vVxX]|\d{12})$/', new UniqueHashedNicUser()],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'contact' => ['required', 'regex:/^[0-9]{10}$/', 'unique:users'],
            'roles' => 'required',
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ];
    }

    // -------------------------
    // Live Validation on Field Update
    // -------------------------
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount()
    {
        $this->allRole = Role::all();
    }

    public function render()
    {
        return view('livewire.users.user-create');
    }

    public function createUser()
    {
        $validated = $this->validate();

        // Try to find person by NIC hash
        $nicHash = hash('sha256', $validated['nic']);
        $person = People::where('nic_hash', $nicHash)->first();

        // If no matching person found, stop execution
        if (!$person) {
            session()->flash('error', 'This NIC does not exist in the People database.');
            return; // Stop the function
        }

        // Hash password
        $validated['password'] = Hash::make($validated['password']);

        // Create or update user
        $user = User::updateOrCreate(
            ['nic_hash' => $person->nic_hash],
            [
                'nic'       => $validated['nic'],
                'nic_hash'  => $person->nic_hash,
                'people_id' => $person->people_id,
                'name'      => $person->name_with_initials,
                'email'     => $validated['email'],
                'contact'   => $validated['contact'],
                'password'  => $validated['password'],
            ]
        );

        $user->syncRoles($this->roles);

        session()->flash('message', 'User created successfully.');
        return redirect()->route('users.index');
    }
}
