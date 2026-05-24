<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use Carbon\Carbon;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        $services = [
            [
                'service_id'   => 'SER001',
                'service_name' => 'SLTS',
                'description'  => 'Sri Lanka Teacher Service',
                'rank'         => 7,
                'active_status'=> '1',
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'service_id'   => 'SER002',
                'service_name' => 'SLTES',
                'description'  => 'Sri Lanka Teacher Educator Service',
                'rank'         => 5,
                'active_status'=> '1',
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'service_id'   => 'SER003',
                'service_name' => 'SLTAS',
                'description'  => 'Sri Lanka Teacher Advisor Service',
                'rank'         => 6,
                'active_status'=> '1',
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'service_id'   => 'SER004',
                'service_name' => 'SLPS',
                'description'  => 'Sri Lanka Principal Service',
                'rank'         => 4,
                'active_status'=> '1',
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'service_id'   => 'SER005',
                'service_name' => 'SLEAS',
                'description'  => 'Sri Lanka Education Administrative Service',
                'rank'         => 2,
                'active_status'=> '1',
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'service_id'   => 'SER006',
                'service_name' => 'SLAS',
                'description'  => 'Sri Lanka Administrative Service',
                'rank'         => 1,
                'active_status'=> '1',
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'service_id'   => 'SER007',
                'service_name' => 'DOS',
                'description'  => 'Development Officer Service',
                'rank'         => 8,
                'active_status'=> '1',
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'service_id'   => 'SER008',
                'service_name' => 'MSO',
                'description'  => 'Management Services Officer Service',
                'rank'         => 9,
                'active_status'=> '1',
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'service_id'   => 'SER009',
                'service_name' => 'SLAcS',
                'description'  => 'Sri Lanka Accountants Service',
                'rank'         => 3,
                'active_status'=> '1',
                'created_at' => $now, 
                'updated_at' => $now
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['service_id' => $service['service_id']], // Unique check
                $service
            );
        }
    }
}
