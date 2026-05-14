<?php

namespace App\Livewire\MainTables;

use App\Models\Service;
use Livewire\Component;
use App\Models\ServiceRank;
use Illuminate\Validation\Rule;

class MainTablesServiceRanks extends Component
{
    public $showModelNewServiceRank = false; // control modal visibility
    public $showModelEditServiceRank = false; // control modal visibility

    public $rank_id, $service_id, $rank_name, $description;
    public $update_service_rank_id, $update_service_id, $update_service_rank_name, $update_description;

    public $serviceOption = [];

    public $editServiceRankId;

    public function editServiceRank($id)
    {
        $service_rank = ServiceRank::findOrFail($id);

        $this->editServiceRankId = $service_rank->id;
        $this->update_service_id = $service_rank->service_id;
        $this->update_service_rank_id = $service_rank->rank_id;
        $this->update_service_rank_name = $service_rank->rank_name;
        $this->update_description = $service_rank->description;

        $this->showModelEditServiceRank = true; // ensure modal is open
    }

    public function updateServiceRank()
    {
        $this->validate([
            'update_service_rank_id' => [
                'required',
                'string',
                'regex:/^[RANK]{4}\d{3}$/', // Example: RANK123
                Rule::unique('service_ranks', 'rank_id')->ignore($this->editServiceRankId),
            ],
            'update_service_id' => [
                'required',
                'string',
                'regex:/^[SER]{3}\d{3}$/', // Example: SER123
            ],
            'update_service_rank_name' => [
                'required',
                'string',
                'max:255',
            ],
            'update_description' => 'nullable|string|max:500',
        ]);

        ServiceRank::where('id', $this->editServiceRankId)->update([
            'rank_id' => $this->update_service_rank_id,
            'service_id' => $this->update_service_id,
            'rank_name' => $this->update_service_rank_name,
            'description' => $this->update_description,
        ]);

        $this->showModelEditServiceRank = false;

        session()->flash('message', '✅ Service updated successfully!');

        $this->reset(['update_service_rank_id', 'update_service_id', 'update_service_rank_name', 'update_description', 'editServiceRankId']);
    }


    protected function rules()
    {
        if ($this->editServiceRankId) {
            // ✅ Editing existing record
            return [
                'update_service_rank_id' => [
                    'required',
                    'string',
                    'regex:/^RANK\d{3}$/', // Example: RANK123
                    Rule::unique('service_ranks', 'rank_id')->ignore($this->editServiceRankId),
                ],
                'update_service_id' => [
                    'required',
                    'string',
                    'regex:/^SER\d{3}$/', // Example: SER123
                ],
                'update_service_rank_name' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'update_description' => 'nullable|string|max:500',
            ];
        }

        return [
            'rank_id' => 'required|string|regex:/^RANK\d{3}$/|unique:service_ranks,rank_id',
            'service_id' => 'required|string|regex:/^SER\d{3}$/',
            'rank_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // 🔹 Submit form
    public function addNewServiceRank()
    {
        $validated = $this->validate();
        
        ServiceRank::create($validated);

        session()->flash('message', '✅ New Service Rank added successfully!');

        // ✅ Close the modal
        $this->showModelNewServiceRank = false;

        $this->reset(['rank_id', 'service_id', 'rank_name', 'description']);
    }

    public function deleteServiceRank($id)
    {
        $service_rank = ServiceRank::find($id);

        if ($service_rank) {
            $service_rank->delete();
            session()->flash('message', 'Service Rank deleted successfully!');
        } else {
            session()->flash('message', 'Service Rank not found!');
        }
    }

    public function toggleStatus($id)
    {
        $service_rank = ServiceRank::find($id);

        if ($service_rank) {
            // Toggle between 1 and 0
            $service_rank->active_status = $service_rank->active_status == '1' ? '0' : '1';
            $service_rank->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $service_rank->active_status == '1'
                    ? 'Service Rank activated successfully!'
                    : 'Service Rank deactivated successfully!',
            ]);
        }
    }

    public function mount(){
        $this->serviceOption = Service::orderBy('service_id', 'asc')->active()->get();
    }
    
    public function render()
    {
        $service_ranks = ServiceRank::orderBy('service_id')->paginate(50);
        return view('livewire.main-tables.main-tables-service-ranks', compact('service_ranks'));
    }
}
