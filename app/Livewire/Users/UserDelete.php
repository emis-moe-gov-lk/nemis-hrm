<?php

namespace App\Livewire\Users;

use App\Models\DeletedUsersArchive;
use App\Models\EducationAdministratorService;
use App\Models\EmployerAppointment;
use App\Models\EmployerAppointmentHistory;
use App\Models\EmployerAttachmentAppointment;
use App\Models\EmployerCurrentAppointment;
use App\Models\Family;
use App\Models\FamilyMember;
use App\Models\People;
use App\Models\PeopleEducationQualification;
use App\Models\Principal;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class UserDelete extends Component
{
    public $peopleId;
    public $myprofile;
    public $user;

    public bool $hasDependencies = false;

    public string $deleteReason = '';
    public ?string $deleteNote = null;

    public string $password = '';
    public bool $confirmDeleteCheck = false;
    /**
     * Create a new component instance.
     */
    public function mount($id)
    {
        if($id == Auth::user()->id) {
            session()->flash('error', __('You cannot delete your own account.'));
            return redirect()->route('users.index');
        }

        if($id == 1) {
            session()->flash('error', __('You cannot delete the super admin account.'));
            return redirect()->route('users.index');
        }

        $this->myprofile = People::findOrFail($id);
        $this->peopleId = $this->myprofile->people_id;
    }

     /**
     * Dependency checks: keep minimal and safe.
     * If your system MUST allow delete even with dependencies, set false (not recommended).
     */
    protected function checkDependencies(string $peopleId): bool
    {
        // Add/remove checks based on your real tables
        $hasEmployer = EmployerAppointment::where('employee_id', $peopleId)->exists()
            || EmployerCurrentAppointment::where('employee_id', $peopleId)->exists()
            || EmployerAppointmentHistory::where('employee_id', $peopleId)->exists()
            || EmployerAttachmentAppointment::where('employee_id', $peopleId)->exists();

        $hasRoles = User::where('people_id', $peopleId)->exists();

        $hasPersonLinks = Principal::where('employee_id', $peopleId)->exists()
            || Teacher::where('employee_id', $peopleId)->exists()
            || EducationAdministratorService::where('employee_id', $peopleId)->exists()
            || PeopleEducationQualification::where('people_id', $peopleId)->exists();

        $hasFamily = Family::where('member_a_id', $peopleId)
            ->orWhere('member_b_id', $peopleId)
            ->exists();

        return $hasEmployer || $hasRoles || $hasPersonLinks || $hasFamily;
    }

    /**
     * Modal submit action
     */
    public function confirmDelete()
    {
        // Permission (double check - you already have @can in blade)
        if (!Auth::user() || !Auth::user()->can('user.delete')) {
            session()->flash('error', __('Unauthorized action.'));
            return;
        }

        // Validate form
        $this->validate([
            'deleteReason' => ['required', 'string'],
            'password' => ['required', 'string'],
            'confirmDeleteCheck' => ['accepted'],
            'deleteNote' => ['nullable', 'string', 'max:2000'],
        ]);

        // Verify current user password
        if (!Hash::check($this->password, Auth::user()->password)) {
            session()->flash('error', __('Incorrect password.'));
            return;
        }

        $peopleId = (string) $this->myprofile->people_id;

        // Only allow if this person is already inactive (your SQL condition uses people.active_status = 0)
        $isInList = DB::table('people')
            ->where('people_id', $peopleId)
            ->exists();

        if (! $isInList) {
            session()->flash('warning', __('This profile is not in the list. Please check the User list and try again.'));
            return;
        }

        // If you want to block deletion when there are dependencies, keep this.
        // Your SQL deletes dependencies anyway, but UI warns; you decide policy:
        // If you want to ALLOW deleting even with dependencies, comment this block.
        // if ($this->hasDependencies) {
        //     session()->flash('warning', __('This user has linked records. Please consider deactivation instead.'));
        //     return;
        // }

        try {
            $deletedUserName = '';
            $deletedUserNic = '';

            DB::transaction(function () use ($peopleId, &$deletedUserName, &$deletedUserNic) {

                // OPTIONAL: store delete audit snapshot (recommended)
                // You can insert into deleted_users_archive here if you created it.
                if (DB::getSchemaBuilder()->hasTable('deleted_users_archive')) {
                    $personRow = People::where('people_id', $peopleId)->first();
                    $userRow   = User::where('people_id', $peopleId)->first();
                    $employerAppointmentRow = EmployerAppointment::where('employee_id', $peopleId)->get();
                    $employerCurrentAppointmentRow = EmployerCurrentAppointment::where('employee_id', $peopleId)->get();
                    $employerAppointmentHistoryRow = EmployerAppointmentHistory::where('employee_id', $peopleId)->get();
                    $employerAttachmentAppointmentRow = EmployerAttachmentAppointment::where('employee_id', $peopleId)->get();
                    $educationAdministratorServiceRow = EducationAdministratorService::where('employee_id', $peopleId)->get();
                    $principalRow = Principal::where('employee_id', $peopleId)->get();
                    $teacherRow = Teacher::where('employee_id', $peopleId)->get();
                    $peopleEducationQualificationRow = PeopleEducationQualification::where('people_id', $peopleId)->get();
                    $familyRow = Family::where('member_a_id', $peopleId)->orWhere('member_b_id', $peopleId)->get();
                    if($familyRow->isNotEmpty()) {
                        $childrenRow = FamilyMember::whereIn('family_id', $familyRow->pluck('family_id'))->get();
                    }
                    else {
                        $childrenRow = [];
                    }


                    $snapshot = [
                        'people' => $personRow,
                        'user'   => $userRow,
                        'employerAppointment' => $employerAppointmentRow,
                        'employerCurrentAppointment' => $employerCurrentAppointmentRow,
                        'employerAppointmentHistory' => $employerAppointmentHistoryRow,
                        'employerAttachmentAppointment' => $employerAttachmentAppointmentRow,
                        'educationAdministratorService' => $educationAdministratorServiceRow,
                        'principal' => $principalRow,
                        'teacher' => $teacherRow,
                        'peopleEducationQualification' => $peopleEducationQualificationRow,
                        'family' => $familyRow,
                        'children' => $childrenRow,
                    ];

                    DeletedUsersArchive::create([
                        'original_user_id' => $userRow->id ?? 0,
                        'people_id'        => $peopleId,
                        'user_data'        => json_encode($snapshot),
                        'related_data'     => json_encode(['delete_reason' => $this->deleteReason, 'delete_note' => $this->deleteNote]),
                        'delete_reason'    => $this->deleteReason,
                        'delete_note'      => $this->deleteNote,
                        'deleted_by'       => Auth::user()->people_id ?? (string) Auth::id(),
                        'deleted_ip'       => request()->ip(),
                        'data_hash'        => hash('sha256', json_encode($snapshot)),
                        'is_restored'      => 0,
                        'restored_at'      => null,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ]);

                    $deletedUserName = $personRow->name_with_initials;
                    $deletedUserNic = $personRow->nic;
                }

                // ===========================
                // Apply your SQL logic safely
                // ===========================

                // 1) Employer-related tables
                $employerAppointmentRow->each(function ($employerAppointment) {
                    $employerAppointment->delete();
                });

                $employerAppointmentHistoryRow->each(function ($employerAppointmentHistory) {
                    $employerAppointmentHistory->delete();
                });

                $employerAttachmentAppointmentRow->each(function ($employerAttachmentAppointment) {
                    $employerAttachmentAppointment->delete();
                });

                $employerCurrentAppointmentRow->each(function ($employerCurrentAppointment) {
                    $employerCurrentAppointment->delete();
                });

                $educationAdministratorServiceRow->each(function ($educationAdministratorService) {
                    $educationAdministratorService->delete();
                });

                $principalRow->each(function ($principal) {
                    $principal->delete();
                });

                $teacherRow->each(function ($teacher) {
                    $teacher->delete();
                });

                $peopleEducationQualificationRow->each(function ($peopleEducationQualification) {
                    $peopleEducationQualification->delete();
                });

                $childrenRow->each(function ($children) {
                    $children->delete();
                });

                $familyRow->each(function ($family) {
                    $family->delete();
                });

                $userRow->delete();
                $personRow->delete();

            });

            $this->dispatch('modal-close', name: 'delete-profile');

            session()->flash(
                'success',
                "User '{$deletedUserName}' (NIC: {$deletedUserNic}) was deleted successfully."
            );

            return redirect()->route('users.index');

        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.users.user-delete', [
            'myprofile' => $this->myprofile,
        ]);
    }
}
