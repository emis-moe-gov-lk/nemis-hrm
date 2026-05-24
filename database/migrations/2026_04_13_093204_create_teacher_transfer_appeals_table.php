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
        Schema::create('teacher_transfer_appeals', function (Blueprint $table) {

            $table->id();

            // Unique Appeal ID
            $table->string('appeal_id', 20)->unique('tta_appeal_id_unique');

            // Relations
            $table->string('transfer_application_id', 20);
            $table->string('policy_id', 20);
            $table->unsignedSmallInteger('number_of_appeals')->default(1);

            // Appeal Details
            $table->text('appeal_reason');
            $table->text('appeal_remarks')->nullable();

            // Status
            $table->enum('appeal_status', ['pending', 'approved', 'rejected'])
                ->default('pending');

            // Optional: decision note
            $table->text('decision_remarks')->nullable();

            // Status flag
            $table->boolean('active_status')
                ->default(true)
                ->comment('true: Active, false: Inactive');

            // Audit
            $table->string('created_by', 12)->nullable();
            $table->string('updated_by', 12)->nullable();

            $table->timestamps();

            // Keep each appeal sequence unique per application while allowing multiple appeals.
            $table->unique(['transfer_application_id', 'number_of_appeals'], 'tt_appeals_app_no_unique');

            // Foreign Keys (short names)
            $table->foreign('transfer_application_id', 'tta_app_fk')
                ->references('transfer_application_id')
                ->on('teacher_transfer_applications')
                ->cascadeOnDelete();

            $table->foreign('policy_id', 'tta_policy_fk')
                ->references('policy_id')
                ->on('teacher_transfer_policies');

            $table->foreign('created_by', 'tta_created_fk')
                ->references('people_id')
                ->on('people');

            $table->foreign('updated_by', 'tta_updated_fk')
                ->references('people_id')
                ->on('people');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_transfer_appeals');
    }
};
