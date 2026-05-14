<?php

namespace App\Livewire\Institutions\Transfer;

use Livewire\Component;
use App\Models\Institution;
use App\Models\TeacherTransferApplication;
use App\Models\TeacherTransferApplicationRecommendation;
use App\Models\TeacherTransferRecommendationList;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

    public function mount($id)
    {
        $this->id = $id;
        $this->institution = Institution::findOrFail($this->id);

        // Fetch recommendation options for School level (OLID006)
        $this->recommendationOptions = TeacherTransferRecommendationList::where('office_level_id', 'OLID006')
            ->active()
            ->get();
    }

    public function openRecommendationModal($applicationId)
    {
        $this->selectedApplication = TeacherTransferApplication::with('employee')->findOrFail($applicationId);
        
        // Check for existing recommendation at this workplace
        $existing = TeacherTransferApplicationRecommendation::where('transfer_application_id', $this->selectedApplication->transfer_application_id)
            ->where('workplace_id', $this->institution->workplace_id)
            ->first();

        if ($existing) {
            $this->recommendationDecision = $existing->transfer_recommendation_list_id;
            $this->recommendationRemarks = $existing->remarks;
        } else {
            $this->recommendationDecision = '';
            $this->recommendationRemarks = '';
        }
        
        $this->showRecommendationModal = true;
    }

    public function closeRecommendationModal()
    {
        $this->showRecommendationModal = false;
        $this->selectedApplication = null;
    }

    public function submitRecommendation()
    {
        $this->validate([
            'recommendationDecision' => 'required',
            'recommendationRemarks' => 'nullable|string|max:500',
        ]);

        $decision = TeacherTransferRecommendationList::where('transfer_recommendation_list_id', $this->recommendationDecision)->firstOrFail();

        DB::beginTransaction();
        try {
            // Save recommendation
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

            // Handle rejection or advance step
            if (Str::contains(strtolower($decision->decision), 'reject') || Str::contains(strtolower($decision->decision), 'can’t be released') || Str::contains(strtolower($decision->decision), 'can t be released')) {
                $this->selectedApplication->update(['status' => 'rejected']);
            } else {
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
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Recommendation submitted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        $transferRequests = TeacherTransferApplication::with([
            'employee',
            'currentWorkplace',
            'policy.steps',
            'category',
            'teacher.appointmentSubject',
            'teacher.mainSubject',
            'teacher.secondarySubject',
            'recommendations' => function($query) {
                $query->where('workplace_id', $this->institution->workplace_id);
            }
        ])
            ->where('current_workplace', $this->institution->workplace_id)
            ->get();

        return view('livewire.institutions.transfer.transfer-requests', [
            'transferRequests' => $transferRequests,
            'institution'      => $this->institution
        ]);
    }
}
