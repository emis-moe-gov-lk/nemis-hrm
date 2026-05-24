<?php

namespace App\Livewire\TransferModule\Teacher;

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
    public $filterSubCategory = '';
    public $filterZone = '';

    public function updatedFilterPolicy()
    {
        $this->filterCategory = '';
        $this->filterSubCategory = '';
        $this->resetPage();
    }

    public function updatedFilterSubCategory()
    {
        $this->filterCategory = '';

        $this->resetPage();
    }

    public function updatedFilterZone()
    {
        $this->resetPage();
    }

    public function exportExcel()
    {
        return Excel::download(
            new TeacherTransferRequestExport($this->filterPolicy, $this->filterCategory, $this->filterSubCategory, $this->filterZone),
            'Transfer-Requests-' . date('Y-m-d') . '.xlsx'
        );
    }

    public function render()
    {
        $policies = \App\Models\TeacherTransferPolicy::active()->orderByDesc('policy_year')->get();

        $transferSubCategories = \App\Models\TeacherTransferSubCategory::active()
            ->orderBy('display_order')
            ->get();

        $transferCategories = \App\Models\TeacherTransferCategory::scopedListQuery(
            $this->filterPolicy,
            null,
            null,
            $this->filterSubCategory ?: null
        )->with('transferSubCategory')->get();

        $zones = \App\Models\ZonalEducationOffice::active()->get();
        if (Auth::check()) {
            $user = Auth::user();
            $query = TeacherTransferApplication::with(['policy', 'targetProvince', 'reason', 'employee.title', 'category.transferSubCategory', 'transferSubCategory']);

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

            if ($this->filterSubCategory) {
                $query->where('transfer_sub_category_id', $this->filterSubCategory);
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

        return view('livewire.transfer-module.teacher.teacher-transfer-request', [
            'applications' => $applications,
            'policies' => $policies,
            'transferSubCategories' => $transferSubCategories,
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
            'rejected' => ['color' => 'rose', 'label' => 'Not Recomended'],
            default => ['color' => 'slate', 'label' => ucfirst($status)],
        };
    }
}
