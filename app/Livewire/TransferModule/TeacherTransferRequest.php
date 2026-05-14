<?php

namespace App\Livewire\TransferModule;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TeacherTransferApplication;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TeacherTransferRequestExport;

class TeacherTransferRequest extends Component
{
    use WithPagination;

    public $filterPolicy = '';
    public $filterCategory = '';
    public $filterZone = '';

    public function updatedFilterPolicy()
    {
        $this->filterCategory = '';
        $this->resetPage();
    }

    public function updatedFilterCategory()
    {
        $this->resetPage();
    }

    public function updatedFilterZone()
    {
        $this->resetPage();
    }

    public function exportExcel()
    {
        return Excel::download(
            new TeacherTransferRequestExport($this->filterPolicy, $this->filterCategory, $this->filterZone),
            'Transfer-Requests-' . date('Y-m-d') . '.xlsx'
        );
    }

    public function render()
    {
        $policies = \App\Models\TransferPolicy::active()->orderByDesc('policy_year')->get();

        $transferCategories = \App\Models\TransferCategory::scopedListQuery($this->filterPolicy)->get();

        $zones = \App\Models\ZonalEducationOffice::active()->get();
        if (Auth::check()) {
            $user = Auth::user();
            $query = TeacherTransferApplication::with(['policy', 'targetProvince', 'reason', 'employee.title']);

            // If not super admin, filter by workplace hierarchy
            if (!$user->hasRole('super admin')) {
                $workplace = $user->workplace;
                if ($workplace) {
                    $allowedWorkplaceIds = $workplace->getAllChildWorkplaces();
                    $query->whereIn('current_workplace', $allowedWorkplaceIds);
                } else {
                    // Fallback if no workplace is found
                    $query->where('employee_id', $user->people_id);
                }
            }

            if ($this->filterPolicy) {
                $query->where('policy_id', $this->filterPolicy);
            }

            if ($this->filterCategory) {
                $query->where('transfer_category', $this->filterCategory);
            }

            if ($this->filterZone) {
                $query->where(function ($q) {
                    $q->where('current_workplace', $this->filterZone)
                      ->orWhereHas('currentWorkplace.institution', function ($instQ) {
                          $instQ->where('zeo_wp_id', $this->filterZone);
                      });
                });
            }

            $applications = $query->orderByDesc('created_at')->paginate(10);
        } else {
            $applications = TeacherTransferApplication::whereIn('id', [])->paginate(10);
        }

        return view('livewire.transfer-module.teacher-transfer-request', [
            'applications' => $applications,
            'policies' => $policies,
            'transferCategories' => $transferCategories,
            'zones' => $zones,
        ]);
    }

    public function statusBadge($status)
    {
        return match ($status) {
            'draft' => ['color' => 'slate', 'label' => 'Draft'],
            'submitted' => ['color' => 'indigo', 'label' => 'Submitted'],
            'processing' => ['color' => 'amber', 'label' => 'Processing'],
            'approved' => ['color' => 'emerald', 'label' => 'Approved'],
            'rejected' => ['color' => 'rose', 'label' => 'Rejected'],
            default => ['color' => 'slate', 'label' => ucfirst($status)],
        };
    }
}
