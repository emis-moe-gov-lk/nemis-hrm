<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('teacher_transfer_boards')) {
            return;
        }

        if (! Schema::hasColumn('teacher_transfer_boards', 'transfer_sub_category_id')) {
            Schema::table('teacher_transfer_boards', function (Blueprint $table) {
                $table->string('transfer_sub_category_id', 20)
                    ->nullable()
                    ->after('transfer_category_id');
            });
        }

        if (! Schema::hasColumn('teacher_transfer_boards', 'board_stage')) {
            Schema::table('teacher_transfer_boards', function (Blueprint $table) {
                $table->string('board_stage', 20)
                    ->nullable()
                    ->after('board_type');

                $table->index(
                    ['policy_id', 'transfer_category_id', 'transfer_sub_category_id', 'board_stage'],
                    'ttb_policy_category_subcat_stage_idx'
                );

                $table->foreign('transfer_sub_category_id', 'ttb_sub_category_fk')
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

        DB::table('teacher_transfer_boards as boards')
            ->join('teacher_transfer_categories as categories', 'categories.transfer_category_id', '=', 'boards.transfer_category_id')
            ->whereNull('boards.transfer_sub_category_id')
            ->whereNotNull('categories.transfer_sub_category_id')
            ->select('boards.id', 'categories.transfer_sub_category_id')
            ->orderBy('boards.id')
            ->chunkById(100, function ($boards) {
                foreach ($boards as $board) {
                    DB::table('teacher_transfer_boards')
                        ->where('id', $board->id)
                        ->update([
                            'transfer_sub_category_id' => $board->transfer_sub_category_id,
                        ]);
                }
            }, 'boards.id', 'id');

        DB::table('teacher_transfer_boards')
            ->whereNull('board_stage')
            ->update([
                'board_stage' => DB::raw("CASE WHEN board_type = 'appeal' THEN 'peo' ELSE 'peo' END"),
            ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('teacher_transfer_boards')) {
            return;
        }

        Schema::table('teacher_transfer_boards', function (Blueprint $table) {
            if (Schema::hasColumn('teacher_transfer_boards', 'transfer_sub_category_id')) {
                $table->dropForeign('ttb_sub_category_fk');
                $table->dropIndex('ttb_policy_category_subcat_stage_idx');
                $table->dropColumn('transfer_sub_category_id');
            }

            if (Schema::hasColumn('teacher_transfer_boards', 'board_stage')) {
                $table->dropColumn('board_stage');
            }
        });
    }
};
