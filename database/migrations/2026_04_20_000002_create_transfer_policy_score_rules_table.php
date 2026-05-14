<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_policy_score_rules', function (Blueprint $table) {
            $table->id();
            $table->string('policy_id', 20);
            $table->string('criteria_key', 60);
            $table->decimal('score_per_unit', 10, 2)->nullable();
            $table->decimal('base_value', 10, 2)->nullable();
            $table->boolean('active_status')->default(true)->index();
            $table->timestamps();

            $table->unique(['policy_id', 'criteria_key'], 'tpsr_policy_criteria_unique');
            $table->foreign('policy_id')->references('policy_id')->on('transfer_policies')->cascadeOnDelete();
            $table->foreign('criteria_key')->references('criteria_key')->on('transfer_score_criteria')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_policy_score_rules');
    }
};
