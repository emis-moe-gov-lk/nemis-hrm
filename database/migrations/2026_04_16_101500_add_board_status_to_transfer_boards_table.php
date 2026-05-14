<?php

use App\Models\TransferBoard;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('transfer_boards')) {
            return;
        }

        if (!Schema::hasColumn('transfer_boards', 'board_status')) {
            Schema::table('transfer_boards', function (Blueprint $table) {
                $table->string('board_status', 20)
                    ->default(TransferBoard::STATUS_ON_PROGRESS)
                    ->after('end_date');

                $table->index('board_status', 'tb_board_status_index');
            });
        }

        DB::table('transfer_boards')
            ->whereNull('board_status')
            ->update(['board_status' => TransferBoard::STATUS_ON_PROGRESS]);
    }

    public function down(): void
    {
        if (!Schema::hasTable('transfer_boards') || !Schema::hasColumn('transfer_boards', 'board_status')) {
            return;
        }

        Schema::table('transfer_boards', function (Blueprint $table) {
            $table->dropIndex('tb_board_status_index');
            $table->dropColumn('board_status');
        });
    }
};
