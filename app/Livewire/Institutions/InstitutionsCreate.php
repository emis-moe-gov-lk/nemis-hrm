<?php

namespace App\Livewire\Institutions;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;

use App\Models\Authority;
use App\Models\Workplaces;
use App\Models\Institution;
use App\Models\DistrictsList;
use App\Models\InstitutionCategory;
use App\Models\InstitutionLanguages;
use App\Models\InstitutionType;
use App\Models\InstitutionGender;
use App\Models\InstitutionEthnisity;
use App\Models\GradeSpan;
use App\Models\InstitutionalFacility;
use App\Models\ZonalEducationOffice;
use App\Models\DivisionalEducationOffice;
use App\Models\ProvincialEducationOffice;

use App\Rules\UniquePhoneAcrossTables;
use App\Rules\UniqueEmailAcrossTables;

use Illuminate\Support\Facades\DB;


class InstitutionsCreate extends Component
{
    // -------------------------
    // Administration Details
    // -------------------------
    public $censusNo;
    public $workplaceId;
    public $provincialOffice;
    public $zonalOffice;
    public $divisionOffice;
    public $district;

    // -------------------------
    // Institute Details
    // -------------------------
    public $institutionName;
    public $otherName;
    public $establishedYear;

    public $addressLine1;
    public $postalCode;
    public $email;
    public $contactNumber;
    public $latitude;
    public $longitude;
    public $status;

    // -------------------------
    // Catagories
    // -------------------------
    public $institutionCategory;
    public $authorityCategory;
    public $institutionType;
    public $languageId;
    public $gradespanId;
    public $institutionGenderId;
    public $institutionEthnisityId;
    public $institutionalFacilityId;


    // -------------------------
    // Dropdown Options
    // -------------------------
    public $provinceOption = [];
    public $districtOption = [];
    public $zonalOfficeOption = [];
    public $divisionOfficeOption = [];
    public $institutionCategoryOption = [];
    public $authorityOption = [];
    public $institutionLanguagesOption = [];
    public $institutionTypeOption = [];
    public $institutionGenderOption = [];
    public $institutionEthnisityOption = [];
    public $gradeSpanOption = [];
    public $institutionalFacilityOption = [];

    public $censusExists = false;

    private const SCHOOL_OFFICE_LEVEL = 'OLID006';

    protected function rules(): array
    {
        return [
            'censusNo' => [
                'required',
                'integer',
                'digits_between:1,5',
            ],

            'workplaceId' => [
                'required',
                'string',
                'regex:/^INS\d{7}$/',
            ],


            'provincialOffice' => [
                'required',
            ],

            'zonalOffice' => [
                'required',
            ],

            'divisionOffice' => [
                'required',
            ],

            'institutionCategory' => [
                'required',
            ],

            'authorityCategory' => [
                'required',
            ],

            'institutionType' => [
                'required',
            ],

            'languageId' => [
                'required',
            ],

            'gradespanId' => [
                'required',
            ],

            'institutionGenderId' => [
                'required',
            ],

            'institutionEthnisityId' => [
                'required',
            ],

            'institutionalFacilityId' => [
                'required',
            ],

            'district' => [
                'required',
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

            'establishedYear' => [
                'required',
                'integer',
                'digits:4',
                'between:1900,' . now()->year,
            ],

            'contactNumber' => [
                'nullable',
                'regex:/^0(70|71|72|74|75|76|77|78)\d{7}$/',
                new UniquePhoneAcrossTables(null),
            ],

            'email' => [
                'nullable',
                'email',
                new UniqueEmailAcrossTables(null),
            ],

            'latitude' => [
                'nullable',
                'numeric',
                'between:5.916,9.835',
            ],

            'longitude' => [
                'nullable',
                'numeric',
                'between:79.652,81.881',
            ],

            'status' => [
                'required',
            ],
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount(): void
    {
        $this->provinceOption = ProvincialEducationOffice::orderBy('short_name')->get();
        $this->districtOption = DistrictsList::orderBy('district_name')->get();
        $this->institutionCategoryOption = InstitutionCategory::orderBy('institution_category_id')->get();
        $this->authorityOption = Authority::orderBy('authority_id')->get();
        $this->institutionLanguagesOption = InstitutionLanguages::orderBy('language_id')->get();
        $this->institutionTypeOption = InstitutionType::orderBy('institution_types_id')->get();
        $this->institutionGenderOption = InstitutionGender::orderBy('gender_id')->get();
        $this->institutionEthnisityOption = InstitutionEthnisity::orderBy('ethnicity_id')->get();
        $this->gradeSpanOption = GradeSpan::orderBy('grade_span_id')->get();
        $this->institutionalFacilityOption = InstitutionalFacility::Active()->orderBy('name', 'asc')->get();
    }

    public function updatedCensusNo($value): void
    {
        // Normalize input (numbers only)
        $censusNo = preg_replace('/\D/', '', (string) $value);

        // Empty or invalid input → reset safely
        if ($censusNo === '') {
            $this->reset(['censusExists', 'workplaceId']);
            return;
        }

        // Check existence safely
        $this->censusExists = Institution::where('census_No', $censusNo)->exists();

        // Generate workplace ID with protection
        try {
            $this->workplaceId = $this->generateWorkplaceId($censusNo);
        } catch (\Throwable $e) {
            $this->workplaceId = null;

            // Optional: log for debugging
            // logger()->warning('Invalid census number', ['value' => $value]);
        }
    }


    public function updatedProvincialOffice($value): void
    {
        $this->zonalOfficeOption = ZonalEducationOffice::where('peo_wp_id', $value)
            ->orderBy('short_name')
            ->get();
    }


    public function updatedZonalOffice($value): void
    {
        $this->divisionOfficeOption = DivisionalEducationOffice::where('zeo_wp_id', $value)
            ->orderBy('short_name')
            ->get();
    }


    private function generateWorkplaceId(int|string $censusNo): string
    {
        // Ensure numeric value only
        $numeric = preg_replace('/\D/', '', (string) $censusNo);

        if ($numeric === '') {
            throw new \InvalidArgumentException('Invalid census number provided.');
        }

        return 'INS' . str_pad($numeric, 7, '0', STR_PAD_LEFT);
    }



    public function save(): void
    {
        $this->validate();

        DB::beginTransaction();

        try {

            $workplace = Workplaces::create([
                'workplace_id' => $this->workplaceId,
                'office_level_id' => self::SCHOOL_OFFICE_LEVEL,
                'parent_workplace_id' => $this->divisionOffice,
            ]);

            Institution::create([
                'workplace_id' => $workplace->workplace_id,
                'census_no' => $this->censusNo,
                'institution_category_id' => $this->institutionCategory,
                'authority_id' => $this->authorityCategory,
                'language_id' => $this->languageId,
                'gender_id' => $this->institutionGenderId,
                'ethnicity_id' => $this->institutionEthnisityId,
                'institution_types_id' => $this->institutionType,
                'grade_span_id' => $this->gradespanId,
                'facilities_id' => $this->institutionalFacilityId,
                'district_id' => $this->district,
                'zeo_wp_id' => $this->zonalOffice,
                'deo_wp_id' => $this->divisionOffice,
                'name' => strtoupper(trim($this->institutionName)),
                'other_name' => $this->otherName ? strtoupper(trim($this->otherName)) : null,
                'established_year' => $this->establishedYear,
                'email' => $this->email,
                'phone' => $this->contactNumber,
                'address' => $this->addressLine1,
                'postal_code' => $this->postalCode,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'active_status' => $this->status,
                'created_by' => auth()->user()?->people_id,
            ]);

            DB::commit();

            session()->flash('success', 'Institution created successfully.');
            $this->reset();
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);

            session()->flash(
                'error',
                'Institution creation failed. Please try again.' . $e->getMessage()
            );
        }
    }



    public function render()
    {
        return view('livewire.institutions.institutions-create');
    }
}
