<?php

namespace App\Livewire\MainTables;

use App\Models\Title;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MainTablesTitles extends Component
{
    public $showModelNewTitle = false;
    public $titleId, $title;

    // 🔹 Validation rules
    protected function rules()
    {
        if ($this->editTitleId) {
            // ✅ Editing existing record
            return [
                'updateTitleId' => [
                    'required',
                    'string',
                    'regex:/^[T]{1}\d{2}$/',
                    Rule::unique('titles', 'title_id')->ignore($this->editTitleId),
                ],
                'updateTitle' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('titles', 'title_name')->ignore($this->editTitleId),
                ],
            ];
        }

        return [
            'titleId' => [
                'required',
                'string',
                'regex:/^[T]{1}\d{2}$/',
                Rule::unique('titles', 'title_id'),
            ],
            'title' => [
                'required',
                'string',
                'max:50',
                Rule::unique('titles', 'title_name'),
            ],
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // 🔹 Submit form
    public function addNewTitle()
    {
        $validated = $this->validate();

        try{
            Title::create([
                'title_id' => $this->titleId,
                'title_name' => $this->title,
            ]);

            session()->flash('message', '✅ New Title added successfully!');

            // ✅ Close modal
            $this->showModelNewTitle = false;

            // ✅ Reset form fields (but keep modal control variable)
            $this->reset(['titleId', 'title']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to save title data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Title creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }

    }

    public function deleteTitle($id)
    {
        $title = Title::find($id);

        if ($title) {
            $title->delete();
            session()->flash('message', '✅ Title deleted successfully!');
        } else {
            session()->flash('message', 'Title not found!');
        }
    }

    public function toggleStatus($id)
    {
        $title = Title::find($id);

        if ($title) {
            // Toggle between 1 and 0
            $title->active_status = $title->active_status == '1' ? '0' : '1';
            $title->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $title->active_status == '1'
                    ? 'Title activated successfully!'
                    : 'Title deactivated successfully!',
            ]);
        }
    }

    public $showModelEditTitle = false;
    public $editTitleId, $updateTitleId, $updateTitle;


    public function editTitle($id)
    {
        $title = Title::findOrFail($id);

        $this->editTitleId = $title->id;
        $this->updateTitleId = $title->title_id;
        $this->updateTitle = $title->title_name;

        $this->showModelEditTitle = true; // ensure modal is open
    }

    public function updateTitleList()
    {
        $this->validate([
            'updateTitleId' => [
                'required',
                'string',
                'regex:/^[T]{1}\d{2}$/',
                Rule::unique('titles', 'title_id')->ignore($this->editTitleId),
            ],
            'updateTitle' => [
                'required',
                'string',
                'max:50',
                Rule::unique('titles', 'title_name')->ignore($this->editTitleId),
            ],
        ]);

        try{

            Title::where('id', $this->editTitleId)->update([
                'title_id' => $this->updateTitleId,
                'title_name' => $this->updateTitle,
            ]);


            $this->showModelEditTitle = false;

            session()->flash('message', '✅ Title updated successfully!');

            $this->reset(['updateTitleId', 'updateTitle', 'editTitleId']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to update title data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Title update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $titles = Title::orderBy('title_id')->paginate(50);
        return view('livewire.main-tables.main-tables-titles', compact('titles'));
    }
}
