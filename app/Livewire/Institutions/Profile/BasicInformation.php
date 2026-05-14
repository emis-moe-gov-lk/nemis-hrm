<?php

namespace App\Livewire\Institutions\Profile;

use Livewire\Component;
use App\Models\Institution;
use App\Models\InstitutionCategory;
use App\Models\InstitutionType;
use App\Models\InstitutionLanguages;
use App\Models\InstitutionGender;
use App\Models\InstitutionalFacility;
use App\Models\GradeSpan;
use App\Models\InstitutionAuthority;

class BasicInformation extends Component
{
    public $institution;

    public $institutionCategoryOption;
    public $institutionTypeOption;
    public $institutionLanguageOption;
    public $typeByGenderOption;
    public $facilitiesOption;
    public $gradeSpanOption;
    public $authorityOption;

    public $cencesNumber;
    public $intituteNumber;
    public $establishedYear;
    public $institutionName;
    public $otherName;
    public $institutionCategory;
    public $institutionType;
    public $institutionLanguage;
    public $typeByGender;
    public $facilities;
    public $gradeSpan;
    public $authority;

    public function rules()
    {
        return [
            'cencesNumber' => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9\-]+$/',   // only numbers and hyphens if needed
                'unique:institutions,census_no,' . $this->institution->id,
            ],

            'intituteNumber' => [
                'required',
                'string',
                'max:20',
                'unique:institutions,workplace_id,' . $this->institution->id,
            ],

            'establishedYear' => [
                'nullable',
                'digits:4',
                'integer',
                'min:1800',
                'max:' . date('Y'),
            ],


            'institutionName' => [
                'required',
                'string',
                'max:255',
            ],

            'otherName' => [
                'nullable',
                'string',
                'max:255',
            ],

            'institutionCategory' => [
                'required',
                'exists:institution_categories,institution_category_id',
            ],

            'institutionType' => [
                'required',
                'exists:institution_types,institution_types_id',
            ],

            'institutionLanguage' => [
                'required',
                'exists:languages,language_id',
            ],

            'typeByGender' => [
                'required',
                'exists:type_by_genders,type_by_gender_id',
            ],

            'facilities' => [
                'required',
                'exists:institutional_facilities,facilities_id',
            ],

            'gradeSpan' => [
                'required',
                'exists:grade_spans,grade_span_id',
            ],

            'authority' => [
                'required',
                'exists:authorities,authority_id',
            ],
        ];
    }


    // Live validation per-field
    public function updated($property)
    {
        $this->validateOnly($property);
    }

    public function mount($institutionId)
    {
        $this->institution = Institution::find($institutionId);

        $this->cencesNumber = $this->institution->census_no;
        $this->intituteNumber = $this->institution->workplace_id;
        $this->establishedYear = $this->institution->established_year;
        $this->institutionName = strtoupper($this->institution->name);
        $this->otherName = strtoupper($this->institution->other_name);
        $this->institutionCategory = $this->institution->institutionCategory->institution_category_id;
        $this->institutionType = $this->institution->institutionType->institution_types_id;
        $this->institutionLanguage = $this->institution->institutionLanguages->language_id;
        $this->typeByGender = $this->institution->typeByGender->gender_id;
        $this->facilities = $this->institution->facilities->facilities_id;
        $this->gradeSpan = $this->institution->gradeSpan->grade_span_id;
        $this->authority = $this->institution->authority->authority_id;

        $this->institutionCategoryOption = InstitutionCategory::all();
        $this->institutionTypeOption = InstitutionType::all();
        $this->institutionLanguageOption = InstitutionLanguages::all();
        $this->typeByGenderOption = InstitutionGender::all();
        $this->facilitiesOption = InstitutionalFacility::all();
        $this->gradeSpanOption = GradeSpan::all();
        $this->authorityOption = InstitutionAuthority::all();
    }

    public function updateBasicInformation()
    {
        $this->validate([
            'cencesNumber' => 'required',
            'intituteNumber' => 'required',
            'institutionName' => 'required',
            'otherName' => 'nullable',
            'institutionCategory' => 'required',
            'institutionType' => 'required',
            'institutionLanguage' => 'required',
            'typeByGender' => 'required',
            'facilities' => 'required',
            'gradeSpan' => 'required',
            'authority' => 'required',
        ]);
        try {
            $this->institution->update([
                'census_no' => $this->cencesNumber,
                'workplace_id' => $this->intituteNumber,
                'established_year' => $this->establishedYear,
                'name' => $this->institutionName,
                'other_name' => $this->otherName,
                'institution_category_id' => $this->institutionCategory,
                'institution_types_id' => $this->institutionType,
                'language_id' => $this->institutionLanguage,
                'gender_id' => $this->typeByGender,
                'facilities_id' => $this->facilities,
                'grade_span_id' => $this->gradeSpan,
                'authority_id' => $this->authority,
            ]);
            session()->flash('success', 'Basic Information Updated Successfully.');
            return $this->redirect(url()->previous(), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update Basic Information: ' . $e->getMessage());
            return $this->redirect(url()->previous(), navigate: true);
        }
    }

    public function render()
    {
        return view('livewire.institutions.profile.basic-information');
    }

    public function resetForm()
    {
        $this->mount($this->institution->id);
    }
}
