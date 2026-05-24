<?php

namespace App\Livewire\Teacher\Transfer;

use Livewire\Component;
use App\Models\TeacherTransferApplication;
use App\Models\TeacherTransferAppeals;
use App\Models\TeacherTransferApplicationRecommendation;
use App\Models\TeacherTransferRecommendationList;
use App\Models\TeacherTransferPolicyStep;
use App\Support\Transfer\TransferAccess;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TeacherTransferApplicationView extends Component
{
    public $applicationId;
    public $application;

    public $recommendationDecision;
    public $recommendationRemarks;
    public $recommendationOptions = [];
    public $showRecommendationModal = false;
    public $mapLocations = [];
    public bool $showAppealModal = false;
    public string $appealReason = '';
    public string $appealRemarks = '';

    public function mount($id)
    {
        $this->applicationId = $id;

        $this->application = TeacherTransferApplication::with([
            'policy.steps.officeLevel',
            'employee.title',
            'employee.gender',
            'targetProvince',
            'reason',
            'preferences.institution.institution',
            'preferences.zonalOffice.zonal',
            'currentWorkplace.officeLevel',
            'category.transferSubCategory',
            'teacher.teacherCategory',
            'teacher.teacherType',
            'teacher.medium',
            'teacher.appointmentSubject',
            'teacher.mainSubject',
            'teacher.secondarySubject',
            'teacher.currentTeachingSubject',
            'boardRecommendation.recommendationList',
            'boardRecommendation.creator',
            'boardRecommendation.selectedZone',
            'boardRecommendation.selectedSchool',
            'appeals.selectedZone',
            'appeals.selectedSchool',
            'appeals.board',
        ])
            ->where('transfer_application_id', $id)
            ->firstOrFail();

        // Load recommendation options based on current step office level
        $currentStep = $this->application->policy->steps
            ->where('step_order', $this->application->current_step)
            ->first();

        if ($currentStep) {
            $this->recommendationOptions = TeacherTransferRecommendationList::where('office_level_id', $currentStep->office_level_id)
                ->active()
                ->get();
        }

        if (!TransferAccess::canViewTeacherTransferApplication(Auth::user(), $this->application)) {
            abort(403, 'Unauthorized access to this application.');
        }

        // Pre-load existing recommendation if any
        $userWorkplace = Auth::user()->workplace;
        if ($userWorkplace) {
            $existingRec = TeacherTransferApplicationRecommendation::where('transfer_application_id', $this->application->transfer_application_id)
                ->where('workplace_id', $userWorkplace->workplace_id)
                ->first();

            if ($existingRec) {
                $this->recommendationDecision = $existingRec->transfer_recommendation_list_id;
                $this->recommendationRemarks = $existingRec->remarks;
            }
        }
    }

    public function getCanRecommendProperty()
    {
        if ($this->application->status !== 'submitted' && $this->application->status !== 'processing') {
            return false;
        }

        $currentStep = $this->application->policy->steps
            ->where('step_order', $this->application->current_step)
            ->first();

        if (!$currentStep) {
            return false;
        }

        $userWorkplace = Auth::user()->workplace;

        // User must belong to the correct office level and (if ZEO/School) be in the right hierarchy
        if (!$userWorkplace || $userWorkplace->office_level_id !== $currentStep->office_level_id) {
            return false;
        }

        // Hierarchy check (Basic)
        // If step is School, user must be in the applicant's school
        if ($currentStep->office_level_id === 'OLID006' && $userWorkplace->workplace_id !== $this->application->current_workplace) {
            return false;
        }

        // If step is Zone, user must be in the parent zone of the applicant's school
        if ($currentStep->office_level_id === 'OLID004') {
            $applicantWorkplace = $this->application->currentWorkplace;
            if ($applicantWorkplace->parent_workplace_id !== $userWorkplace->workplace_id) {
                // Check if the ZEO is parent of the DIV which is parent of School
                $div = $applicantWorkplace->parent;
                if (!$div || $div->office_level_id !== 'OLID005' || $div->parent_workplace_id !== $userWorkplace->workplace_id) {
                    return false;
                }
            }
        }

        return true;
    }

    public function submitRecommendation()
    {
        if (!$this->canRecommend) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'You are not authorized to approve this transfer at this stage.']);
            return;
        }

        $this->validate([
            'recommendationDecision' => 'required',
            'recommendationRemarks' => 'nullable|string|max:500',
        ]);

        $currentStep = $this->application->policy->steps
            ->where('step_order', $this->application->current_step)
            ->first();
        $isInstitutionApproval = $currentStep?->office_level_id === 'OLID006';

        $decision = TeacherTransferRecommendationList::where('transfer_recommendation_list_id', $this->recommendationDecision)->firstOrFail();

        DB::beginTransaction();
        try {
            // Create or update recommendation record
            TeacherTransferApplicationRecommendation::updateOrCreate(
                [
                    'transfer_application_id' => $this->application->transfer_application_id,
                    'workplace_id' => Auth::user()->workplace_id,
                ],
                [
                    'approved_by' => Auth::user()->people_id,
                    'transfer_recommendation_list_id' => $this->recommendationDecision,
                    'remarks' => $this->recommendationRemarks,
                    'recommendation_status' => true,
                    'active_status' => true,
                ]
            );

            // Handle rejection/release refusal.
            $decisionText = str_replace(["'", "\xE2\x80\x99"], '', strtolower($decision->decision));
            $isRejectedDecision = Str::contains($decisionText, [
                'reject',
                'cannot be released',
                'cant be released',
                'can t be released',
                'not recommended',
            ]);

            if ($isRejectedDecision) {
                $this->application->update(['status' => 'rejected']);
            } else {
                // Advance step
                $nextStep = $this->application->policy->steps
                    ->where('step_order', '>', $this->application->current_step)
                    ->first();

                if ($nextStep) {
                    $this->application->update([
                        'current_step' => $nextStep->step_order,
                        'status' => 'processing'
                    ]);
                } else {
                    // Final step approved
                    $this->application->update(['status' => 'approved']);
                }
            }

            DB::commit();

            $this->showRecommendationModal = false;
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => $isInstitutionApproval
                    ? 'Institution approval submitted successfully.'
                    : 'Recommendation submitted successfully.',
            ]);
            $this->redirect(TransferAccess::recommendationRedirectRoute(Auth::user()));
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function statusBadge($status)
    {
        return match ($status) {
            'draft' => ['color' => 'slate', 'label' => 'Draft'],
            'pending' => ['color' => 'amber', 'label' => 'Pending'],
            'submitted' => ['color' => 'indigo', 'label' => 'Submitted'],
            'processing' => ['color' => 'amber', 'label' => 'Processing'],
            'approved' => ['color' => 'emerald', 'label' => 'Approved'],
            'rejected' => ['color' => 'rose', 'label' => 'Not Recomended'],
            default => ['color' => 'slate', 'label' => ucfirst($status)],
        };
    }

    public function openRecommendationModal()
    {
        if (!$this->canRecommend) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'You are not authorized to approve this transfer at this stage.']);

            return;
        }

        $this->showRecommendationModal = true;
    }

    public function closeRecommendationModal(): void
    {
        $this->showRecommendationModal = false;
    }

    public function getCanSubmitAppealProperty(): bool
    {
        return Auth::user()?->people_id === $this->application->employee_id
            && filled($this->application->boardRecommendation)
            && $this->remainingAppeals > 0
            && !$this->hasPendingAppeal;
    }

    public function getHasPendingAppealProperty(): bool
    {
        return $this->application->appeals
            ->contains(fn ($appeal) => $appeal->appeal_status === TeacherTransferAppeals::STATUS_PENDING);
    }

    public function getRemainingAppealsProperty(): int
    {
        return max(0, 3 - $this->application->appeals->count());
    }

    public function openAppealModal(): void
    {
        if (!$this->canSubmitAppeal) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'You cannot submit a new appeal right now.']);

            return;
        }

        $this->appealReason = '';
        $this->appealRemarks = '';
        $this->showAppealModal = true;
    }

    public function closeAppealModal(): void
    {
        $this->showAppealModal = false;
    }

    public function submitAppeal(): void
    {
        if (!$this->canSubmitAppeal) {
            session()->flash('error', 'You cannot submit a new appeal right now.');

            return;
        }

        $validated = $this->validate([
            'appealReason' => ['required', 'string', 'max:1000'],
            'appealRemarks' => ['nullable', 'string', 'max:2000'],
        ], [
            'appealReason.required' => 'Enter the appeal reason.',
        ]);

        $existingAppealsCount = TeacherTransferAppeals::query()
            ->where('transfer_application_id', $this->application->transfer_application_id)
            ->count();

        $hasPendingAppeal = TeacherTransferAppeals::query()
            ->where('transfer_application_id', $this->application->transfer_application_id)
            ->where('appeal_status', TeacherTransferAppeals::STATUS_PENDING)
            ->exists();

        if ($hasPendingAppeal) {
            $this->application->load([
                'appeals.selectedZone',
                'appeals.selectedSchool',
                'appeals.board',
            ]);

            $this->showAppealModal = false;
            session()->flash('success', 'This application already has a pending appeal. The latest appeal is now shown below.');

            return;
        }

        if ($existingAppealsCount >= 3) {
            $this->application->load([
                'appeals.selectedZone',
                'appeals.selectedSchool',
                'appeals.board',
            ]);

            $this->showAppealModal = false;
            session()->flash('error', 'The maximum number of appeals has already been reached for this application.');

            return;
        }

        try {
            TeacherTransferAppeals::create([
                'transfer_application_id' => $this->application->transfer_application_id,
                'policy_id' => $this->application->policy_id,
                'appeal_reason' => trim($validated['appealReason']),
                'appeal_remarks' => filled($validated['appealRemarks']) ? trim($validated['appealRemarks']) : null,
                'appeal_status' => TeacherTransferAppeals::STATUS_PENDING,
                'active_status' => true,
                'created_by' => Auth::user()?->people_id,
                'updated_by' => Auth::user()?->people_id,
            ]);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $message = collect($exception->errors())->flatten()->first() ?? 'Unable to submit the appeal right now.';
            session()->flash('error', $message);

            return;
        } catch (\Throwable $exception) {
            report($exception);
            session()->flash('error', 'Unable to submit the appeal right now.');

            return;
        }

        $this->application->refresh();
        $this->application->load([
            'policy.steps.officeLevel',
            'employee.title',
            'employee.gender',
            'targetProvince',
            'reason',
            'preferences.institution.institution',
            'preferences.zonalOffice.zonal',
            'currentWorkplace.officeLevel',
            'category.transferSubCategory',
            'teacher.teacherCategory',
            'teacher.teacherType',
            'teacher.medium',
            'teacher.appointmentSubject',
            'teacher.mainSubject',
            'teacher.secondarySubject',
            'teacher.currentTeachingSubject',
            'boardRecommendation.recommendationList',
            'boardRecommendation.creator',
            'boardRecommendation.selectedZone',
            'boardRecommendation.selectedSchool',
            'appeals.selectedZone',
            'appeals.selectedSchool',
            'appeals.board',
        ]);

        $this->showAppealModal = false;
        session()->flash('success', 'Appeal submitted successfully.');

        $this->redirect(route('transfer.teacher-transfer-application.view', $this->application->transfer_application_id), navigate: true);
    }

    public function render()
    {
        return view('livewire.teacher.transfer.teacher-transfer-application-view');
    }
}
