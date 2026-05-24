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
        Schema::create('teacher_transfer_board_subjects', function (Blueprint $table) {
            $table->id();

            $table->string('board_id', 20);
            $table->string('subject_id', 10);

            $table->boolean('active_status')
                ->default(true)
                ->comment('true: Active, false: Inactive');

            $table->string('created_by', 12)->nullable();
            $table->string('updated_by', 12)->nullable();

            $table->timestamps();

            $table->unique(['board_id', 'subject_id'], 'tbs_board_subject_unique');
            $table->index(['board_id', 'active_status'], 'tbs_board_status_index');
            $table->index('subject_id', 'tbs_subject_index');

            $table->foreign('board_id', 'tbs_board_fk')
                ->references('board_id')
                ->on('teacher_transfer_boards')
                ->cascadeOnDelete();

            $table->foreign('subject_id', 'tbs_subject_fk')
                ->references('subject_id')
                ->on('subject_lists');

            $table->foreign('created_by', 'tbs_created_fk')
                ->references('people_id')
                ->on('people');

            $table->foreign('updated_by', 'tbs_updated_fk')
                ->references('people_id')
                ->on('people');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_transfer_board_subjects');
    }
};
