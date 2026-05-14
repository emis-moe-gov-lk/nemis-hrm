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
        Schema::create('teacher_transfer_board_recommendation_lists', function (Blueprint $table) {
            $table->id();
            $table->string('ttbr_list_id', 10)->unique();
            $table->string('decision');
            $table->string('created_by', 12)->nullable();
            $table->string('updated_by', 12)->nullable();
            $table->boolean('active_status')
                ->default(true)
                ->comment('true: Active, false: Inactive');
            $table->timestamps();

            // UNIQUE FK NAMES
            $table->foreign('created_by', 'ttbrl_created_fk')
                ->references('people_id')
                ->on('people')
                ->nullOnDelete();

            $table->foreign('updated_by', 'ttbrl_updated_fk')
                ->references('people_id')
                ->on('people')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_transfer_board_recommendation_lists');
    }
};
