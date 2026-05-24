<?php

namespace App\Livewire\MainTables;

use App\Models\Service;
use Livewire\Component;
use Illuminate\Validation\Rule;

class MainTablesServices extends Component
{
    public $showModelNewService = false; // control modal visibility
    public $showModelEditService = false; // control modal visibility

    public $service_id, $service_name, $description, $rank;
    public $update_service_id, $update_service_name, $update_description, $update_rank;

    public $editServiceId;

    public function editService($id)
    {
        $service = Service::findOrFail($id);

        $this->editServiceId = $service->id;
        $this->update_service_id = $service->service_id;
        $this->update_service_name = $service->service_name;
        $this->update_description = $service->description;
        $this->update_rank = $service->rank;

        $this->showModelEditService = true; // ensure modal is open
    }

    public function updateService()
    {
        $this->validate([
            'update_service_id' => [
                'required',
                'string',
                'regex:/^[SER]{3}\d{3}$/', // Example: SER123
                Rule::unique('services', 'service_id')->ignore($this->editServiceId),
            ],
            'update_service_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('services', 'service_name')->ignore($this->editServiceId),
            ],
            'update_rank' => 'required|integer|min:0',
            'update_description' => 'nullable|string|max:500',
        ]);

        Service::where('id', $this->editServiceId)->update([
            'service_id' => $this->update_service_id,
            'service_name' => $this->update_service_name,
            'rank' => $this->update_rank,
            'description' => $this->update_description,
        ]);

        $this->showModelEditService = false;

        session()->flash('message', '✅ Service updated successfully!');

        $this->reset(['update_service_id', 'update_service_name', 'update_rank', 'update_description', 'editServiceId']);
    }


    protected function rules()
    {
        if ($this->editServiceId) {
            // ✅ Editing existing record
            return [
                'update_service_id' => [
                    'required',
                    'string',
                    'regex:/^[SER]{3}\d{3}$/',
                    Rule::unique('services', 'service_id')->ignore($this->editServiceId),
                ],
                'update_service_name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('services', 'service_name')->ignore($this->editServiceId),
                ],
                'update_rank' => 'required|integer|min:0',
                'update_description' => 'nullable|string|max:500',
            ];
        }

        return [
            'service_id' => [
                'required',
                'string',
                'regex:/^[SER]{3}\d{3}$/', // Example: SER123
                'unique:services,service_id'
            ],
            'service_name' => 'required|string|max:255|unique:services,service_name',
            'rank' => 'required|integer|min:0',
            'description' => 'nullable|string|max:500',
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // 🔹 Submit form
    public function addNewService()
    {
        $validated = $this->validate();

        Service::create($validated);

        session()->flash('message', '✅ New Service added successfully!');
        // ✅ Close the modal
        $this->showModelNewService = false;

        $this->reset(['service_id', 'service_name', 'rank', 'description']);
    }

    public function deleteService($id)
    {
        $service = Service::find($id);

        if ($service) {
            $service->delete();
            session()->flash('message', 'Service deleted successfully!');
        } else {
            session()->flash('message', 'Authority not found!');
        }
    }

    public function toggleStatus($id)
    {
        $service = Service::find($id);

        if ($service) {
            // Toggle between 1 and 0
            $service->active_status = $service->active_status == '1' ? '0' : '1';
            $service->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $service->active_status == '1'
                    ? 'Service activated successfully!'
                    : 'Service deactivated successfully!',
            ]);
        }
    }

    public function render()
    {
        $services = Service::orderBy('service_id')->paginate(50);
        return view('livewire.main-tables.main-tables-services', compact('services'));
    }
}
