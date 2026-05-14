<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TeacherTransferRecommendationList;

class TeacherTransferRecommendationListSeeder extends Seeder
{
    public function run(): void
    {
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
                'decision' => 'This teacher can’t be released',
            ],

            [
                'office_level_id' => 'OLID004',
                'decision' => 'The service of this teacher has been/has not been confirmed. Date of Confirmation of Service.',
            ],

            [
                'office_level_id' => 'OLID004',
                'decision' => 'There are/are not disciplinary or audit queries against this teacher',
            ],

            [
                'office_level_id' => 'OLID004',
                'decision' => 'This teacher is/is not qualified for the transfer.',
            ],

            [
                'office_level_id' => 'OLID004',
                'decision' => 'This teacher can be released with/without a successor',
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
