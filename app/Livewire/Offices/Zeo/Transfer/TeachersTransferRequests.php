<?php

namespace App\Livewire\Offices\Zeo\Transfer;

use Livewire\Component;

use App\Models\Workplaces;
use App\Models\ZonalEducationOffice;
use App\Models\TeacherTransferApplication;
use App\Models\TeacherTransferApplicationRecommendation;
use App\Models\TeacherTransferRecommendationList;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TeachersTransferRequests extends Component
{
    public $id;
    public $zeo;
    public $office;
    
    // Modal State
    public $showRecommendationModal = false;
    public $selectedApplication;
    public $recommendationDecision;
    public $recommendationRemarks;
    public $recommendationOptions = [];

    public function mount($id)
    {
        $this->id = $id;
        $this->zeo = ZonalEducationOffice::findOrFail($this->id);
        $this->office = Workplaces::where('workplace_id', $this->zeo->workplace_id)->firstOrFail();
        
        // Fetch Zonal Level (OLID004) Recommendation Options
        $this->recommendationOptions = TeacherTransferRecommendationList::where('office_level_id', 'OLID004')
            ->active()
            ->get();
    }

    public function openRecommendationModal($applicationId)
    {
        $this->selectedApplication = TeacherTransferApplication::with(['employee', 'currentWorkplace', 'policy.steps'])->findOrFail($applicationId);
        
        // Check for existing recommendation at this zonal office
        $existing = TeacherTransferApplicationRecommendation::where('transfer_application_id', $this->selectedApplication->transfer_application_id)
            ->where('workplace_id', $this->office->workplace_id)
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
            // Save zonal recommendation
            TeacherTransferApplicationRecommendation::updateOrCreate(
                [
                    'transfer_application_id' => $this->selectedApplication->transfer_application_id,
                    'workplace_id' => $this->office->workplace_id,
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
            if (Str::contains(strtolower($decision->decision), 'reject') || Str::contains(strtolower($decision->decision), 'not qualified')) {
                $this->selectedApplication->update(['status' => 'rejected']);
            } else {
                // Advance to next step (e.g. Provincial)
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
                    $this->selectedApplication->update(['status' => 'approved']);
                }
            }

            DB::commit();

            $this->closeRecommendationModal();
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Zonal recommendation submitted successfully.']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        // Discover all schools in this zone
        $childWorkplaces = $this->office->getAllChildWorkplaces();

        $transferRequests = TeacherTransferApplication::with([
            'employee', 
            'currentWorkplace', 
            'policy.steps', 
            'category', 
            'teacher.appointmentSubject', 
            'teacher.mainSubject', 
            'teacher.secondarySubject',
            'recommendations' => function($query) {
                $query->where('workplace_id', $this->office->workplace_id);
            }
        ])
            ->whereIn('current_workplace', $childWorkplaces)
            ->where('status', '!=', 'draft') // Only show processed/processing ones
            ->get();

        return view('livewire.offices.zeo.transfer.teachers-transfer-requests', [
            'transferRequests' => $transferRequests,
        ]);
    }
}
