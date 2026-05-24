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
        Schema::table('positions', function (Blueprint $table) {
            if (!Schema::hasColumn('positions', 'position_order')) {
                $table->smallInteger('position_order')->default(0)->after('service_id')->comment('e.g., 1, 2, 3, 4, 5, 6, 7, 8, 9, 10');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            if (Schema::hasColumn('positions', 'position_order')) {
                $table->dropColumn('position_order');
            }
        });
    }
};
