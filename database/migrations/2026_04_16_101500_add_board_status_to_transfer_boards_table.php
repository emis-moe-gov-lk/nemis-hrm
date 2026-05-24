<?php

use App\Models\TeacherTransferBoard;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('teacher_transfer_boards')) {
            return;
        }

        if (!Schema::hasColumn('teacher_transfer_boards', 'board_status')) {
            Schema::table('teacher_transfer_boards', function (Blueprint $table) {
                $table->string('board_status', 20)
                    ->default(TeacherTransferBoard::STATUS_ON_PROGRESS)
                    ->after('end_date');

                $table->index('board_status', 'tb_board_status_index');
            });
        }

        DB::table('teacher_transfer_boards')
            ->whereNull('board_status')
            ->update(['board_status' => TeacherTransferBoard::STATUS_ON_PROGRESS]);
    }

    public function down(): void
    {
        if (!Schema::hasTable('teacher_transfer_boards') || !Schema::hasColumn('teacher_transfer_boards', 'board_status')) {
            return;
        }

        Schema::table('teacher_transfer_boards', function (Blueprint $table) {
            $table->dropIndex('tb_board_status_index');
            $table->dropColumn('board_status');
        });
    }
};
