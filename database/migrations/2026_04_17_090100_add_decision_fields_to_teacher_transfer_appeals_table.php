<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teacher_transfer_appeals', function (Blueprint $table) {
            if (!Schema::hasColumn('teacher_transfer_appeals', 'appeal_board_id')) {
                $table->string('appeal_board_id', 20)->nullable()->after('number_of_appeals');
                $table->index('appeal_board_id', 'tta_appeal_board_idx');
            }

            if (!Schema::hasColumn('teacher_transfer_appeals', 'school_selection_type')) {
                $table->string('school_selection_type', 20)->nullable()->after('decision_remarks');
            }

            if (!Schema::hasColumn('teacher_transfer_appeals', 'selected_zone_id')) {
                $table->char('selected_zone_id', 10)->nullable()->after('school_selection_type');
                $table->index('selected_zone_id', 'tta_sel_zone_idx');
            }

            if (!Schema::hasColumn('teacher_transfer_appeals', 'selected_school_id')) {
                $table->char('selected_school_id', 10)->nullable()->after('selected_zone_id');
                $table->index('selected_school_id', 'tta_sel_school_idx');
            }

            if (!Schema::hasColumn('teacher_transfer_appeals', 'transfer_effective_date')) {
                $table->date('transfer_effective_date')->nullable()->after('selected_school_id');
            }

            if (!Schema::hasColumn('teacher_transfer_appeals', 'rejection_reason')) {
                $table->string('rejection_reason', 80)->nullable()->after('transfer_effective_date');
            }

            $table->foreign('appeal_board_id', 'tta_appeal_board_fk')
                ->references('board_id')
                ->on('teacher_transfer_boards');

            $table->foreign('selected_zone_id', 'tta_sel_zone_fk')
                ->references('workplace_id')
                ->on('workplaces');

            $table->foreign('selected_school_id', 'tta_sel_school_fk')
                ->references('workplace_id')
                ->on('institutions');
        });
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('teacher_transfer_appeals', function (Blueprint $table) {
            if (Schema::hasColumn('teacher_transfer_appeals', 'appeal_board_id')) {
                $table->dropForeign('tta_appeal_board_fk');
            }

            if (Schema::hasColumn('teacher_transfer_appeals', 'selected_zone_id')) {
                $table->dropForeign('tta_sel_zone_fk');
            }

            if (Schema::hasColumn('teacher_transfer_appeals', 'selected_school_id')) {
                $table->dropForeign('tta_sel_school_fk');
            }

            if (Schema::hasColumn('teacher_transfer_appeals', 'appeal_board_id')) {
                $table->dropIndex('tta_appeal_board_idx');
            }

            if (Schema::hasColumn('teacher_transfer_appeals', 'selected_zone_id')) {
                $table->dropIndex('tta_sel_zone_idx');
            }

            if (Schema::hasColumn('teacher_transfer_appeals', 'selected_school_id')) {
                $table->dropIndex('tta_sel_school_idx');
            }

            $columns = array_values(array_filter([
                Schema::hasColumn('teacher_transfer_appeals', 'appeal_board_id') ? 'appeal_board_id' : null,
                Schema::hasColumn('teacher_transfer_appeals', 'school_selection_type') ? 'school_selection_type' : null,
                Schema::hasColumn('teacher_transfer_appeals', 'selected_zone_id') ? 'selected_zone_id' : null,
                Schema::hasColumn('teacher_transfer_appeals', 'selected_school_id') ? 'selected_school_id' : null,
                Schema::hasColumn('teacher_transfer_appeals', 'transfer_effective_date') ? 'transfer_effective_date' : null,
                Schema::hasColumn('teacher_transfer_appeals', 'rejection_reason') ? 'rejection_reason' : null,
            ]));

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
