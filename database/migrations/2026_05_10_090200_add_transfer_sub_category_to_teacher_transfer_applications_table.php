<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('teacher_transfer_applications')) {
            return;
        }

        if (! Schema::hasColumn('teacher_transfer_applications', 'transfer_sub_category_id')) {
            Schema::table('teacher_transfer_applications', function (Blueprint $table) {
                $table->string('transfer_sub_category_id', 20)
                    ->nullable()
                    ->after('transfer_category');

                $table->index(
                    ['policy_id', 'transfer_category', 'transfer_sub_category_id'],
                    'tta_policy_category_subcat_idx'
                );

                $table->foreign('transfer_sub_category_id', 'tta_sub_category_fk')
                    ->references('transfer_sub_category_id')
                    ->on('teacher_transfer_sub_categories');
            });
        }

        if (
            ! Schema::hasTable('teacher_transfer_categories')
            || ! Schema::hasColumn('teacher_transfer_categories', 'transfer_sub_category_id')
        ) {
            return;
        }

        DB::table('teacher_transfer_applications as applications')
            ->join('teacher_transfer_categories as categories', 'categories.transfer_category_id', '=', 'applications.transfer_category')
            ->whereNull('applications.transfer_sub_category_id')
            ->whereNotNull('categories.transfer_sub_category_id')
            ->select('applications.id', 'categories.transfer_sub_category_id')
            ->orderBy('applications.id')
            ->chunkById(100, function ($applications) {
                foreach ($applications as $application) {
                    DB::table('teacher_transfer_applications')
                        ->where('id', $application->id)
                        ->update([
                            'transfer_sub_category_id' => $application->transfer_sub_category_id,
                        ]);
                }
            }, 'applications.id', 'id');
    }

    public function down(): void
    {
        if (! Schema::hasTable('teacher_transfer_applications')
            || ! Schema::hasColumn('teacher_transfer_applications', 'transfer_sub_category_id')) {
            return;
        }

        Schema::table('teacher_transfer_applications', function (Blueprint $table) {
            $table->dropForeign('tta_sub_category_fk');
            $table->dropIndex('tta_policy_category_subcat_idx');
            $table->dropColumn('transfer_sub_category_id');
        });
    }
};
