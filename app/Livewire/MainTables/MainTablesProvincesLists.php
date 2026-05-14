<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use App\Models\ProvincesList;
use Illuminate\Validation\Rule;

class MainTablesProvincesLists extends Component
{
    public $showModelNewProvince = false; // control modal visibility
    public $showModelEditProvince = false; // control modal visibility

    public $province_id, $province_name, $province_rank;
    public $update_province_id, $update_province_name, $update_province_rank;

    public $editProvinceId;

    public function editProvince($id)
    {
        $province = ProvincesList::findOrFail($id);

        $this->editProvinceId = $province->id;
        $this->update_province_id = $province->province_id;
        $this->update_province_name = $province->province_name;
        $this->update_province_rank = $province->province_code;

        $this->showModelEditProvince = true; // ensure modal is open
    }

    public function updateProvince()
    {
        $this->validate([
            'update_province_id' => [
                'required',
                'string',
                'regex:/^[PRO]{3}\d{2}$/', // Example: SER123
                Rule::unique('provinces_lists', 'province_id')->ignore($this->editProvinceId),
            ],
            'update_province_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('provinces_lists', 'province_name')->ignore($this->editProvinceId),
            ],
            'update_province_rank' => 'nullable|numeric|max:255',
        ]);

        ProvincesList::where('id', $this->editProvinceId)->update([
            'province_id' => $this->update_province_id,
            'province_name' => $this->update_province_name,
            'province_code' => $this->update_province_rank,
        ]);

        $this->showModelEditProvince = false;

        session()->flash('message', '✅ Province updated successfully!');

        $this->reset(['update_province_id', 'update_province_name', 'update_province_rank', 'editProvinceId']);
    }


    protected function rules()
    {
        if ($this->editProvinceId) {
            // ✅ Editing existing record
            return [
                'update_province_id' => [
                    'required',
                    'string',
                    'regex:/^[PRO]{3}\d{2}$/',
                    Rule::unique('provinces_lists', 'province_id')->ignore($this->editProvinceId),
                ],
                'update_province_name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('provinces_lists', 'province_name')->ignore($this->editProvinceId),
                ],
                'update_province_rank' => 'nullable|numeric|max:255',
            ];
        }

        return [
            'province_id' => [
                'required',
                'string',
                'regex:/^[PRO]{3}\d{2}$/', // Example: SER123
                'unique:provinces_lists,province_id'
            ],
            'province_name' => 'required|string|max:255|unique:provinces_lists,province_name',
            'province_rank' => 'nullable|numeric|max:255',
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // 🔹 Submit form
    public function addNewProvince()
    {
        $validated = $this->validate();

        ProvincesList::create(
            [
                'province_id' => $validated['province_id'],
                'province_name' => $validated['province_name'],
                'province_code' => $validated['province_rank'],
            ]
        );

        session()->flash('message', '✅ New Province added successfully!');
        // ✅ Close the modal
        $this->showModelNewProvince = false;

        $this->reset(['province_id', 'province_name', 'province_rank']);
    }

    public function deleteProvince($id)
    {
        $province = ProvincesList::find($id);

        if ($province) {
            $province->delete();
            session()->flash('message', 'Province deleted successfully!');
        } else {
            session()->flash('message', 'Province not found!');
        }
    }

    public function toggleStatus($id)
    {
        $province = ProvincesList::find($id);

        if ($province) {
            // Toggle between 1 and 0
            $province->active_status = $province->active_status == '1' ? '0' : '1';
            $province->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $province->active_status == '1'
                    ? 'Province activated successfully!'
                    : 'Province deactivated successfully!',
            ]);
        }
    }

    public function render()
    {
        $provinces = ProvincesList::orderBy('province_code', 'asc')->paginate(50);
        return view('livewire.main-tables.main-tables-provinces-lists', compact('provinces'));
    }
}
