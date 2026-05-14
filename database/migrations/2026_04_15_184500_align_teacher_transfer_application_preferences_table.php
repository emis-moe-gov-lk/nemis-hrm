<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('teacher_transfer_application_preferences')) {
            return;
        }

        Schema::table('teacher_transfer_application_preferences', function (Blueprint $table) {
            if (!Schema::hasColumn('teacher_transfer_application_preferences', 'distance')) {
                $table->decimal('distance', 5, 2)
                    ->nullable()
                    ->after('ins_wp_id')
                    ->comment('teacher permanent address to institution distance in km');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('teacher_transfer_application_preferences')) {
            return;
        }

        Schema::table('teacher_transfer_application_preferences', function (Blueprint $table) {
            if (Schema::hasColumn('teacher_transfer_application_preferences', 'distance')) {
                $table->dropColumn('distance');
            }
        });
    }
};
