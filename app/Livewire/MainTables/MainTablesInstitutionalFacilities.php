<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use Illuminate\Validation\Rule;
use App\Models\InstitutionalFacility;

class MainTablesInstitutionalFacilities extends Component
{
    public $showModelNewInstitutionalFacility = false; // control modal visibility
    public $showModelEditInstitutionalFacility = false; // control modal visibility

    public $facilities_id, $facilities_name, $description;
    public $update_facilities_id, $update_facilities_name, $update_description;

    public $editInstitutionalFacilityId;

    public function editInstitutionalFacility($id)
    {
        $institutional_facility = InstitutionalFacility::findOrFail($id);

        $this->editInstitutionalFacilityId = $institutional_facility->id;
        $this->update_facilities_id = $institutional_facility->facilities_id;
        $this->update_facilities_name = $institutional_facility->name;
        $this->update_description = $institutional_facility->description;

        $this->showModelEditInstitutionalFacility = true; // ensure modal is open
    }

    public function updateInstitutionalFacility()
    {
        $this->validate([
            'update_facilities_id' => [
                'required',
                'string',
                'regex:/^[FAC]{3}\d{3}$/', // Example: ITID12
                Rule::unique('institutional_facilities', 'facilities_id')->ignore($this->editInstitutionalFacilityId),
            ],
            'update_facilities_name' => [
                'required',
                'string',
                'max:255',
            ],
            'update_description' => 'nullable|string|max:500',
        ]);

        InstitutionalFacility::where('id', $this->editInstitutionalFacilityId)->update([
            'facilities_id' => $this->update_facilities_id,
            'name' => $this->update_facilities_name,
            'description' => $this->update_description,
        ]);

        $this->showModelEditInstitutionalFacility = false;

        session()->flash('message', '✅ Institutional Facility updated successfully!');

        $this->reset(['update_facilities_id', 'update_facilities_name', 'update_description', 'editInstitutionalFacilityId']);
    }


    protected function rules()
    {
        if ($this->editInstitutionalFacilityId) {
            // ✅ Editing existing record
            return [
                'update_facilities_id' => [
                    'required',
                    'string',
                    'regex:/^[FAC]{3}\d{3}$/',
                    Rule::unique('institutional_facilities', 'facilities_id')->ignore($this->editInstitutionalFacilityId),
                ],
                'update_facilities_name' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'update_description' => 'nullable|string|max:500',
            ];
        }

        return [
            'facilities_id' => [
                'required',
                'string',
                'regex:/^[FAC]{3}\d{3}$/', // Example: AUID12
                'unique:institutional_facilities,facilities_id'
            ],
            'facilities_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // 🔹 Submit form
    public function addNewInstitutionalFacility()
    {
        $validated = $this->validate();

        InstitutionalFacility::create(
            [
                'facilities_id' => $validated['facilities_id'],
                'name' => $validated['facilities_name'],
                'description' => $validated['description'],
            ]
        );

        session()->flash('message', '✅ New Institutional Facility added successfully!');
        // ✅ Close the modal
        $this->showModelNewInstitutionalFacility = false;

        $this->reset(['facilities_id', 'facilities_name', 'description']);
    }

    public function deleteInstitutionalFacility($id)
    {
        $institutional_facility = InstitutionalFacility::find($id);

        if ($institutional_facility) {
            $institutional_facility->delete();
            session()->flash('message', 'Institutional Facility deleted successfully!');
        } else {
            session()->flash('message', 'Institutional Facility not found!');
        }
    }

    public function toggleStatus($id)
    {
        $institutional_facility = InstitutionalFacility::find($id);

        if ($institutional_facility) {
            // Toggle between 1 and 0
            $institutional_facility->active_status = $institutional_facility->active_status == '1' ? '0' : '1';
            $institutional_facility->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $institutional_facility->active_status == '1'
                    ? 'Institutional Facility activated successfully!'
                    : 'Institutional Facility deactivated successfully!',
            ]);
        }
    }

    public function render()
    {
        $institutional_facilities = InstitutionalFacility::orderBy('facilities_id')->paginate(50);
        return view('livewire.main-tables.main-tables-institutional-facilities', compact('institutional_facilities'));
    }
}
