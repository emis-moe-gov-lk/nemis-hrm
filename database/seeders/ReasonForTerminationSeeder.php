<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReasonForTerminationSeeder extends Seeder
{
    public function run(): void
    {
        $reasons = [
            'Retirement',
            'Resignation',
            'Dismissal',
            'Disciplinary termination',
            'Compulsory retirement',
            'Termination during probation',
            'Medical unfitness',
            'End of contract',
            'Absconding / Abandonment of service',
            'Reduction of staff / Redundancy',
            'Voluntary retirement scheme (VRS)',
            'Completion of service period',
            'Death',
        ];

        $data = [];

        foreach ($reasons as $index => $reason) {
            $data[] = [
                'termination_id' => 'RTS-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'reason' => $reason,
                'active_status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('reasons_for_termination_of_services')->insert($data);
    }
}
