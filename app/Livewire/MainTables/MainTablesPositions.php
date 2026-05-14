<?php

namespace App\Livewire\MainTables;

use App\Models\Service;
use Livewire\Component;
use App\Models\Position;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Container\Facades\Log;

class MainTablesPositions extends Component
{
    public $showModelNewPosition = false;
    public $serviceOption = [];
    public $positionId, $serviceId, $positionName, $positionDescription;

    protected function rules()
    {

        if($this->editPositionId){
            return [
                'updatePositionId' => [
                    'required',
                    'string',
                    'regex:/^POS\d{3}$/', // Matches CT followed by 5 digits (DIS999)
                    'max:6',
                    Rule::unique('positions', 'position_id')->ignore($this->editPositionId),

                ],
                'updateServiceId' => 'required|string|max:10',
                'updatePositionName' => ['required', 'string', 'max:255',],
                'updatePositionDescription' => 'nullable|string|max:500',
            ];
        }

        return [
            'positionId' => [
                'required',
                'string',
                'regex:/^POS\d{3}$/', // Matches CT followed by 5 digits (DIS999)
                'max:6',
                'unique:positions,position_id',
            ],
            'serviceId' => 'required|string|max:10',
            'positionName' => 'required|string|max:255',
            'positionDescription' => 'nullable|string|max:500',
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // 🔹 Submit form
    public function addNewPosition()
    {
        $validated = $this->validate();

        try{
            Position::create([
                'position_id' => $this->positionId,
                'service_id' => $this->serviceId,
                'position_name' => $this->positionName,
                'description' => $this->positionDescription,

            ]);

            session()->flash('message', '✅ New Position added successfully!');

            // ✅ Close modal
            $this->showModelNewPosition = false;

            // ✅ Reset form fields (but keep modal control variable)
            $this->reset(['positionId', 'serviceId', 'positionName', 'positionDescription']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to save Position data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Position creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }

    }

    public function toggleStatus($id)
    {
        $staPosition = Position::find($id);

        if ($staPosition) {
            // Toggle between 1 and 0
            $staPosition->active_status = $staPosition->active_status == '1' ? '0' : '1';
            $staPosition->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $staPosition->active_status == '1'
                    ? 'Position activated successfully!'
                    : 'Position deactivated successfully!',
            ]);
        }
    }

    public function deletePosition($id)
    {
        $delPosition = Position::find($id);

        if ($delPosition) {
            $delPosition->delete();
            session()->flash('message', 'Position deleted successfully!');
        } else {
            session()->flash('message', 'Position not found!');
        }
    }

    public $showModelEditPosition = false;
    public $updatePositionId, $updateServiceId, $updatePositionName, $updatePositionDescription;
    public $editPositionId;

    public function editPosition($id)
    {
        $position = Position::findOrFail($id);

        $this->editPositionId = $position->id;

        $this->updatePositionId = $position->position_id;
        $this->updateServiceId = $position->service_id;
        $this->updatePositionName = $position->position_name;
        $this->updatePositionDescription = $position->description;

        $this->showModelEditPosition = true; // ensure modal is open
    }

    public function updatePosition()
    {
        try{

            $this->validate([
                'updatePositionId' => [
                    'required',
                    'string',
                    'regex:/^POS\d{3}$/', // Matches CT followed by 5 digits (DIS999)
                    'max:6',
                    Rule::unique('positions', 'position_id')->ignore($this->editPositionId),

                ],
                'updateServiceId' => 'required|string|max:10',
                'updatePositionName' => ['required', 'string', 'max:255', Rule::unique('positions', 'position_name')->ignore($this->editPositionId),],
                'updatePositionDescription' => 'nullable|string|max:500',
            ]);

            Position::where('id', $this->editPositionId)->update([
                'position_id' => $this->updatePositionId,
                'service_id' => $this->updateServiceId,
                'position_name' => $this->updatePositionName,
                'description' => $this->updatePositionDescription,
            ]);


            $this->showModelEditPosition = false;

            session()->flash('message', '✅ Position information updated successfully!');

            $this->reset(['updatePositionId', 'updateServiceId', 'updatePositionName', 'updatePositionDescription', 'editPositionId']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to update Position data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Position update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }
    }


    public function mount(){
        $this->serviceOption = Service::orderBy('service_id', 'asc')->active()->get();
    }

    public function render()
    {
        $positionList = Position::orderBy('service_id')->paginate(50);
        return view('livewire.main-tables.main-tables-positions', compact('positionList'));
    }
}
