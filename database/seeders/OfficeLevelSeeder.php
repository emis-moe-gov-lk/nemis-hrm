<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use App\Models\OfficeLevel;

class OfficeLevelSeeder extends Seeder
{
    public function run(): void
    {
        $officeLevels = [
            ['office_level_id' => 'OLID001', 'office_level_rank' => 1, 'office_level_name' => 'Ministry', 'short_name' => 'MOE'],
            ['office_level_id' => 'OLID002', 'office_level_rank' => 2, 'office_level_name' => 'Provincial Ministry', 'short_name' => 'PMO'],
            ['office_level_id' => 'OLID003', 'office_level_rank' => 3, 'office_level_name' => 'Provincial Education Office', 'short_name' => 'PEO'],
            ['office_level_id' => 'OLID004', 'office_level_rank' => 4, 'office_level_name' => 'Zonal Education Office', 'short_name' => 'ZEO'],
            ['office_level_id' => 'OLID005', 'office_level_rank' => 5, 'office_level_name' => 'Divisional Education Office', 'short_name' => 'DEO'],
            ['office_level_id' => 'OLID006', 'office_level_rank' => 6, 'office_level_name' => 'Institution', 'short_name' => 'INS'],
        ];

        foreach ($officeLevels as $level) {
            OfficeLevel::updateOrCreate(
                ['office_level_id' => $level['office_level_id']],
                [
                    'office_level_rank' => $level['office_level_rank'],
                    'office_level_name' => $level['office_level_name'],
                    'short_name' => $level['short_name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
