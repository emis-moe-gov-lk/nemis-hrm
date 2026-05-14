<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeacherTransferBoardRecommendationListSeeder extends Seeder
{
    public function run(): void
    {
        $decisions = [
            'Approved',
            'Rejected',
            'Pending',
            'Deferred',
            'Under Review',
            'Recommended with conditions',
            'Not Recommended',
        ];

        foreach ($decisions as $index => $decision) {
            DB::table('teacher_transfer_board_recommendation_lists')->updateOrInsert(
                ['ttbr_list_id' => 'TTBR-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT)],
                [
                    'decision' => $decision,
                    'active_status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
