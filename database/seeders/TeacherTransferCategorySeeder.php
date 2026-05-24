<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TeacherTransferCategory;

class TeacherTransferCategorySeeder extends Seeder
{
    public function run(): void
    {

        $categories = [

            [
                'office_level_id' => 'OLID001', // Ministry
                'transfer_category_name' => 'National school transfer (MOE)',
                'description' => 'National school transfer',
                'active_status' => true
            ],

            [
                'office_level_id' => 'OLID003',
                'transfer_category_name' => 'Province to National School (WPN)',
                'description' => 'Province to National School',
                'active_status' => true
            ],

            [
                'office_level_id' => 'OLID003', // Provincial
                'transfer_category_name' => 'Inter Province Transfer (WP)',
                'description' => 'Inter Province Transfer',
                'active_status' => true
            ],

            [
                'office_level_id' => 'OLID003',
                'transfer_category_name' => 'Other Province Transfer (WP)',
                'description' => 'Transfers handled at provincial level',
                'active_status' => true
            ],

            [
                'office_level_id' => 'OLID004', // ZEO
                'transfer_category_name' => 'Intra Zone - Within Zone Transfer (WP)',
                'description' => 'Intra Zone - Within Zone Transfer',
                'active_status' => true
            ],

        ];

        foreach ($categories as $category) {

            TeacherTransferCategory::updateOrCreate(

                [
                    'transfer_category_name' => $category['transfer_category_name']
                ],

                [
                    'office_level_id' => $category['office_level_id'],
                    'description' => $category['description'],
                    'active_status' => $category['active_status']
                ]

            );
        }
    }
}
