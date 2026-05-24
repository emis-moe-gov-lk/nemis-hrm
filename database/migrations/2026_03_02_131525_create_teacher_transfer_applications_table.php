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
        Schema::create('teacher_transfer_applications', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_application_id', 20);
            $table->string('policy_id', 20)->index();
            // Service Details
            $table->char('employee_id', 12)->index();
            $table->char('appointment_id', 12)->index();
            $table->date('first_appointment_date');
            $table->string('current_workplace');
            $table->date('current_workplace_join_date');
            $table->char('cwp_facilities_id', 10)->comment('Current working place facilities id');
            $table->string('ns_cat', 1)->nullable()->comment('National school catogory A, B, C, D,..');
            // Residential Address
            $table->string('permanent_address');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            // Temporary Address
            $table->string('temporary_address')->nullable();
            $table->decimal('temp_latitude', 10, 7)->nullable();
            $table->decimal('temp_longitude', 10, 7)->nullable();
            // Transfer Details
            $table->string('transfer_type');
            $table->string('reason_category', 10);
            // Discipline
            $table->boolean('has_disciplinary_actions')->default(false);
            $table->text('disciplinary_actions_details')->nullable();
            // Category
            $table->string('transfer_category', 50);
            $table->string('target_province');
            // Declaration
            $table->boolean('is_declared')->default(false);
            // Status
            $table->enum('status', ['draft', 'submitted', 'processing', 'approved', 'rejected'])
                ->default('draft');
            $table->integer('current_step')->default(1);
            // Audit
            $table->string('created_by', 12)->nullable();
            $table->string('updated_by', 12)->nullable();
            $table->timestamps();

            // short unique name
            $table->unique('transfer_application_id', 'tta_app_unique');

            $table->foreign('policy_id')->references('policy_id')->on('teacher_transfer_policies');
            $table->foreign('appointment_id')->references('appointment_id')->on('employer_appointments');
            $table->foreign('employee_id')->references('people_id')->on('people');
            $table->foreign('target_province')->references('workplace_id')->on('provincial_education_offices');
            $table->foreign('current_workplace')->references('workplace_id')->on('workplaces');
            $table->foreign('transfer_category')->references('transfer_category_id')->on('teacher_transfer_categories');
            $table->foreign('cwp_facilities_id')->references('facilities_id')->on('institutional_facilities');

            $table->foreign('created_by')->references('people_id')->on('people');
            $table->foreign('updated_by')->references('people_id')->on('people');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('teacher_transfer_applications');
        Schema::enableForeignKeyConstraints();
    }
};
