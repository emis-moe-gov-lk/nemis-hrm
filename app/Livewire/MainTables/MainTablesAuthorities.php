<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use App\Models\Authority;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class MainTablesAuthorities extends Component
{
    use WithPagination;

    public $showModelNewAuthority = false; // control modal visibility
    public $showModelEditAuthority = false; // control modal visibility

    public $authority_id, $authority_name, $description;
    public $update_authority_id, $update_authority_name, $update_description;

    public $editAuthorityId;

    public function editAuthority($id)
    {
        $authority = Authority::findOrFail($id);

        $this->editAuthorityId = $authority->id;
        $this->update_authority_id = $authority->authority_id;
        $this->update_authority_name = $authority->authority_name;
        $this->update_description = $authority->description;

        $this->showModelEditAuthority = true; // ensure modal is open
    }

    public function updateAuthority()
    {
        $this->validate([
            'update_authority_id' => [
                'required',
                'string',
                'regex:/^[A-Za-z]{4}\d{2}$/', // Example: AUID12
                Rule::unique('authorities', 'authority_id')->ignore($this->editAuthorityId),
            ],
            'update_authority_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('authorities', 'authority_name')->ignore($this->editAuthorityId),
            ],
            'update_description' => 'nullable|string|max:500',
        ]);

        $this->resetPage();

        Authority::where('id', $this->editAuthorityId)->update([
            'authority_id' => $this->update_authority_id,
            'authority_name' => $this->update_authority_name,
            'description' => $this->update_description,
        ]);

        $this->showModelEditAuthority = false;

        session()->flash('message', '✅ Authority updated successfully!');

        $this->reset(['update_authority_id', 'update_authority_name', 'update_description', 'editAuthorityId']);
    }


    protected function rules()
    {
        if ($this->editAuthorityId) {
            // ✅ Editing existing record
            return [
                'update_authority_id' => [
                    'required',
                    'string',
                    'regex:/^[A-Za-z]{4}\d{2}$/',
                    Rule::unique('authorities', 'authority_id')->ignore($this->editAuthorityId),
                ],
                'update_authority_name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('authorities', 'authority_name')->ignore($this->editAuthorityId),
                ],
                'update_description' => 'nullable|string|max:500',
            ];
        }

        return [
            'authority_id' => [
                'required',
                'string',
                'regex:/^[A-Za-z]{4}\d{2}$/', // Example: AUID12
                'unique:authorities,authority_id'
            ],
            'authority_name' => 'required|string|max:255|unique:authorities,authority_name',
            'description' => 'nullable|string|max:500',
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
        $this->resetPage();
    }

    // 🔹 Submit form
    public function addNewAuthority()
    {
        $validated = $this->validate();
        $this->resetPage();

        Authority::create($validated);

        session()->flash('message', '✅ New Authority added successfully!');
        // ✅ Close the modal
        $this->showModelNewAuthority = false;

        $this->reset(['authority_id', 'authority_name', 'description']);
    }

    public function deleteAuthority($id)
    {
        $authority = Authority::find($id);

        if ($authority) {
            $authority->delete();
            session()->flash('message', 'Authority deleted successfully!');
        } else {
            session()->flash('message', 'Authority not found!');
        }
    }

    public function toggleStatus($id)
    {
        $authority = Authority::find($id);

        if ($authority) {
            // Toggle between 1 and 0
            $authority->active_status = $authority->active_status == '1' ? '0' : '1';
            $authority->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $authority->active_status == '1'
                    ? 'Authority activated successfully!'
                    : 'Authority deactivated successfully!',
            ]);
        }
    }

    public function render()
    {
        $authorities = Authority::orderBy('authority_id')->paginate(50);
        return view('livewire.main-tables.main-tables-authorities', compact('authorities'));
    }
}
