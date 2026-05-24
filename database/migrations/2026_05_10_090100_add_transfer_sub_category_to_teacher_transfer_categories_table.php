<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teacher_transfer_categories', function (Blueprint $table) {
            $table->string('transfer_sub_category_id', 20)
                ->nullable()
                ->after('office_level_id');

            $table->index(
                ['policy_id', 'office_level_id', 'transfer_sub_category_id'],
                'ttc_policy_office_subcat_idx'
            );

            $table->foreign('transfer_sub_category_id', 'ttc_sub_category_fk')
                ->references('transfer_sub_category_id')
                ->on('teacher_transfer_sub_categories');
        });

        DB::table('teacher_transfer_categories')
            ->whereNull('transfer_sub_category_id')
            ->orderBy('id')
            ->get(['id', 'office_level_id', 'transfer_category_name'])
            ->each(function ($category): void {
                $name = strtolower((string) $category->transfer_category_name);

                $code = match (true) {
                    $category->office_level_id === 'OLID004' => 'inter_zone',
                    str_contains($name, 'national school') => 'national_school',
                    str_contains($name, 'province') => 'another_province',
                    $category->office_level_id === 'OLID003' => 'another_zone',
                    default => null,
                };

                if ($code) {
                    $subCategoryId = DB::table('teacher_transfer_sub_categories')
                        ->where('code', $code)
                        ->value('transfer_sub_category_id');

                    if ($subCategoryId) {
                        DB::table('teacher_transfer_categories')
                            ->where('id', $category->id)
                            ->update(['transfer_sub_category_id' => $subCategoryId]);
                    }
                }
            });
    }

    public function down(): void
    {
        Schema::table('teacher_transfer_categories', function (Blueprint $table) {
            $table->dropForeign('ttc_sub_category_fk');
            $table->dropIndex('ttc_policy_office_subcat_idx');
            $table->dropColumn('transfer_sub_category_id');
        });
    }
};
