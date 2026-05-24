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
        if (Schema::hasColumn('employer_current_appointments', 'is_released_to_pool')) {
            return;
        }

        Schema::table('employer_current_appointments', function (Blueprint $table) {
            $table->boolean('is_released_to_pool')->default(0)->after('workplace_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('employer_current_appointments', 'is_released_to_pool')) {
            return;
        }

        Schema::table('employer_current_appointments', function (Blueprint $table) {
            $table->dropColumn('is_released_to_pool');
        });
    }
};
