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
        Schema::create('teacher_transfer_policy_steps', function (Blueprint $table) {
            $table->id();
            $table->string('policy_id', 20);

            $table->string('office_level_id', 10);
            $table->unsignedInteger('step_order');

            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();

            $table->foreign('policy_id')->references('policy_id')->on('teacher_transfer_policies')->cascadeOnDelete();
            $table->foreign('office_level_id')->references('office_level_id')->on('office_levels');
            $table->unique(['policy_id', 'office_level_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_transfer_policy_steps');
    }
};
