<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use App\Models\PoliceStation;
use Illuminate\Validation\Rule;
use Livewire\WithPagination;

class MainTablesPoliceStations extends Component
{
    use WithPagination;
    public $showModelNewPoliceStation = false; // control modal visibility
    public $showModelEditPoliceStation = false; // control modal visibility

    public $police_station_id, $police_station_name, $address, $postal_code, $phone, $email;
    public $update_police_station_id, $update_police_station_name, $update_address, $update_postal_code, $update_phone, $update_email;

    public $editPoliceStationId;

    public function editPoliceStation($id)
    {
        $police_station = PoliceStation::findOrFail($id);

        $this->editPoliceStationId = $police_station->id;
        $this->update_police_station_id = $police_station->police_station_id;
        $this->update_police_station_name = $police_station->police_station_name;
        $this->update_address = $police_station->address;
        $this->update_postal_code = $police_station->postal_code;
        $this->update_phone = $police_station->phone;
        $this->update_email = $police_station->email;

        $this->showModelEditPoliceStation = true; // ensure modal is open
    }

    public function updatePoliceStation()
    {
        $this->validate([
            'update_police_station_id' => [
                'required',
                'string',
                'regex:/^[PSID]{4}\d{3}$/', // Example: PSID123
                Rule::unique('police_stations', 'police_station_id')->ignore($this->editPoliceStationId),
            ],
            'update_police_station_name' => [
                'required',
                'string',
                'max:255',
            ],
            'update_address' => 'required|string|max:500',
            'update_postal_code' => 'required|numeric|max:99999',
            'update_phone' => 'required|string|regex:/^\d{10}$/',
            'update_email' => 'required|email',
        ]);

        $this->resetPage();

        PoliceStation::where('id', $this->editPoliceStationId)->update([
            'police_station_id' => $this->update_police_station_id,
            'police_station_name' => $this->update_police_station_name,
            'address' => $this->update_address,
            'postal_code' => $this->update_postal_code,
            'phone' => $this->update_phone,
            'email' => $this->update_email,
        ]);

        $this->showModelEditPoliceStation = false;

        session()->flash('message', '✅ Police Station updated successfully!');

        $this->reset(['update_police_station_id', 'update_police_station_name', 'update_address', 'update_postal_code', 'update_phone', 'update_email', 'editPoliceStationId']);
    }


    protected function rules()
    {
        if ($this->editPoliceStationId) {
            // ✅ Editing existing record
            return [
                'update_police_station_id' => [
                    'required',
                    'string',
                    'regex:/^[PSID]{4}\d{3}$/',
                    Rule::unique('police_stations', 'police_station_id')->ignore($this->editPoliceStationId),
                ],
                'update_police_station_name' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'update_address' => 'required|string|max:500',
                'update_postal_code' => 'required|numeric|max:99999',
                'update_phone' => 'required|string|regex:/^\d{10}$/',
                'update_email' => 'required|email',
            ];
        }

        return [
            'police_station_id' => [
                'required',
                'string',
                'regex:/^[PSID]{4}\d{3}$/', // Example: PSID123
                'unique:police_stations,police_station_id'
            ],
            'police_station_name' => 'required|string|max:255|unique:police_stations,police_station_name',
            'address' => 'required|string|max:500',
            'postal_code' => 'required|numeric|max:99999',
            'phone' => 'required|string|regex:/^\d{10}$/',
            'email' => 'required|email',
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
        $this->resetPage();
    }

    // 🔹 Submit form
    public function addNewPoliceStation()
    {
        $validated = $this->validate();
        $this->resetPage();

        PoliceStation::create($validated);

        session()->flash('message', '✅ New Police Station added successfully!');
        // ✅ Close the modal
        $this->showModelNewPoliceStation = false;

        $this->reset(['police_station_id', 'police_station_name', 'address', 'postal_code', 'phone', 'email']);
    }

    public function deletePoliceStation($id)
    {
        $police_station = PoliceStation::find($id);

        if ($police_station) {
            $police_station->delete();
            session()->flash('message', 'Police Station deleted successfully!');
        } else {
            session()->flash('message', 'Police Station not found!');
        }
    }

    public function toggleStatus($id)
    {
        $police_station = PoliceStation::find($id);

        if ($police_station) {
            // Toggle between 1 and 0
            $police_station->active_status = $police_station->active_status == '1' ? '0' : '1';
            $police_station->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $police_station->active_status == '1'
                    ? 'Police Station activated successfully!'
                    : 'Police Station deactivated successfully!',
            ]);
        }
    }

    public function render()
    {
        $police_stations = PoliceStation::orderBy('police_station_id')->paginate(50);
        return view('livewire.main-tables.main-tables-police-stations', compact('police_stations'));
    }
}
