<?php

namespace Database\Seeders;

use App\Models\EmployerAppointmentHistory;
use App\Models\EmployerAppointmentRankHistory;
use App\Models\EmployerAppointmentWorkplaceHistory;
use App\Models\EmployerAppointmentPositionHistory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployerAppointmentHistoryMigratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $histories = EmployerAppointmentHistory::all();

        if ($histories->isEmpty()) {
            $this->command->info('No history data found in employer_appointment_histories.');
            return;
        }

        $this->command->info('Migrating ' . $histories->count() . ' history records...');

        DB::transaction(function () use ($histories) {
            foreach ($histories as $history) {
                // 1. Migrate Rank History
                if ($history->rank_id) {
                    EmployerAppointmentRankHistory::create([
                        'appointment_id' => $history->appointment_id,
                        'employee_id'    => $history->employee_id,
                        'ref_letter_no' => $history->appointment_letter_no,
                        'rank_id'        => $history->rank_id,
                        'start_date'     => $history->appoint_date,
                        'end_date'       => $history->end_date,
                        'is_active'      => false,
                        'remarks'        => 'Migrated from legacy history table',
                        'created_by'     => $history->created_by,
                        'updated_by'     => $history->updated_by,
                    ]);
                }

                // 2. Migrate Workplace History
                if ($history->workplace_id) {
                    EmployerAppointmentWorkplaceHistory::create([
                        'appointment_id' => $history->appointment_id,
                        'employee_id'    => $history->employee_id,
                        'ref_letter_no' => $history->appointment_letter_no,
                        'workplace_id'   => $history->workplace_id,
                        'office_level_id' => $history->office_level_id,
                        'start_date'     => $history->appoint_date,
                        'end_date'       => $history->end_date,
                        'is_active'      => false,
                        'remarks'        => 'Migrated from legacy history table',
                        'created_by'     => $history->created_by,
                        'updated_by'     => $history->updated_by,
                    ]);
                }

                // 3. Migrate Position History
                if ($history->position_id) {
                    EmployerAppointmentPositionHistory::create([
                        'appointment_id' => $history->appointment_id,
                        'employee_id'    => $history->employee_id,
                        'ref_letter_no' => $history->appointment_letter_no,
                        'position_id'    => $history->position_id,
                        'start_date'     => $history->appoint_date,
                        'end_date'       => $history->end_date,
                        'is_active'      => false,
                        'remarks'        => 'Migrated from legacy history table',
                        'created_by'     => $history->created_by,
                        'updated_by'     => $history->updated_by,
                    ]);
                }
            }
        });

        $this->command->info('Data migration completed successfully.');
    }
}
