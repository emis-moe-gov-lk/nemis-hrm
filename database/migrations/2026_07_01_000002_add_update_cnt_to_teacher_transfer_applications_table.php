<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('teacher_transfer_applications')
            || Schema::hasColumn('teacher_transfer_applications', 'update_cnt')) {
            return;
        }

        Schema::table('teacher_transfer_applications', function (Blueprint $table) {
            $table->unsignedInteger('update_cnt')
                ->default(0)
                ->after('current_step');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('teacher_transfer_applications')
            || ! Schema::hasColumn('teacher_transfer_applications', 'update_cnt')) {
            return;
        }

        Schema::table('teacher_transfer_applications', function (Blueprint $table) {
            $table->dropColumn('update_cnt');
        });
    }
};
