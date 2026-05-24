<?php

namespace Database\Seeders;

use App\Models\TeacherTransferSubCategory;
use App\Support\Transfer\TransferSubCategoryRules;
use Illuminate\Database\Seeder;

class TeacherTransferSubCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (TransferSubCategoryRules::buildRows() as $row) {
            TeacherTransferSubCategory::updateOrCreate(
                ['code' => $row['code']],
                $row
            );
        }
    }
}
