<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transfer_boards', function (Blueprint $table) {
            if (!Schema::hasColumn('transfer_boards', 'board_type')) {
                $table->string('board_type', 20)
                    ->default('transfer')
                    ->after('bo_workplace_id');

                $table->index('board_type', 'tb_board_type_idx');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transfer_boards', function (Blueprint $table) {
            if (Schema::hasColumn('transfer_boards', 'board_type')) {
                $table->dropIndex('tb_board_type_idx');
                $table->dropColumn('board_type');
            }
        });
    }
};
