<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use App\Models\GradesList;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class MainTablesGrades extends Component
{
    use WithPagination;

    public $name, $order, $active_status = 1;
    public $editId;
    public $showModal = false;

    public function toggleStatus($id)
    {
        $grade = GradesList::find($id);
        if ($grade) {
            $grade->active_status = !$grade->active_status;
            $grade->save();
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Status updated.']);
        }
    }

    public function create()
    {
        $this->reset(['name', 'order', 'editId']);
        $this->showModal = true;
    }

    public function edit($id)
    {
        $grade = GradesList::findOrFail($id);
        $this->editId = $grade->id;
        $this->name = $grade->name;
        $this->order = $grade->order;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'order' => 'required|integer',
        ]);

        if ($this->editId) {
            GradesList::find($this->editId)->update([
                'name' => $this->name,
                'order' => $this->order,
            ]);
        } else {
            GradesList::create([
                'name' => $this->name,
                'order' => $this->order,
                'active_status' => true,
            ]);
        }

        $this->showModal = false;
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Grade level saved.']);
    }

    public function delete($id)
    {
        GradesList::find($id)?->delete();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Grade level removed.']);
    }

    public function render()
    {
        return view('livewire.main-tables.main-tables-grades', [
            'grades' => GradesList::orderBy('order')->paginate(20),
        ]);
    }
}
