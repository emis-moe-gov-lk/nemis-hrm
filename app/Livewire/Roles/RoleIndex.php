<?php

namespace App\Livewire\Roles;

use Livewire\Component;
use Spatie\Permission\Models\Role;

class RoleIndex extends Component
{
    public function render()
    {
        $roles = Role::with('permissions')->paginate(10); // 10 items per page
        return view('livewire.roles.role-index', compact('roles'));
    }

    public function delete($id)
    {
        $role = Role::find($id);
        if ($role) {
            $role->delete();
            session()->flash('message', 'Role deleted successfully.');
        } else {
            session()->flash('error', 'Role not found.');
        }
    }
}
