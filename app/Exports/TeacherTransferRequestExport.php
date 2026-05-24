<?php

namespace App\Exports;

use App\Models\TeacherTransferApplication;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class TeacherTransferRequestExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filterPolicy;
    protected $filterCategory;
    protected $filterSubCategory;
    protected $filterZone;

    public function __construct($filterPolicy, $filterCategory, $filterSubCategory, $filterZone)
    {
        $this->filterPolicy = $filterPolicy;
        $this->filterCategory = $filterCategory;
        $this->filterSubCategory = $filterSubCategory;
        $this->filterZone = $filterZone;
    }

    public function collection()
    {
        $query = TeacherTransferApplication::with([
            'policy', 
            'targetProvince', 
            'reason', 
            'employee.title', 
            'category.transferSubCategory',
            'transferSubCategory',
            'currentWorkplace'
        ]);

        if (Auth::check()) {
            $user = Auth::user();

            if (!$user->hasRole('super admin')) {
                $query->where('employee_id', $user->people_id);
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

            return $query->orderByDesc('created_at')->get();
        }

        return collect();
    }

    public function headings(): array
    {
        return [
            'Submission Date',
            'Transfer Policy',
            'Category',
            'Applicant Name',
            'Applicant Employee ID',
            'Applicant NIC',
            'Applicant Contact',
            'Applicant Address',
            'Current Province',
            'Current Zone',
            'Target Province',
            'Status',
        ];
    }

    public function map($app): array
    {
        $currentProvince = 'N/A';
        $currentZone = 'N/A';

        if ($app->currentWorkplace) {
            $office = $app->currentWorkplace->office();
            if ($office && $office instanceof \App\Models\Institution) {
                $currentZone = $office->zonalEducationOffice->name ?? 'N/A';
                $currentProvince = $office->zonalEducationOffice->provincialEducationOffice->name ?? 'N/A';
            } elseif ($office && $office instanceof \App\Models\ZonalEducationOffice) {
                $currentZone = $office->name ?? 'N/A';
                $currentProvince = $office->provincialEducationOffice->name ?? 'N/A';
            } elseif ($office && $office instanceof \App\Models\ProvincialEducationOffice) {
                $currentProvince = $office->name ?? 'N/A';
            }
        }

        return [
            $app->created_at ? $app->created_at->format('Y-m-d') : 'N/A',
            $app->policy->title ?? 'N/A',
            $app->transferSubCategory->name ?? $app->category->transferSubCategory->name ?? $app->category->transfer_category_name ?? 'N/A',
            ($app->employee->title->title_name ?? '') . ' ' . ($app->employee->full_name ?? 'N/A'),
            $app->employee_id ?? 'N/A',
            $app->employee->nic ?? 'N/A',
            $app->employee->phone ?? 'N/A',
            $app->permanent_address ?? 'N/A',
            $currentProvince,
            $currentZone,
            $app->targetProvince->short_name ?? 'N/A',
            ucfirst($app->status ?? 'unknown'),
        ];
    }
}
