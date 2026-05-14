<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\InstitutionLanguages;

class MainTablesInstitutionLanguages extends Component
{
    public $showModelNewInstitutionLanguage = false;
    public $institutionLanguageId, $institutionLanguage;

    // 🔹 Validation rules
    protected function rules()
    {
        if ($this->editInstitutionLanguageId) {
            // ✅ Editing existing record
            return [
                'updateInstitutionLanguageId' => [
                    'required',
                    'string',
                    'regex:/^[SLID]{4}\d{2}$/',
                    Rule::unique('institution_languages', 'language_id')->ignore($this->editInstitutionLanguageId),
                ],
                'updateInstitutionLanguage' => [
                    'required',
                    'string',
                    'max:50',
                ],
            ];
        }

        return [
            'institutionLanguageId' => [
                'required',
                'string',
                'regex:/^[SLID]{4}\d{2}$/',
                Rule::unique('institution_languages', 'language_id'),
            ],
            'institutionLanguage' => [
                'required',
                'string',
                'max:50',
            ],
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // 🔹 Submit form
    public function addNewInstitutionLanguage()
    {
        $validated = $this->validate();

        try{
            InstitutionLanguages::create([
                'language_id' => $this->institutionLanguageId,
                'name' => $this->institutionLanguage,
            ]);

            session()->flash('message', '✅ New Institution Language added successfully!');

            // ✅ Close modal
            $this->showModelNewInstitutionLanguage = false;

            // ✅ Reset form fields (but keep modal control variable)
            $this->reset(['institutionLanguageId', 'institutionLanguage']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to save Institution Language data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Institution Language creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }

    }

    public function deleteInstitutionLanguage($id)
    {
        $institutionlanguage = InstitutionLanguages::find($id);

        if ($institutionlanguage) {
            $institutionlanguage->delete();
            session()->flash('message', '✅ Institution Language deleted successfully!');
        } else {
            session()->flash('message', 'Institution Language not found!');
        }
    }

    public function toggleStatus($id)
    {
        $institutionlanguages = InstitutionLanguages::find($id);

        if ($institutionlanguages) {
            // Toggle between 1 and 0
            $institutionlanguages->active_status = $institutionlanguages->active_status == '1' ? '0' : '1';
            $institutionlanguages->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $institutionlanguages->active_status == '1'
                    ? 'Institution Language activated successfully!'
                    : 'Institution Language deactivated successfully!',
            ]);
        }
    }

    public $showModelEditInstitutionLanguage = false;
    public $editInstitutionLanguageId, $updateInstitutionLanguageId, $updateInstitutionLanguage;


    public function editInstitutionLanguage($id)
    {
        $institutionlanguage = InstitutionLanguages::findOrFail($id);

        $this->editInstitutionLanguageId = $institutionlanguage->id;
        $this->updateInstitutionLanguageId = $institutionlanguage->language_id;
        $this->updateInstitutionLanguage = $institutionlanguage->name;

        $this->showModelEditInstitutionLanguage = true; // ensure modal is open
    }

    public function updateInstitutionLanguageList()
    {
        $this->validate([
            'updateInstitutionLanguageId' => [
                'required',
                'string',
                'regex:/^[SLID]{4}\d{2}$/',
                Rule::unique('institution_languages', 'language_id')->ignore($this->editInstitutionLanguageId),
            ],
            'updateInstitutionLanguage' => [
                'required',
                'string',
                'max:50',
            ],
        ]);

        try{

            InstitutionLanguages::where('id', $this->editInstitutionLanguageId)->update([
                'language_id' => $this->updateInstitutionLanguageId,
                'name' => $this->updateInstitutionLanguage,
            ]);


            $this->showModelEditInstitutionLanguage = false;

            session()->flash('message', '✅ Institution Languages updated successfully!');

            $this->reset(['updateInstitutionLanguageId', 'updateInstitutionLanguage', 'editInstitutionLanguageId']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to update institution languages data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Institution languages update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }
    }


    public function render()
    {
        $institutionlanguages = InstitutionLanguages::orderBy('language_id')->paginate(50);
        return view('livewire.main-tables.main-tables-institution-languages', compact('institutionlanguages'));
    }
}
