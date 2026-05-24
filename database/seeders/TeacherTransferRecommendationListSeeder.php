<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TeacherTransferRecommendationList;

class TeacherTransferRecommendationListSeeder extends Seeder
{
    public function run(): void
    {
        TeacherTransferRecommendationList::where('office_level_id', 'OLID006')
            ->whereIn('decision', [
                "This teacher can\u{2019}t be released",
                "This teacher can't be released",
            ])
            ->update([
                'decision' => 'Transfer is not recommended for this teacher.',
                'active_status' => true,
            ]);

        $zonalDecisionRenames = [
            'There are/are not disciplinary or audit queries against this teacher'
                => 'There are / are not disciplinary actions or audit queries against this teacher',
            'This teacher is/is not qualified for the transfer.'
                => 'This teacher is/is not Recomemded for the transfer.',
            'This teacher can be released with/without a successor'
                => 'This teacher can be Transferd with/without a successor',
        ];

        foreach ($zonalDecisionRenames as $oldDecision => $newDecision) {
            TeacherTransferRecommendationList::where('office_level_id', 'OLID004')
                ->where('decision', $oldDecision)
                ->update([
                    'decision' => $newDecision,
                    'active_status' => true,
                ]);
        }

        $recommendations = [

            [
                'office_level_id' => 'OLID006',
                'decision' => 'This teacher can be released without a successor as he/she is an excess teacher',
            ],

            [
                'office_level_id' => 'OLID006',
                'decision' => 'This teacher can be released only if a suitable successor is provided.',
            ],

            [
                'office_level_id' => 'OLID006',
                'decision' => 'This teacher can be released without a successor.',
            ],

            [
                'office_level_id' => 'OLID006',
                'decision' => 'Transfer is not recommended for this teacher.',
            ],

            [
                'office_level_id' => 'OLID004',
                'decision' => 'The service of this teacher has been/has not been confirmed. Date of Confirmation of Service.',
            ],

            [
                'office_level_id' => 'OLID004',
                'decision' => 'There are / are not disciplinary actions or audit queries against this teacher',
            ],

            [
                'office_level_id' => 'OLID004',
                'decision' => 'This teacher is/is not Recomemded for the transfer.',
            ],

            [
                'office_level_id' => 'OLID004',
                'decision' => 'This teacher can be Transferd with/without a successor',
            ],

            [
                'office_level_id' => 'OLID003',
                'decision' => 'This teacher is/is not qualified for the transfer.',
            ],

            [
                'office_level_id' => 'OLID003',
                'decision' => 'This teacher can be released with/without a successor.',
            ],
        ];

        foreach ($recommendations as $rec) {

            TeacherTransferRecommendationList::updateOrCreate(
                [
                    'office_level_id' => $rec['office_level_id'],
                    'decision' => $rec['decision']
                ],
                [
                    'active_status' => true
                ]
            );
        }
    }
}
