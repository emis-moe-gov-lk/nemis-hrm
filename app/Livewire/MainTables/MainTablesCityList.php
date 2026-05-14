<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use App\Models\Citylists;
use App\Models\DistrictsList;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\WithPagination;

class MainTablesCityList extends Component
{
    use WithPagination;

    public $showModelNewCity = false;
    public $districtOption = [];
    public $cityId, $district, $cityNameEn, $cityNameSi, $cityNameTa, $postalCode, $latitude, $longitude;

    public function mount(){
        $this->districtOption = DistrictsList::orderBy('district_id', 'asc')->active()->get();
    }

    protected function rules()
    {
        if($this->editCityId){
            return [
                'updateCityId' => [
                    'required',
                    'string',
                    'regex:/^CTY\d{6}$/', // Matches CT followed by 5 digits (CTY999999)
                    'max:9',
                    Rule::unique('Citylists', 'city_id')->ignore($this->editCityId),
                ],
                'updateDistrict' => 'required|string|max:10',
                'updateCityNameEn' => 'required|string|max:255',
                'updateCityNameSi' => 'nullable|string|max:255',
                'updateCityNameTa' => 'nullable|string|max:255',
                'updatePostalCode' => 'required|digits:5',
                'updateLatitude' => 'nullable|numeric|between:5.916,9.835',
                'updateLongitude' => 'nullable|numeric|between:79.652,81.881',
            ];
        }

        return [
            'cityId' => [
                'required',
                'string',
                'regex:/^CTY\d{6}$/', // Matches CT followed by 5 digits (CTY999999)
                'max:9',
                'unique:citylists,city_id',
            ],
            'district' => 'required|string|max:10',
            'cityNameEn' => 'required|string|max:255',
            'cityNameSi' => 'nullable|string|max:255',
            'cityNameTa' => 'nullable|string|max:255',
            'postalCode' => 'required|digits:5',
            'latitude' => 'nullable|numeric|between:5.916,9.835',
            'longitude' => 'nullable|numeric|between:79.652,81.881',
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
        $this->resetPage();
    }

    // 🔹 Submit form
    public function addNewCity()
    {
        $validated = $this->validate();
        $this->resetPage();

        try{
            Citylists::create([
                'city_id' => $this->cityId,
                'district_id' => $this->district,
                'city_name_en' => $this->cityNameEn,
                'city_name_si' => $this->cityNameSi,
                'city_name_ta' => $this->cityNameTa,
                'postcode' => $this->postalCode,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ]);

            session()->flash('message', '✅ New City added successfully!');

            // ✅ Close modal
            $this->showModelNewCity = false;

            // ✅ Reset form fields (but keep modal control variable)
            $this->reset(['cityId', 'district', 'cityNameEn', 'cityNameSi', 'cityNameTa', 'postalCode', 'latitude', 'longitude']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to save City data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('City creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }

    }

    public function toggleStatus($id)
    {
        $cityList = Citylists::find($id);

        if ($cityList) {
            // Toggle between 1 and 0
            $cityList->active_status = $cityList->active_status == '1' ? '0' : '1';
            $cityList->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $cityList->active_status == '1'
                    ? 'City activated successfully!'
                    : 'City deactivated successfully!',
            ]);
        }
    }

    public function deleteCity($id)
    {
        $cityList = Citylists::find($id);

        if ($cityList) {
            $cityList->delete();
            session()->flash('message', 'City deleted successfully!');
        } else {
            session()->flash('message', 'City not found!');
        }
    }

    public $showModelEditCityList = false;
    public $updateCityId, $updateDistrict, $updateCityNameEn, $updateCityNameSi, $updateCityNameTa, $updatePostalCode, $updateLatitude, $updateLongitude;
    public $editCityId;


    public function editCityList($id)
    {

        $city = Citylists::findOrFail($id);

        $this->editCityId = $city->id;

        $this->updateCityId = $city->city_id ;
        $this->updateDistrict = $city->district_id ;
        $this->updateCityNameEn = $city->city_name_en;
        $this->updateCityNameSi = $city->city_name_si;
        $this->updateCityNameTa = $city->city_name_ta;
        $this->updatePostalCode = $city->postcode;
        $this->updateLatitude = $city->latitude;
        $this->updateLongitude = $city->longitude;

        $this->showModelEditCityList = true; // ensure modal is open
    }

    public function updateCity()
    {
        $this->validate([
            'updateCityId' => [
                'required',
                'string',
                'regex:/^CTY\d{6}$/', // Matches CT followed by 5 digits (CTY999999)
                'max:9',
                Rule::unique('Citylists', 'city_id')->ignore($this->editCityId),
            ],
            'updateDistrict' => 'required|string|max:10',
            'updateCityNameEn' => 'required|string|max:255',
            'updateCityNameSi' => 'nullable|string|max:255',
            'updateCityNameTa' => 'nullable|string|max:255',
            'updatePostalCode' => 'required|digits:5',
            'updateLatitude' => 'nullable|numeric|between:5.916,9.835',
            'updateLongitude' => 'nullable|numeric|between:79.652,81.881',
        ]);

        $this->resetPage();

        try{

            Citylists::where('id', $this->editCityId)->update([
                'city_id' => $this->updateCityId,
                'district_id' => $this->updateDistrict,
                'city_name_en' => $this->updateCityNameEn,
                'city_name_si' => $this->updateCityNameSi,
                'city_name_ta' => $this->updateCityNameTa,
                'postcode' => $this->updatePostalCode,
                'latitude' => $this->updateLatitude,
                'longitude' => $this->updateLongitude,
            ]);


            $this->showModelEditCityList = false;

            session()->flash('message', '✅ City information updated successfully!');

            $this->reset(['updateCityId', 'updateDistrict', 'updateCityNameEn', 'updateCityNameSi', 'updateCityNameTa', 'updatePostalCode', 'updateLatitude', 'updateLongitude', 'editCityId']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to update city data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('City update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }
    }


    public function render()
    {
        $cities = Citylists::orderBy('city_id')->paginate(50);
        return view('livewire.main-tables.main-tables-city-list',compact('cities'));
    }
}
