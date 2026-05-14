<?php

namespace App\Livewire\Roles;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use Livewire\Component;

class RoleEdit extends Component
{
    public $role;
    public $role_name;
    public $selectedPermissions;

    public $allPermissions = [];

    public function mount($id){
        $this->role = Role::find($id);
        $this->allPermissions = Permission::get();
        $this->role_name = $this->role->name;
        $this->selectedPermissions = $this->role->permissions->pluck('name')->toArray();
    }

    public function render()
    {
        return view('livewire.roles.role-edit');
    }

    public function updateRole(){
        $this->validate([
            'role_name' => 'required|unique:roles,name,'.$this->role->id,
            'selectedPermissions' => 'required|array|min:1',
        ]);

        $this->role->name = $this->role_name;
        $this->role->save();
        $this->role->syncPermissions($this->selectedPermissions);

        //session()->flash('message', 'Role created successfully.');
        $this->dispatch('toast', message: 'Record saved successfully!', type: 'success');

        return redirect()->route('roles.index')->with('message', 'Role updated successfully.');
    }
}
