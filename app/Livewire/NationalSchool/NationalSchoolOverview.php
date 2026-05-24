<?php

namespace App\Livewire\NationalSchool;

use Livewire\Component;

class NationalSchoolOverview extends Component
{
    public function render()
    {
        $items = [
            [
                'label' => 'School Registry',
                'desc' => 'Comprehensive directory of all national level educational institutions.',
                'icon' => 'building-library',
                'route' => route('national-school.list'),
                'gradient' => 'from-indigo-500 to-blue-600',
                'shadow' => 'shadow-indigo-200',
                'text' => 'text-indigo-600',
                'permission' => 'institution.list.view'
            ],
            [
                'label' => 'Teacher Cadre',
                'desc' => 'Manage and monitor teacher appointments in national schools.',
                'icon' => 'academic-cap',
                'route' => route('national-school.teacher-list'),
                'gradient' => 'from-emerald-500 to-teal-600',
                'shadow' => 'shadow-emerald-200',
                'text' => 'text-emerald-600',
                'permission' => 'institution.list.view'
            ],
            [
                'label' => 'SLPS Officers',
                'desc' => 'Registry of Principals and leadership staff in the national sector.',
                'icon' => 'user-group',
                'route' => route('national-school.principal-list'),
                'gradient' => 'from-blue-500 to-cyan-600',
                'shadow' => 'shadow-blue-200',
                'text' => 'text-blue-600',
                'permission' => 'institution.list.view'
            ],
            [
                'label' => 'SLEAS Officers',
                'desc' => 'Administrative leadership and Sri Lanka Education Administrative Service data.',
                'icon' => 'briefcase',
                'route' => route('national-school.sleas-list'),
                'gradient' => 'from-violet-500 to-purple-600',
                'shadow' => 'shadow-violet-200',
                'text' => 'text-violet-600',
                'permission' => 'institution.list.view'
            ],
            [
                'label' => 'Approved Cadre',
                'desc' => 'DMS approved workforce and establishment details for national schools.',
                'icon' => 'clipboard-document-check',
                'route' => route('national-school.dms-approved-cadre'),
                'gradient' => 'from-orange-500 to-amber-600',
                'shadow' => 'shadow-orange-200',
                'text' => 'text-orange-600',
                'permission' => 'institution.list.view'
            ],
        ];

        return view('livewire.national-school.national-school-overview', [
            'items' => $items
        ]);
    }
}
