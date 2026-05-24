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
        Schema::create('institution_student_admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_class_id')->constrained('institution_classes')->onDelete('cascade');
            $table->integer('academic_year');
            $table->integer('male_count')->default(0);
            $table->integer('female_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institution_student_admissions');
    }
};
