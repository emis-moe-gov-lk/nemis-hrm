<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_policy_facility_score_rules', function (Blueprint $table) {
            $table->id();
            $table->string('policy_id', 20);
            $table->string('criteria_key', 60);
            $table->char('facilities_id', 10);
            $table->decimal('score_per_year', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['policy_id', 'criteria_key', 'facilities_id'], 'tpfsr_policy_criteria_facility_unique');
            $table->foreign('policy_id')->references('policy_id')->on('transfer_policies')->cascadeOnDelete();
            $table->foreign('criteria_key')->references('criteria_key')->on('transfer_score_criteria')->cascadeOnDelete();
            $table->foreign('facilities_id')->references('facilities_id')->on('institutional_facilities')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_policy_facility_score_rules');
    }
};
