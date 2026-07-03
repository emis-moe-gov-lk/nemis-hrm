<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\TeacherTransferRecommendationList;

class TeacherTransferRecommendationListSeeder extends Seeder
{
    public function run(): void
    {
        $hasRejectsApplicationColumn = Schema::hasColumn(
            'teacher_transfer_recommendation_lists',
            'rejects_application'
        );

        $principalRenameData = [
            'decision' => 'Transfer is not recommended for this teacher.',
            'active_status' => true,
        ];

        if ($hasRejectsApplicationColumn) {
            $principalRenameData['rejects_application'] = true;
        }

        TeacherTransferRecommendationList::where('office_level_id', 'OLID006')
            ->whereIn('decision', [
                "This teacher can\u{2019}t be released",
                "This teacher can't be released",
            ])
            ->update($principalRenameData);

        $zonalDecisionRenames = [
            'There are/are not disciplinary or audit queries against this teacher' => [
                'decision' => 'There are / are not disciplinary actions or audit queries against this teacher',
                'rejects_application' => false,
            ],
            'This teacher is/is not qualified for the transfer.' => [
                'decision' => 'This teacher is/is not Recomemded for the transfer.',
                'rejects_application' => true,
            ],
            'This teacher can be released with/without a successor' => [
                'decision' => 'This teacher can be Transferd with/without a successor',
                'rejects_application' => false,
            ],
        ];

        foreach ($zonalDecisionRenames as $oldDecision => $renameData) {
            $updateData = [
                'decision' => $renameData['decision'],
                'active_status' => true,
            ];

            if ($hasRejectsApplicationColumn) {
                $updateData['rejects_application'] = $renameData['rejects_application'];
            }

            TeacherTransferRecommendationList::where('office_level_id', 'OLID004')
                ->where('decision', $oldDecision)
                ->update($updateData);
        }

        $recommendations = [

            [
                'office_level_id' => 'OLID006',
                'decision' => 'This teacher can be released without a successor as he/she is an excess teacher',
                'rejects_application' => false,
            ],

            [
                'office_level_id' => 'OLID006',
                'decision' => 'This teacher can be released only if a suitable successor is provided.',
                'rejects_application' => false,
            ],

            [
                'office_level_id' => 'OLID006',
                'decision' => 'This teacher can be released without a successor.',
                'rejects_application' => false,
            ],

            [
                'office_level_id' => 'OLID006',
                'decision' => 'Transfer is not recommended for this teacher.',
                'rejects_application' => true,
            ],

            [
                'office_level_id' => 'OLID004',
                'decision' => 'The service of this teacher has been/has not been confirmed. Date of Confirmation of Service.',
                'rejects_application' => false,
            ],

            [
                'office_level_id' => 'OLID004',
                'decision' => 'There are / are not disciplinary actions or audit queries against this teacher',
                'rejects_application' => false,
            ],

            [
                'office_level_id' => 'OLID004',
                'decision' => 'This teacher is/is not Recomemded for the transfer.',
                'rejects_application' => true,
            ],

            [
                'office_level_id' => 'OLID004',
                'decision' => 'This teacher can be Transferd with/without a successor',
                'rejects_application' => false,
            ],

            [
                'office_level_id' => 'OLID003',
                'decision' => 'This teacher is/is not qualified for the transfer.',
                'rejects_application' => true,
            ],

            [
                'office_level_id' => 'OLID003',
                'decision' => 'This teacher can be released with/without a successor.',
                'rejects_application' => false,
            ],
        ];

        foreach ($recommendations as $rec) {
            $updateData = [
                'active_status' => true
            ];

            if ($hasRejectsApplicationColumn) {
                $updateData['rejects_application'] = $rec['rejects_application'];
            }

            TeacherTransferRecommendationList::updateOrCreate(
                [
                    'office_level_id' => $rec['office_level_id'],
                    'decision' => $rec['decision']
                ],
                $updateData
            );
        }
    }
}
