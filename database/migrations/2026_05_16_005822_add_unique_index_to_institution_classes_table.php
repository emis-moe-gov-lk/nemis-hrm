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
        Schema::table('institution_classes', function (Blueprint $table) {
            $table->unique(['institution_grade_id', 'class_name'], 'grade_class_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('institution_classes', function (Blueprint $table) {
            $table->dropUnique('grade_class_unique');
        });
    }
};
