<?php

namespace App\Livewire\Roles;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleCreate extends Component
{
    public $role_name;
    public $selectedPermissions;

    public $allPermissions = [];

    public function mount(){
        //$this->allPermissions = Permission::all();
        $this->allPermissions = Permission::orderBy('name', 'asc')->get();

    }

    public function render()
    {
        return view('livewire.roles.role-create');
    }

    public function createRole(){
        $this->validate([
            'role_name' => 'required|unique:roles,name',
            'selectedPermissions' => 'required|array|min:1',
        ]);

        $role = Role::create(['name' => $this->role_name]);
        $role->syncPermissions($this->selectedPermissions);

        //session()->flash('message', 'Role created successfully.');

        return redirect()->route('roles.index')->with('message', 'Role created successfully.');
    }
}
