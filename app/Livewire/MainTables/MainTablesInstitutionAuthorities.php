<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use Illuminate\Validation\Rule;
use App\Models\InstitutionAuthority;

class MainTablesInstitutionAuthorities extends Component
{
    public $showModelNewInsAuthority = false; // control modal visibility
    public $showModelEditInsAuthority = false; // control modal visibility

    public $authority_id, $authority_name, $description;
    public $update_authority_id, $update_authority_name, $update_description;

    public $editAuthorityId;

    public function editInsAuthority($id)
    {
        $insauthority = InstitutionAuthority::findOrFail($id);

        $this->editAuthorityId = $insauthority->id;
        $this->update_authority_id = $insauthority->authority_id;
        $this->update_authority_name = $insauthority->authority_name;
        $this->update_description = $insauthority->description;

        $this->showModelEditInsAuthority = true; // ensure modal is open
    }

    public function updateInsAuthority()
    {
        $this->validate([
            'update_authority_id' => [
                'required',
                'string',
                'regex:/^[AUID]{4}\d{2}$/', // Example: AUID12
                Rule::unique('institution_authorities', 'authority_id')->ignore($this->editAuthorityId),
            ],
            'update_authority_name' => [
                'required',
                'string',
                'max:255',
            ],
            'update_description' => 'nullable|string|max:500',
        ]);

        InstitutionAuthority::where('id', $this->editAuthorityId)->update([
            'authority_id' => $this->update_authority_id,
            'authority_name' => $this->update_authority_name,
            'description' => $this->update_description,
        ]);

        $this->showModelEditInsAuthority = false;

        session()->flash('message', '✅ Institution Authority updated successfully!');

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
                    'regex:/^[AUID]{4}\d{2}$/',
                    Rule::unique('institution_authorities', 'authority_id')->ignore($this->editAuthorityId),
                ],
                'update_authority_name' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'update_description' => 'nullable|string|max:500',
            ];
        }

        return [
            'authority_id' => [
                'required',
                'string',
                'regex:/^[AUID]{4}\d{2}$/', // Example: AUID12
                'unique:institution_authorities,authority_id'
            ],
            'authority_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // 🔹 Submit form
    public function addNewInsAuthority()
    {
        $validated = $this->validate();

        InstitutionAuthority::create($validated);

        session()->flash('message', '✅ New Institution Authority added successfully!');
        // ✅ Close the modal
        $this->showModelNewInsAuthority = false;

        $this->reset(['authority_id', 'authority_name', 'description']);
    }

    public function deleteInsAuthority($id)
    {
        $insauthority = InstitutionAuthority::find($id);

        if ($insauthority) {
            $insauthority->delete();
            session()->flash('message', 'Institution Authority deleted successfully!');
        } else {
            session()->flash('message', 'Authority not found!');
        }
    }

    public function toggleStatus($id)
    {
        $insauthority = InstitutionAuthority::find($id);

        if ($insauthority) {
            // Toggle between 1 and 0
            $insauthority->active_status = $insauthority->active_status == '1' ? '0' : '1';
            $insauthority->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $insauthority->active_status == '1'
                    ? 'Institution Authority activated successfully!'
                    : 'Institution Authority deactivated successfully!',
            ]);
        }
    }

    public function render()
    {
        $insauthorities = InstitutionAuthority::orderBy('authority_id', 'asc')->paginate(50);
        return view('livewire.main-tables.main-tables-institution-authorities', compact('insauthorities'));
    }
}
