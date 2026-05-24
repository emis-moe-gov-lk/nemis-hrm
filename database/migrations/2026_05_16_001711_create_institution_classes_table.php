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
        Schema::create('institution_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_grade_id')->constrained('institution_grades')->onDelete('cascade');
            $table->string('class_name'); // e.g. A, B
            $table->string('medium_id')->nullable(); // FK to medium_of_instructions
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institution_classes');
    }
};
