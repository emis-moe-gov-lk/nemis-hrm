<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use App\Models\DistrictsList;
use Illuminate\Validation\Rule;
use App\Models\DivisionalSecretariatOffice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\WithPagination;

class MainTablesDSOffice extends Component
{
    use WithPagination;
    public $districtOption = [];

    public function mount(){
        $this->districtOption = DistrictsList::orderBy('district_id', 'asc')->active()->get();
    }

    public function toggleStatus($id)
    {
        $staDSOffice = DivisionalSecretariatOffice::find($id);

        if ($staDSOffice) {
            // Toggle between 1 and 0
            $staDSOffice->active_status = $staDSOffice->active_status == '1' ? '0' : '1';
            $staDSOffice->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $staDSOffice->active_status == '1'
                    ? 'DS Office activated successfully!'
                    : 'DS Office deactivated successfully!',
            ]);
        }
    }

    public function deleteDSOffice($id)
    {
        $delDSOffice = DivisionalSecretariatOffice::find($id);

        if ($delDSOffice) {
            $delDSOffice->delete();
            session()->flash('message', 'DS Office deleted successfully!');
        } else {
            session()->flash('message', 'DS Office not found!');
        }
    }

    public $showModelNewDSOffice = false;
    public $dsOfficeId, $districtId, $dsOfficeName;

    protected function rules()
    {
        if($this->editDSOfficeId){
            return [
                'updateDSOfficeId' => [
                    'required',
                    'string',
                    'regex:/^DSO\d{4}$/', // Matches CT followed by 5 digits (DSO9999)
                    'max:7',
                    Rule::unique('divisional_secretariat_offices', 'dso_id')->ignore($this->editDSOfficeId),

                ],
                'UpdateDistrictId' => 'required|string|max:10',
                'updateDSOfficeName' => 'required|string|max:255',
            ];
        }

        return [
            'dsOfficeId' => [
                'required',
                'string',
                'regex:/^DSO\d{4}$/', // Matches CT followed by 5 digits (DSO9999)
                'max:7',
                'unique:divisional_secretariat_offices,dso_id',
            ],
            'districtId' => 'required|string|max:10',
            'dsOfficeName' => 'required|string|max:255',
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
        $this->resetPage();
    }

    // 🔹 Submit form
    public function addNewDSOffice()
    {
        $validated = $this->validate();
        $this->resetPage();

        try{
            DivisionalSecretariatOffice::create([
                'dso_id' => $this->dsOfficeId,
                'district_id' => $this->districtId,
                'dso_name' => $this->dsOfficeName,

            ]);

            session()->flash('message', '✅ New DS Office added successfully!');

            // ✅ Close modal
            $this->showModelNewDSOffice = false;

            // ✅ Reset form fields (but keep modal control variable)
            $this->reset(['dsOfficeId', 'districtId', 'dsOfficeName']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to save DS Office data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('DS Office creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }

    }

    public $showModelEditDSOffice = false;
    public $updateDSOfficeId, $UpdateDistrictId, $updateDSOfficeName;
    public $editDSOfficeId;

    public function editDSOffice($id)
    {
        $dsOffice = DivisionalSecretariatOffice::findOrFail($id);

        $this->editDSOfficeId = $dsOffice->id;

        $this->updateDSOfficeId = $dsOffice->dso_id;
        $this->UpdateDistrictId = $dsOffice->district_id;
        $this->updateDSOfficeName = $dsOffice->dso_name;

        $this->showModelEditDSOffice = true; // ensure modal is open
    }

    public function updateDSOffice()
    {
        try{

            $this->validate([
                'updateDSOfficeId' => [
                    'required',
                    'string',
                    'regex:/^DSO\d{4}$/', // Matches CT followed by 5 digits (DSO9999)
                    'max:7',
                    Rule::unique('divisional_secretariat_offices', 'dso_id')->ignore($this->editDSOfficeId),

                ],
                'UpdateDistrictId' => 'required|string|max:10',
                'updateDSOfficeName' => 'required|string|max:255',
            ]);

            DivisionalSecretariatOffice::where('id', $this->editDSOfficeId)->update([
                'dso_id' => $this->updateDSOfficeId,
                'district_id' => $this->UpdateDistrictId,
                'dso_name' => $this->updateDSOfficeName,
            ]);

            $this->resetPage();

            $this->showModelEditDSOffice = false;

            session()->flash('message', '✅ DS Office information updated successfully!');

            $this->reset(['updateDSOfficeId', 'UpdateDistrictId', 'updateDSOfficeName', 'editDSOfficeId']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to update DS Office data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('DS Office update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }
    }


    public function render()
    {
        $dsOfficeList = DivisionalSecretariatOffice::orderBy('dso_id')->paginate(50);
        return view('livewire.main-tables.main-tables-d-s-office', compact('dsOfficeList'));
    }
}
