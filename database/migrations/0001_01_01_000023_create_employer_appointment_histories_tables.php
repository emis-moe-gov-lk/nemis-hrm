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
        // 1. Rank History
        Schema::create('employer_appointment_rank_histories', function (Blueprint $table) {
            $table->id();
            $table->char('appointment_id', 12)->index();
            $table->char('employee_id', 12)->index();
            $table->string('ref_letter_no')->nullable();
            $table->char('rank_id', 20)->index();

            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('remarks')->nullable();

            $table->char('created_by', 12)->nullable();
            $table->char('updated_by', 12)->nullable();
            $table->timestamps();

            $table->foreign('appointment_id')->references('appointment_id')->on('employer_appointments')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('employee_id')->references('people_id')->on('people')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('rank_id')->references('rank_id')->on('service_ranks')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('created_by')->references('people_id')->on('people');
            $table->foreign('updated_by')->references('people_id')->on('people');
        });

        // 2. Workplace History
        Schema::create('employer_appointment_workplace_histories', function (Blueprint $table) {
            $table->id();
            $table->char('appointment_id', 12)->index();
            $table->char('employee_id', 12)->index();
            $table->string('ref_letter_no')->nullable();
            $table->char('workplace_id', 20)->index();
            $table->char('office_level_id', 10)->index();

            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('remarks')->nullable();

            $table->char('created_by', 12)->nullable();
            $table->char('updated_by', 12)->nullable();
            $table->timestamps();

            $table->foreign('appointment_id')->references('appointment_id')->on('employer_appointments')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('employee_id')->references('people_id')->on('people')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('office_level_id')->references('office_level_id')->on('office_levels')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('workplace_id')->references('workplace_id')->on('workplaces')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('created_by')->references('people_id')->on('people');
            $table->foreign('updated_by')->references('people_id')->on('people');
        });

        // 3. Position History
        Schema::create('employer_appointment_position_histories', function (Blueprint $table) {
            $table->id();
            $table->char('appointment_id', 12)->index();
            $table->char('employee_id', 12)->index();
            $table->string('ref_letter_no')->nullable();
            $table->char('position_id', 10)->index();

            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('remarks')->nullable();

            $table->char('created_by', 12)->nullable();
            $table->char('updated_by', 12)->nullable();
            $table->timestamps();

            $table->foreign('appointment_id')->references('appointment_id')->on('employer_appointments')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('employee_id')->references('people_id')->on('people')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('position_id')->references('position_id')->on('positions')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('created_by')->references('people_id')->on('people');
            $table->foreign('updated_by')->references('people_id')->on('people');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employer_appointment_position_histories');
        Schema::dropIfExists('employer_appointment_workplace_histories');
        Schema::dropIfExists('employer_appointment_rank_histories');
    }
};
