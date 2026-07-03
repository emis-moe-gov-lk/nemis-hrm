<?php

namespace App\Livewire\Institutions\Transfer;

use Livewire\Component;
use App\Models\Institution;
use App\Models\TeacherTransferApplication;
use App\Models\TeacherTransferApplicationRecommendation;
use App\Models\TeacherTransferRecommendationList;
use App\Models\TeacherTransferPolicyStep;
use App\Support\Transfer\TransferAccess;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransferRequests extends Component
{
    public $id;
    public $institution;

    // Modal State
    public $showRecommendationModal = false;
    public $selectedApplication;
    public $recommendationDecision;
    public $recommendationRemarks;
    public $recommendationOptions = [];
    public bool $approvalReadOnly = false;
    public ?string $approvalWindowMessage = null;

    public function mount($id)
    {
        $this->id = $id;
        $this->institution = Institution::query()
            ->where('id', $this->id)
            ->orWhere('workplace_id', $this->id)
            ->firstOrFail();
        abort_unless(TransferAccess::canViewInstitutionRequests(Auth::user(), $this->institution), 403);

        // Fetch institution-level approval options for School level (OLID006)
        $this->recommendationOptions = TeacherTransferRecommendationList::where('office_level_id', 'OLID006')
            ->active()
            ->get();
    }

    public function openRecommendationModal($applicationId)
    {
        $this->prepareApprovalModal($applicationId);

        if (!$this->canEditInstitutionApproval($this->selectedApplication)) {
            $this->closeRecommendationModal();
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Institution approval can be edited only before the institution approval closing date.',
            ]);

            return;
        }

        $this->approvalReadOnly = false;
        $this->showRecommendationModal = true;
    }

    public function viewRecommendationModal($applicationId)
    {
        $this->prepareApprovalModal($applicationId);
        $this->approvalReadOnly = true;
        $this->showRecommendationModal = true;
    }

    public function closeRecommendationModal()
    {
        $this->showRecommendationModal = false;
        $this->selectedApplication = null;
        $this->approvalReadOnly = false;
        $this->approvalWindowMessage = null;
    }

    public function submitRecommendation()
    {
        abort_unless(
            $this->selectedApplication
                && TransferAccess::canViewInstitutionRequests(Auth::user(), $this->institution)
                && $this->selectedApplication->current_workplace === $this->institution->workplace_id,
            403
        );

        if ($this->approvalReadOnly || !$this->canEditInstitutionApproval($this->selectedApplication)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Institution approval is closed for editing.',
            ]);

            return;
        }

        $this->validate([
            'recommendationDecision' => 'required',
            'recommendationRemarks' => 'nullable|string|max:500',
        ]);

        $decision = TeacherTransferRecommendationList::where('transfer_recommendation_list_id', $this->recommendationDecision)->firstOrFail();

        DB::beginTransaction();
        try {
            // Save institution-level approval.
            TeacherTransferApplicationRecommendation::updateOrCreate(
                [
                    'transfer_application_id' => $this->selectedApplication->transfer_application_id,
                    'workplace_id' => $this->institution->workplace_id,
                ],
                [
                    'approved_by' => Auth::user()->people_id,
                    'transfer_recommendation_list_id' => $this->recommendationDecision,
                    'remarks' => $this->recommendationRemarks,
                    'recommendation_status' => true,
                    'active_status' => true,
                ]
            );

            // Handle rejection/release refusal or advance step.
            $isRejectedDecision = $decision->rejectsApplication();

            $institutionStep = $this->institutionApprovalStep($this->selectedApplication);

            if ($isRejectedDecision) {
                $this->selectedApplication->update(['status' => 'rejected']);
            } elseif ($this->selectedApplication->current_step <= ($institutionStep?->step_order ?? $this->selectedApplication->current_step)) {
                // Advance to next step based on policy
                $nextStep = $this->selectedApplication->policy->steps
                    ->where('step_order', '>', $this->selectedApplication->current_step)
                    ->sortBy('step_order')
                    ->first();

                if ($nextStep) {
                    $this->selectedApplication->update([
                        'current_step' => $nextStep->step_order,
                        'status' => 'processing'
                    ]);
                } else {
                    // Final step approved
                    $this->selectedApplication->update(['status' => 'approved']);
                }
            }

            DB::commit();

            $this->closeRecommendationModal();
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Institution approval submitted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function institutionApprovalStep(TeacherTransferApplication $application): ?TeacherTransferPolicyStep
    {
        return $application->policy?->steps?->firstWhere('office_level_id', 'OLID006');
    }

    public function canEditInstitutionApproval(?TeacherTransferApplication $application): bool
    {
        if (!$application || !TransferAccess::canViewInstitutionRequests(Auth::user(), $this->institution)) {
            return false;
        }

        if ($application->current_workplace !== $this->institution->workplace_id) {
            return false;
        }

        if ($application->relationLoaded('boardRecommendation')) {
            if ($application->boardRecommendation) {
                return false;
            }
        } elseif ($application->boardRecommendation()->exists()) {
            return false;
        }

        $step = $this->institutionApprovalStep($application);

        if (!$step?->end_date) {
            return false;
        }

        return now()->lte($step->end_date->copy()->endOfDay());
    }

    protected function prepareApprovalModal($applicationId): void
    {
        abort_unless(TransferAccess::canViewInstitutionRequests(Auth::user(), $this->institution), 403);

        $this->selectedApplication = TeacherTransferApplication::with([
            'employee',
            'policy.steps.officeLevel',
            'category.transferSubCategory',
            'transferSubCategory',
        ])->findOrFail($applicationId);

        abort_unless($this->selectedApplication->current_workplace === $this->institution->workplace_id, 403);

        $existing = $this->existingInstitutionApproval($this->selectedApplication);

        $this->recommendationDecision = $existing?->transfer_recommendation_list_id ?? '';
        $this->recommendationRemarks = $existing?->remarks ?? '';

        $step = $this->institutionApprovalStep($this->selectedApplication);
        $this->approvalWindowMessage = $step?->end_date
            ? 'Institution approval closes on ' . $step->end_date->format('M d, Y') . '.'
            : 'No institution approval closing date is configured.';
    }

    protected function existingInstitutionApproval(TeacherTransferApplication $application): ?TeacherTransferApplicationRecommendation
    {
        return TeacherTransferApplicationRecommendation::with('recommendation')
            ->where('transfer_application_id', $application->transfer_application_id)
            ->where('workplace_id', $this->institution->workplace_id)
            ->first();
    }

    public function render()
    {
        $transferRequests = TeacherTransferApplication::with([
            'employee.title',
            'currentWorkplace',
            'policy.steps.officeLevel',
            'category.transferSubCategory',
            'transferSubCategory',
            'teacher.appointmentSubject',
            'teacher.mainSubject',
            'teacher.secondarySubject',
            'boardRecommendation',
            'recommendations' => function($query) {
                $query->where('workplace_id', $this->institution->workplace_id);
            },
            'recommendations.recommendation',
            'recommendations.approver',
        ])
            ->where('current_workplace', $this->institution->workplace_id)
            ->get();

        return view('livewire.institutions.transfer.transfer-requests', [
            'transferRequests' => $transferRequests,
            'institution'      => $this->institution
        ]);
    }
}
