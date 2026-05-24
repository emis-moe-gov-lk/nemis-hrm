<?php

namespace App\Livewire\Institutions\Profile;

use Livewire\Component;
use App\Models\Institution;
use App\Models\InstitutionGrade;
use App\Models\InstitutionClass;
use App\Models\InstitutionStudentAdmission;
use App\Models\MediumOfInstruction;
use Illuminate\Support\Facades\DB;

class StudentEnrollment extends Component
{
    public $id; // Institution ID
    public $institution;
    public $academicYear;
    
    // New Grade/Class inputs
    public $selectedGradeListId; // From GradesList
    public $newClassName;
    public $selectedGradeId; // From InstitutionGrade (instance)
    public $selectedMediumId;

    // Enrollment Data
    public $enrollments = []; // [class_id => ['male' => X, 'female' => Y]]

    public function mount($id)
    {
        $this->id = $id;
        $this->institution = Institution::findOrFail($this->id);
        $this->academicYear = date('Y');
        $this->loadEnrollments();
    }

    public function loadEnrollments()
    {
        $this->enrollments = [];
        $grades = InstitutionGrade::where('institution_id', $this->id)
            ->where('academic_year', $this->academicYear)
            ->with(['classes.admissions' => function($query) {
                $query->where('academic_year', $this->academicYear);
            }])
            ->get();

        foreach ($grades as $grade) {
            foreach ($grade->classes as $class) {
                $admission = $class->admissions->first();
                $this->enrollments[$class->id] = [
                    'male' => $admission ? $admission->male_count : 0,
                    'female' => $admission ? $admission->female_count : 0,
                ];
            }
        }
    }

    public function updatedAcademicYear()
    {
        $this->loadEnrollments();
    }

    public function addGrade()
    {
        $this->validate([
            'selectedGradeListId' => 'required|exists:grades_lists,id',
        ]);

        // Check if grade already exists for this year
        $exists = InstitutionGrade::where('institution_id', $this->id)
            ->where('grade_id', $this->selectedGradeListId)
            ->where('academic_year', $this->academicYear)
            ->exists();

        if ($exists) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'This grade is already added for ' . $this->academicYear]);
            return;
        }

        $gradeList = \App\Models\GradesList::find($this->selectedGradeListId);

        InstitutionGrade::create([
            'institution_id' => $this->id,
            'grade_id' => $this->selectedGradeListId,
            'academic_year' => $this->academicYear,
            'order' => $gradeList->order,
        ]);

        $this->selectedGradeListId = '';
        $this->loadEnrollments();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Grade level added to ' . $this->academicYear]);
    }

    public function addClass()
    {
        $this->validate([
            'selectedGradeId' => 'required|exists:institution_grades,id',
            'newClassName' => 'required|string|max:255',
            'selectedMediumId' => 'required|in:0,1,2,3',
        ]);

        // Check for duplicate class name in this grade (stricter: same name not allowed even in different medium)
        $exists = InstitutionClass::where('institution_grade_id', $this->selectedGradeId)
            ->where('class_name', $this->newClassName)
            ->exists();

        if ($exists) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'A class section with this name already exists in this grade. Each section must have a unique name.']);
            return;
        }

        InstitutionClass::create([
            'institution_grade_id' => $this->selectedGradeId,
            'class_name' => $this->newClassName,
            'medium_id' => $this->selectedMediumId,
        ]);

        $this->newClassName = '';
        $this->loadEnrollments();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Class added successfully.']);
    }

    public function copyFromPreviousYear()
    {
        $prevYear = $this->academicYear - 1;
        $prevGrades = InstitutionGrade::where('institution_id', $this->id)
            ->where('academic_year', $prevYear)
            ->with('classes')
            ->get();

        if ($prevGrades->isEmpty()) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'No data found for ' . $prevYear]);
            return;
        }

        DB::beginTransaction();
        try {
            foreach ($prevGrades as $oldGrade) {
                // Check if already exists in current year
                $newGrade = InstitutionGrade::firstOrCreate([
                    'institution_id' => $this->id,
                    'grade_id' => $oldGrade->grade_id,
                    'academic_year' => $this->academicYear,
                ], [
                    'order' => $oldGrade->order,
                ]);

                foreach ($oldGrade->classes as $oldClass) {
                    InstitutionClass::firstOrCreate([
                        'institution_grade_id' => $newGrade->id,
                        'class_name' => $oldClass->class_name,
                        'medium_id' => $oldClass->medium_id,
                    ]);
                }
            }
            DB::commit();
            $this->loadEnrollments();
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Configuration copied from ' . $prevYear]);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function saveEnrollment()
    {
        DB::beginTransaction();
        try {
            foreach ($this->enrollments as $classId => $data) {
                InstitutionStudentAdmission::updateOrCreate(
                    [
                        'institution_class_id' => $classId,
                        'academic_year' => $this->academicYear,
                    ],
                    [
                        'male_count' => $data['male'] ?? 0,
                        'female_count' => $data['female'] ?? 0,
                    ]
                );
            }
            DB::commit();
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Enrollment data saved successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function deleteClass($classId)
    {
        InstitutionClass::find($classId)?->delete();
        $this->loadEnrollments();
    }

    public function deleteGrade($gradeId)
    {
        InstitutionGrade::find($gradeId)?->delete();
        $this->loadEnrollments();
    }

    public function getMediumName($id)
    {
        $mediums = [
            '0' => 'Sinhala',
            '1' => 'Tamil',
            '2' => 'English',
            '3' => 'Bilingual',
        ];

        return $mediums[$id] ?? 'N/A';
    }

    public function render()
    {
        $grades = InstitutionGrade::where('institution_id', $this->id)
            ->where('academic_year', $this->academicYear)
            ->with(['classes', 'gradeList'])
            ->orderBy('order')
            ->get();

        $mediums = [
            '0' => 'Sinhala',
            '1' => 'Tamil',
            '2' => 'English',
            '3' => 'Bilingual',
        ];
        
        $globalGrades = \App\Models\GradesList::orderBy('order')->get();

        return view('livewire.institutions.profile.student-enrollment', [
            'grades' => $grades,
            'mediums' => $mediums,
            'globalGrades' => $globalGrades,
        ]);
    }
}
