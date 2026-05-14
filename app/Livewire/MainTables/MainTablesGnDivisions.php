<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use App\Models\GnDivision;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\DivisionalSecretariatOffice;
use Livewire\WithPagination;


class MainTablesGnDivisions extends Component
{
    use WithPagination;
    public $showModelNewGNDivision = false;
    public $dsOfficeOption = [];
    public $gnDivisionId, $dsOfficeId, $gnDivisionCode, $gnDivisionName;
    public $search = '';

    protected function rules()
    {

        if($this->editGNDivisionId){
            return [
                'updateGNDivisionId' => [
                    'required',
                    'string',
                    'regex:/^GND\d{5}$/', // Matches CT followed by 5 digits (DIS999)
                    'max:8',
                    Rule::unique('gn_divisions', 'gn_division_id')->ignore($this->editGNDivisionId),

                ],
                'updateDsOfficeId' => 'required|string|max:10',
                'updateGNDivisionCode' => 'required|string|max:255',
                'updateGNDivisionName' => 'required|string|max:255',
            ];
        }

        return [
            'gnDivisionId' => [
                'required',
                'string',
                'regex:/^GND\d{5}$/', // Matches CT followed by 5 digits (DIS999)
                'max:8',
                'unique:gn_divisions,gn_division_id',
            ],
            'dsOfficeId' => 'required|string|max:10',
            'gnDivisionCode' => 'required|string|max:255',
            'gnDivisionName' => 'required|string|max:255',
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
        $this->resetPage();
    }

    // 🔹 Submit form
    public function addNewGNDivision()
    {
        $validated = $this->validate();
        $this->resetPage();

        try{
            GnDivision::create([
                'gn_division_id' => $this->gnDivisionId,
                'dso_id' => $this->dsOfficeId,
                'gn_division_code' => $this->gnDivisionCode,
                'gn_division_name' => $this->gnDivisionName,

            ]);

            session()->flash('message', '✅ New GN Division added successfully!');

            // ✅ Close modal
            $this->showModelNewGNDivision = false;

            // ✅ Reset form fields (but keep modal control variable)
            $this->reset(['gnDivisionId', 'dsOfficeId', 'gnDivisionCode', 'gnDivisionName']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to save GN Division data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('GN Division creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }

    }

    public function toggleStatus($id)
    {
        $staGNDivision = GNDivision::find($id);

        if ($staGNDivision) {
            // Toggle between 1 and 0
            $staGNDivision->active_status = $staGNDivision->active_status == '1' ? '0' : '1';
            $staGNDivision->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $staGNDivision->active_status == '1'
                    ? 'GN Division activated successfully!'
                    : 'GN Division deactivated successfully!',
            ]);
        }
    }

    public function deleteGNDivision($id)
    {
        $delGNDivision = GNDivision::find($id);

        if ($delGNDivision) {
            $delGNDivision->delete();
            session()->flash('message', 'GN Division deleted successfully!');
        } else {
            session()->flash('message', 'GN Division not found!');
        }
    }

    public $showModelEditGNDivision = false;
    public $updateGNDivisionId, $updateDsOfficeId, $updateGNDivisionCode, $updateGNDivisionName;
    public $editGNDivisionId;

    public function editGNDivision($id)
    {
        $gnDivision = GNDivision::findOrFail($id);

        $this->editGNDivisionId = $gnDivision->id;

        $this->updateGNDivisionId = $gnDivision->gn_division_id;
        $this->updateDsOfficeId = $gnDivision->dso_id;
        $this->updateGNDivisionCode = $gnDivision->gn_division_code;
        $this->updateGNDivisionName = $gnDivision->gn_division_name;

        $this->showModelEditGNDivision = true; // ensure modal is open
    }

    public function updateGNDivision()
    {
        try{

            $this->validate([
                'updateGNDivisionId' => [
                    'required',
                    'string',
                    'regex:/^GND\d{5}$/', // Matches CT followed by 5 digits (DIS999)
                    'max:8',
                    Rule::unique('gn_divisions', 'gn_division_id')->ignore($this->editGNDivisionId),

                ],
                'updateDsOfficeId' => 'required|string|max:10',
                'updateGNDivisionCode' => 'required|string|max:255',
                'updateGNDivisionName' => 'required|string|max:255',
            ]);

            GNDivision::where('id', $this->editGNDivisionId)->update([
                'gn_division_id' => $this->updateGNDivisionId,
                'dso_id' => $this->updateDsOfficeId,
                'gn_division_code' => $this->updateGNDivisionCode,
                'gn_division_name' => $this->updateGNDivisionName,
            ]);

            $this->resetPage();

            $this->showModelEditGNDivision = false;

            session()->flash('message', '✅ GN Division information updated successfully!');

            $this->reset(['updateGNDivisionId', 'updateDsOfficeId', 'updateGNDivisionCode', 'updateGNDivisionName', 'editGNDivisionId']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to update GN Division data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('GN Division update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }
    }


    public function mount(){
        $this->dsOfficeOption = DivisionalSecretariatOffice::orderBy('dso_name', 'asc')->active()->get();
    }

    public function render()
    {
        
        $gnDivisionList = GnDivision::orderBy('gn_division_name');

        // =========================
            // SEARCH (Optimized)
            // =========================
            if (!empty($this->search)) {

                $search = trim($this->search);

                $gnDivisionList->orWhere(function ($query) use ($search) {
                    $query->where('gn_division_id', 'like', "%{$search}%")
                        ->orWhere('gn_division_code', 'like', "%{$search}%")
                        ->orWhere('gn_division_name', 'like', "%{$search}%");
                });
            }

            $gnDivisionList = $gnDivisionList->paginate(50);

        return view('livewire.main-tables.main-tables-gn-divisions', compact('gnDivisionList'));
    }
}
