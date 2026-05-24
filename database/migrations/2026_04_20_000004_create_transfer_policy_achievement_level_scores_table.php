<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_policy_achievement_level_scores', function (Blueprint $table) {
            $table->id();
            $table->string('policy_id', 20);
            $table->string('achievement_level', 20);
            $table->decimal('score_per_achievement', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['policy_id', 'achievement_level'], 'tpas_policy_level_unique');
            $table->foreign('policy_id')->references('policy_id')->on('teacher_transfer_policies')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_policy_achievement_level_scores');
    }
};
