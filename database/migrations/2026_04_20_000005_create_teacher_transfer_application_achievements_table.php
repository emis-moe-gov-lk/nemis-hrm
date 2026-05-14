<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_transfer_application_achievements', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_application_id', 20);
            $table->string('achievement_type', 20);
            $table->string('achievement_level', 20);
            $table->string('title');
            $table->string('event_name')->nullable();
            $table->date('achievement_date')->nullable();
            $table->text('details')->nullable();
            $table->text('contribution_details')->nullable();
            $table->boolean('is_included')->default(true)->index();
            $table->text('review_remarks')->nullable();
            $table->string('reviewed_by', 12)->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['transfer_application_id', 'achievement_level'], 'ttaa_application_level_index');
            $table->foreign('transfer_application_id', 'ttaa_application_fk')
                ->references('transfer_application_id')
                ->on('teacher_transfer_applications')
                ->cascadeOnDelete();
            $table->foreign('reviewed_by')->references('people_id')->on('people')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_transfer_application_achievements');
    }
};
