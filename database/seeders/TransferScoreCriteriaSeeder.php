<?php

namespace Database\Seeders;

use App\Models\TransferScoreCriterion;
use Illuminate\Database\Seeder;

class TransferScoreCriteriaSeeder extends Seeder
{
    public function run(): void
    {
        $criteria = [
            ['TSC001', 'distance_current_workplace', 'Distance to Current Workplace', 'Road distance from permanent address to current workplace multiplied by score per kilometre.'],
            ['TSC002', 'current_difficulty_years', 'Current Workplace Difficulty', 'Current workplace difficulty score multiplied by years at the current workplace.'],
            ['TSC003', 'previous_difficulty_years', 'Previous Difficult Area Service', 'Previous workplace difficulty score multiplied by years served in each historical workplace.'],
            ['TSC004', 'age', 'Age', 'Base age gives one score point and each additional full year adds one point.'],
            ['TSC005', 'current_station_years', 'Current Station Years', 'Base years at current station gives one score point and each additional full year adds one point.'],
            ['TSC006', 'achievements', 'Achievements', 'Teacher-entered achievements scored by zonal, district, provincial, or national level.'],
        ];

        foreach ($criteria as $index => [$id, $key, $name, $description]) {
            TransferScoreCriterion::updateOrCreate(
                ['criteria_key' => $key],
                [
                    'criteria_id' => $id,
                    'name' => $name,
                    'description' => $description,
                    'display_order' => $index + 1,
                    'active_status' => true,
                ],
            );
        }
    }
}
