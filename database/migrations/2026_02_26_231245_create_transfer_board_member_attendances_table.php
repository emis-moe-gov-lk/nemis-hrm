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
        Schema::create('transfer_board_member_attendances', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->string('tbm_id', 20);

            // Attendance Info
            $table->date('attendance_date');

            $table->enum('attendance_status', ['present', 'absent', 'late'])
                ->default('present');

            $table->text('remarks')->nullable();

            // Status
            $table->boolean('active_status')
                ->default(true)
                ->comment('true: Active, false: Inactive');

            // Audit
            $table->string('created_by', 12)->nullable();
            $table->string('updated_by', 12)->nullable();

            $table->timestamps();

            // indexes
            $table->index('tbm_id', 'tbma_tbm_idx');
            $table->index('attendance_date', 'tbma_date_idx');

            // unique
            $table->unique(['tbm_id', 'attendance_date'], 'tbma_unique');

            // FK
            $table->foreign('tbm_id', 'tbma_member_fk')
                ->references('tbm_id')
                ->on('transfer_board_members')
                ->cascadeOnDelete();

            $table->foreign('created_by', 'tbma_created_fk')
                ->references('people_id')
                ->on('people');

            $table->foreign('updated_by', 'tbma_updated_fk')
                ->references('people_id')
                ->on('people');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_board_member_attendances');
    }
};
