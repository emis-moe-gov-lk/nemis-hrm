<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TransferReason;

class TransferReasonSeeder extends Seeder
{
    public function run(): void
    {
        $reasons = [

            /*
            |--------------------------------------------------------------------------
            | MEDICAL GROUNDS
            |--------------------------------------------------------------------------
            */

            [
                'title' => 'Medical Grounds (Self/Dependents)',
                'description' => 'Transfer requested due to serious medical condition of the teacher or immediate dependents requiring relocation for treatment.',
                'category' => 'medical',
                'display_order' => 1,
            ],
            [
                'title' => 'Long-Term Medical Treatment Requirement',
                'description' => 'Transfer required due to ongoing long-term medical treatment in a specific district or province.',
                'category' => 'medical',
                'display_order' => 2,
            ],
            [
                'title' => 'Disability or Special Needs Condition',
                'description' => 'Transfer requested due to physical disability or special medical condition requiring accessible facilities.',
                'category' => 'medical',
                'display_order' => 3,
            ],

            /*
            |--------------------------------------------------------------------------
            | PERSONAL / FAMILY
            |--------------------------------------------------------------------------
            */

            [
                'title' => 'Spouse Employment Change',
                'description' => 'Transfer requested due to change in employment location of spouse.',
                'category' => 'personal',
                'display_order' => 4,
            ],
            [
                'title' => 'Marriage',
                'description' => 'Transfer requested due to marriage and relocation to spouse’s area.',
                'category' => 'personal',
                'display_order' => 5,
            ],
            [
                'title' => 'Divorce / Legal Separation',
                'description' => 'Transfer requested due to change in family circumstances following divorce or separation.',
                'category' => 'personal',
                'display_order' => 6,
            ],
            [
                'title' => 'Elderly Parent Care',
                'description' => 'Transfer requested to provide care for elderly or dependent parents.',
                'category' => 'personal',
                'display_order' => 7,
            ],
            [
                'title' => "Children's Education",
                'description' => 'Transfer requested to facilitate better educational opportunities for children.',
                'category' => 'personal',
                'display_order' => 8,
            ],
            [
                'title' => 'Safety / Security Concerns',
                'description' => 'Transfer requested due to safety, social conflict, or personal security concerns.',
                'category' => 'personal',
                'display_order' => 9,
            ],

            /*
            |--------------------------------------------------------------------------
            | SERVICE RELATED
            |--------------------------------------------------------------------------
            */

            [
                'title' => 'Completion of Required Service Period',
                'description' => 'Transfer requested after completing mandatory service period at current station.',
                'category' => 'service',
                'display_order' => 10,
            ],
            [
                'title' => 'Completion of Difficult Area Service',
                'description' => 'Transfer requested after completing service in a difficult or remote area school.',
                'category' => 'service',
                'display_order' => 11,
            ],
            [
                'title' => 'Transport Difficulty',
                'description' => 'Transfer requested due to significant commuting hardship or lack of transport facilities.',
                'category' => 'service',
                'display_order' => 12,
            ],
            [
                'title' => 'Excess Staff Adjustment',
                'description' => 'Transfer required due to excess staff situation at the current school.',
                'category' => 'service',
                'display_order' => 13,
            ],
            [
                'title' => 'Subject Requirement Adjustment',
                'description' => 'Transfer required to address subject teacher shortage in another institution.',
                'category' => 'service',
                'display_order' => 14,
            ],
            [
                'title' => 'Promotion / Grade Upgrade',
                'description' => 'Transfer due to promotion or appointment to higher responsibility position.',
                'category' => 'service',
                'display_order' => 15,
            ],

            /*
            |--------------------------------------------------------------------------
            | ADMINISTRATIVE
            |--------------------------------------------------------------------------
            */

            [
                'title' => 'Administrative Requirement',
                'description' => 'Transfer initiated due to institutional restructuring or administrative necessity.',
                'category' => 'service',
                'display_order' => 16,
            ],

            /*
            |--------------------------------------------------------------------------
            | SPECIAL CONDITIONS (Sri Lanka Context)
            |--------------------------------------------------------------------------
            */

            [
                'title' => 'Natural Disaster Impact',
                'description' => 'Transfer requested due to relocation following natural disasters such as floods or landslides.',
                'category' => 'other',
                'display_order' => 17,
            ],
            [
                'title' => 'Health Risk Area (Epidemic / Environmental)',
                'description' => 'Transfer requested due to confirmed environmental or health risk conditions.',
                'category' => 'other',
                'display_order' => 18,
            ],
            [
                'title' => 'Other Reasons',
                'description' => 'Transfer requested for valid reasons not covered under predefined categories.',
                'category' => 'other',
                'display_order' => 19,
            ],
        ];

        foreach ($reasons as $reason) {
            TransferReason::updateOrCreate(
                ['title' => $reason['title']],
                $reason
            );
        }
    }
}
