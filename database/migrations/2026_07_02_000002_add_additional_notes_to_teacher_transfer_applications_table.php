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
            || Schema::hasColumn('teacher_transfer_applications', 'additional_notes')) {
            return;
        }

        Schema::table('teacher_transfer_applications', function (Blueprint $table) {
            $table->text('additional_notes')
                ->nullable()
                ->after('disciplinary_actions_details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('teacher_transfer_applications')
            || ! Schema::hasColumn('teacher_transfer_applications', 'additional_notes')) {
            return;
        }

        Schema::table('teacher_transfer_applications', function (Blueprint $table) {
            $table->dropColumn('additional_notes');
        });
    }
};
