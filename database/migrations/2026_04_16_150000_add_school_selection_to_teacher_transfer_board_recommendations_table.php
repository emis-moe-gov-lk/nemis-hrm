<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('teacher_transfer_board_recommendations')) {
            return;
        }

        Schema::table('teacher_transfer_board_recommendations', function (Blueprint $table) {
            if (!Schema::hasColumn('teacher_transfer_board_recommendations', 'school_selection_type')) {
                $table->string('school_selection_type', 20)->nullable();
            }

            if (!Schema::hasColumn('teacher_transfer_board_recommendations', 'selected_zone_id')) {
                $table->char('selected_zone_id', 10)->nullable();
                $table->index('selected_zone_id', 'ttbr_sel_zone_idx');
            }

            if (!Schema::hasColumn('teacher_transfer_board_recommendations', 'selected_school_id')) {
                $table->char('selected_school_id', 10)->nullable();
                $table->index('selected_school_id', 'ttbr_sel_school_idx');
            }

            if (!Schema::hasColumn('teacher_transfer_board_recommendations', 'transfer_effective_date')) {
                $table->date('transfer_effective_date')->nullable();
            }

            if (!Schema::hasColumn('teacher_transfer_board_recommendations', 'rejection_reason')) {
                $table->string('rejection_reason', 80)->nullable();
            }
        });

        Schema::table('teacher_transfer_board_recommendations', function (Blueprint $table) {
            if (Schema::hasColumn('teacher_transfer_board_recommendations', 'selected_zone_id')) {
                $table->foreign('selected_zone_id', 'ttbr_sel_zone_fk')
                    ->references('workplace_id')
                    ->on('zonal_education_offices')
                    ->nullOnDelete();
            }

            if (Schema::hasColumn('teacher_transfer_board_recommendations', 'selected_school_id')) {
                $table->foreign('selected_school_id', 'ttbr_sel_school_fk')
                    ->references('workplace_id')
                    ->on('institutions')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('teacher_transfer_board_recommendations')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('teacher_transfer_board_recommendations', function (Blueprint $table) {
            if (Schema::hasColumn('teacher_transfer_board_recommendations', 'selected_zone_id')) {
                $table->dropForeign('ttbr_sel_zone_fk');
                $table->dropIndex('ttbr_sel_zone_idx');
            }

            if (Schema::hasColumn('teacher_transfer_board_recommendations', 'selected_school_id')) {
                $table->dropForeign('ttbr_sel_school_fk');
                $table->dropIndex('ttbr_sel_school_idx');
            }
        });

        Schema::table('teacher_transfer_board_recommendations', function (Blueprint $table) {
            $columns = [
                'school_selection_type',
                'selected_zone_id',
                'selected_school_id',
                'transfer_effective_date',
                'rejection_reason',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('teacher_transfer_board_recommendations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
