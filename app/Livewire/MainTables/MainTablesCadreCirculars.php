<?php

namespace App\Livewire\MainTables;


use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CadreCirculars;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MainTablesCadreCirculars extends Component
{
    use WithPagination;

    public $showModelNewCadreCircular = false;
    public $supersededCircularOption;
    public $circularId, $circularNo, $title, $description, $issuedDate, $effectiveFrom, $effectiveTo, $supersededCircular, $activeStatus = true;
    public $editCircularId;
    public $updateCircularId, $updateCircularNo, $updateTitle, $updateDescription, $updateIssuedDate, $updateEffectiveFrom, $updateEffectiveTo, $updateSupersededCircular, $updateActiveStatus;

    // 🔹 Validation rules
    protected function rules()
    {
        if ($this->editCircularId) {
            // ✅ Editing existing record
            return [
                'updateCircularId' => [
                    'required',
                    'string',
                    'regex:/^[CIR]{3}\d{3}$/',
                    Rule::unique('cadre_circulars', 'circular_id')->ignore($this->editCircularId),
                ],
                'updateCircularNo' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('cadre_circulars', 'circular_no')->ignore($this->editCircularId),
                ],
                'updateTitle' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'updateDescription' => [
                    'required',
                    'string',
                ],
                'updateIssuedDate' => [
                    'required',
                    'date',
                ],
                'updateEffectiveFrom' => [
                    'nullable',
                    'date',
                ],
                'updateEffectiveTo' => [
                    'nullable',
                    'date',
                ],
                'updateSupersededCircular' => [
                    'nullable',
                    'string',
                    'max:50',
                ],
                'updateActiveStatus' => [
                    'required',
                    'in:0,1,true,false',
                ],
            ];
        }

        return [
            'circularId' => [
                'required',
                'string',
                'regex:/^[CIR]{3}\d{3}$/',
                Rule::unique('cadre_circulars', 'circular_id'),
            ],
            'circularNo' => [
                'required',
                'string',
                'max:50',
                Rule::unique('cadre_circulars', 'circular_no'),
            ],
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'required',
                'string',
            ],
            'issuedDate' => [
                'required',
                'date',
            ],
            'effectiveFrom' => [
                'nullable',
                'date',
            ],
            'effectiveTo' => [
                'nullable',
                'date',
            ],
            'supersededCircular' => [
                'nullable',
                'string',
                'max:50',
            ],
            'activeStatus' => [
                'required',
                'in:0,1,true,false'
            ],

        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
        $this->resetPage();
    }

    // 🔹 Submit form
    public function addNewCadreCircular()
    {
        $validated = $this->validate();
        $this->resetPage();

        try{
            CadreCirculars::create([
                'circular_id' => $this->circularId,
                'circular_no' => $this->circularNo,
                'title' => $this->title,
                'description' => $this->description,
                'issued_date' => $this->issuedDate,
                'effective_from' => $this->effectiveFrom,
                'effective_to' => $this->effectiveTo,
                'supersedes_id' => $this->supersededCircular,
                'active_status' => $this->activeStatus,
            ]);

            if($this->activeStatus){
                CadreCirculars::where('active_status', 1)
                ->where('circular_id', '!=', $this->circularId)
                ->update(['active_status' => 0]);
            }

            session()->flash('message', '✅ New Cadre Circular added successfully!');

            // ✅ Close modal
            $this->showModelNewCadreCircular = false;

            // ✅ Reset form fields (but keep modal control variable)
            $this->reset(['circularId', 'circularNo', 'title', 'description', 'issuedDate', 'effectiveFrom', 'effectiveTo', 'supersededCircular', 'activeStatus']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to save cadre circular data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Cadre circular creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }

    }

    public function toggleStatus($id)
    {
        $cadreCircular = CadreCirculars::find($id);

        if ($cadreCircular) {
            // Toggle between 1 and 0
            $cadreCircular->active_status = $cadreCircular->active_status == '1' ? '0' : '1';
            $cadreCircular->save();

            if($cadreCircular->active_status == '1'){
                CadreCirculars::where('active_status', 1)
                ->where('circular_id', '!=', $cadreCircular->circular_id)
                ->update(['active_status' => 0]);
            }

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $cadreCircular->active_status == '1'
                    ? 'Cadre Circular activated successfully!'
                    : 'Cadre Circular deactivated successfully!',
            ]);
        }
    }

    public $showModelEditCadreCircular = false;

    public function editCadreCircular($id)
    {
        $cadreCircular = CadreCirculars::findOrFail($id);

        $this->editCircularId = $cadreCircular->id;
        $this->updateCircularId = $cadreCircular->circular_id;
        $this->updateCircularNo = $cadreCircular->circular_no;
        $this->updateTitle = $cadreCircular->title;
        $this->updateDescription = $cadreCircular->description;
        $this->updateIssuedDate = $cadreCircular->issued_date;
        $this->updateEffectiveFrom = $cadreCircular->effective_from;
        $this->updateEffectiveTo = $cadreCircular->effective_to;
        $this->updateSupersededCircular = $cadreCircular->supersedes_id;

        $this->showModelEditCadreCircular = true; // ensure modal is open
    }

    public function updateCadreCircularList()
    {
        $this->validate([
            'updateCircularId' => [
                'required',
                'string',
                'regex:/^[CIR]{3}\d{3}$/',
                Rule::unique('cadre_circulars', 'circular_id')->ignore($this->editCircularId),
            ],
            'updateCircularNo' => [
                'required',
                'string',
                'max:50',
                Rule::unique('cadre_circulars', 'circular_no')->ignore($this->editCircularId),
            ],
            'updateTitle' => [
                'required',
                'string',
                'max:255',
            ],
            'updateDescription' => [
                'required',
                'string',
            ],
            'updateIssuedDate' => [
                'required',
                'date',
            ],
            'updateEffectiveFrom' => [
                'nullable',
                'date',
            ],
            'updateEffectiveTo' => [
                'nullable',
                'date',
            ],
            'updateSupersededCircular' => [
                'nullable',
                'string',
                'max:50',
            ],
        ]);

        $this->resetPage();

        try{

            CadreCirculars::where('id', $this->editCircularId)->update([
                'circular_id' => $this->updateCircularId,
                'circular_no' => $this->updateCircularNo,
                'title' => $this->updateTitle,
                'description' => $this->updateDescription,
                'issued_date' => $this->updateIssuedDate,
                'effective_from' => $this->updateEffectiveFrom,
                'effective_to' => $this->updateEffectiveTo,
                'supersedes_id' => $this->updateSupersededCircular,
            ]);


            $this->showModelEditCadreCircular = false;

            session()->flash('message', '✅ Cadre Circular updated successfully!');

            $this->reset(['updateCircularId', 'updateCircularNo', 'updateTitle', 'updateDescription', 'updateIssuedDate', 'updateEffectiveFrom', 'updateEffectiveTo', 'updateSupersededCircular', 'updateActiveStatus', 'editCircularId']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to update cadre circular data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Cadre circular update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }
    }

    public function mount()
    {
        $this->supersededCircularOption = CadreCirculars::orderBy('circular_id')->get();
    }

    public function render()
    {
        $cadreCircular = CadreCirculars::orderBy('circular_id')->paginate(50);
        return view('livewire.main-tables.main-tables-cadre-circulars',compact('cadreCircular'));
    }
}
