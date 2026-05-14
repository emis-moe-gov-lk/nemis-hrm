<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use App\Models\DistrictsList;
use App\Models\ProvincesList;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MainTablesDistrict extends Component
{
    public $showModelNewDistrict = false;
    public $provinceOption = [];
    public $districtId, $provinceId, $districtOrder, $districtName;

    protected function rules()
    {

        if($this->editDistrictId){
            return [
                'updateDistrictId' => [
                    'required',
                    'string',
                    'regex:/^DIS\d{3}$/', // Matches CT followed by 5 digits (DIS999)
                    'max:6',
                    Rule::unique('districts_lists', 'district_id')->ignore($this->editDistrictId),

                ],
                'updateProvinceId' => 'required|string|max:10',
                'updateDistrictOrder' => 'required|integer|max:255',
                'updateDistrictName' => 'required|string|max:255',
            ];
        }

        return [
            'districtId' => [
                'required',
                'string',
                'regex:/^DIS\d{3}$/', // Matches CT followed by 5 digits (DIS999)
                'max:6',
                'unique:districts_lists,district_id',
            ],
            'provinceId' => 'required|string|max:10',
            'districtOrder' => 'required|integer|max:255',
            'districtName' => 'required|string|max:255',
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // 🔹 Submit form
    public function addNewDistrict()
    {
        $validated = $this->validate();

        try{
            DistrictsList::create([
                'district_id' => $this->districtId,
                'district_code' => $this->districtOrder,
                'province_id' => $this->provinceId,
                'district_name' => $this->districtName,

            ]);

            session()->flash('message', '✅ New District added successfully!');

            // ✅ Close modal
            $this->showModelNewDistrict = false;

            // ✅ Reset form fields (but keep modal control variable)
            $this->reset(['districtId', 'districtOrder', 'provinceId', 'districtName']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to save District data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('District creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }

    }

    public function toggleStatus($id)
    {
        $staDistrict = DistrictsList::find($id);

        if ($staDistrict) {
            // Toggle between 1 and 0
            $staDistrict->active_status = $staDistrict->active_status == '1' ? '0' : '1';
            $staDistrict->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $staDistrict->active_status == '1'
                    ? 'District activated successfully!'
                    : 'District deactivated successfully!',
            ]);
        }
    }

    public function deleteDistrict($id)
    {
        $delDistrict = DistrictsList::find($id);

        if ($delDistrict) {
            $delDistrict->delete();
            session()->flash('message', 'District deleted successfully!');
        } else {
            session()->flash('message', 'District not found!');
        }
    }

    public $showModelEditDistrict = false;
    public $updateDistrictId, $updateProvinceId, $updateDistrictOrder, $updateDistrictName;
    public $editDistrictId;

    public function editDistrict($id)
    {
        $district = DistrictsList::findOrFail($id);

        $this->editDistrictId = $district->id;

        $this->updateDistrictId = $district->district_id;
        $this->updateProvinceId = $district->province_id;
        $this->updateDistrictOrder = $district->district_code;
        $this->updateDistrictName = $district->district_name;

        $this->showModelEditDistrict = true; // ensure modal is open
    }

    public function updateDistrict()
    {
        try{

            $this->validate([
                'updateDistrictId' => [
                    'required',
                    'string',
                    'regex:/^DIS\d{3}$/', // Matches CT followed by 5 digits (DIS999)
                    'max:6',
                    Rule::unique('districts_lists', 'district_id')->ignore($this->editDistrictId),

                ],
                'updateProvinceId' => 'required|string|max:10',
                'updateDistrictOrder' => 'required|integer|max:255',
                'updateDistrictName' => 'required|string|max:255',
            ]);

            DistrictsList::where('id', $this->editDistrictId)->update([
                'district_id' => $this->updateDistrictId,
                'province_id' => $this->updateProvinceId,
                'district_code' => $this->updateDistrictOrder,
                'district_name' => $this->updateDistrictName,
            ]);


            $this->showModelEditDistrict = false;

            session()->flash('message', '✅ District information updated successfully!');

            $this->reset(['updateDistrictId', 'updateProvinceId', 'updateDistrictOrder', 'updateDistrictName', 'editDistrictId']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to update District data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('District update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }
    }


    public function mount(){
        $this->provinceOption = ProvincesList::orderBy('province_code', 'asc')->active()->get();
    }

    public function render()
    {
        $districtList = DistrictsList::orderBy('district_code')->paginate(50);
        return view('livewire.main-tables.main-tables-district', compact('districtList'));
    }
}
