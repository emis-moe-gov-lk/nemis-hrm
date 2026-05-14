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
        Schema::create('transfer_policies', function (Blueprint $table) {
            $table->id();
            $table->string('policy_id', 20)->unique();

            // General Info
            $table->year('policy_year');
            $table->string('circular_number')->unique();
            $table->string('title');
            $table->text('description')->nullable();

            // Service Requirements
            $table->unsignedInteger('min_service_current_school');
            $table->unsignedInteger('min_service_total');

            // Important Dates
            $table->date('effective_date');
            $table->date('application_start_date');
            $table->date('application_end_date');

            // Authority & Type
            $table->string('transfer_authority', 10); // workplace_id
            $table->string('transfer_type'); // annual/mutual/medical etc
            $table->unsignedTinyInteger('max_preferences')->default(5);
            $table->char('service_id', 10)->comment('e.g., POS001, POS002');

            // National School special category consider or not
            $table->boolean('is_ns_category_considered')->default(true)->comment('true: Special Category, false: Normal');

            // Status
            $table->boolean('active_status')->default(true)->comment('true: Active, false: Inactive');
            $table->boolean('is_locked')->default(false)->comment('true: Locked, false: Unlocked');

            // Audit
            $table->string('created_by', 12)->nullable()->index();
            $table->string('updated_by', 12)->nullable()->index();

            $table->timestamps();

            $table->foreign('transfer_authority')->references('workplace_id')->on('workplaces');
            $table->foreign('service_id')->references('service_id')->on('services');
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
        Schema::dropIfExists('transfer_policies');
        Schema::enableForeignKeyConstraints();
    }
};
