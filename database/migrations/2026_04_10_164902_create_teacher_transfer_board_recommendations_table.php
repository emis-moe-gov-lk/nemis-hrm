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
        Schema::create('teacher_transfer_board_recommendations', function (Blueprint $table) {

            $table->id();

            $table->string('transfer_application_id', 20);

            $table->string('transfer_board_id', 20)->nullable();

            $table->string('ttbr_list_id', 10)->nullable();

            $table->text('recommendation_remarks')->nullable();

            $table->enum('recommendation_status', ['approved', 'rejected', 'pending'])
                ->default('pending');

            $table->string('created_by', 12)->nullable();
            $table->string('updated_by', 12)->nullable();

            $table->boolean('active_status')
                ->default(true)
                ->comment('true: Active, false: Inactive');

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            // UNIQUE NAME (avoid SQLite conflict)
            $table->unique('transfer_application_id', 'ttbr_app_unique');

            /*
            |--------------------------------------------------------------------------
            | Foreign Keys
            |--------------------------------------------------------------------------
            */

            $table->foreign('transfer_application_id', 'ttbr_app_fk')
                ->references('transfer_application_id')
                ->on('teacher_transfer_applications')
                ->cascadeOnDelete();

            $table->foreign('transfer_board_id', 'ttbr_board_fk')
                ->references('board_id')
                ->on('transfer_boards');

            $table->foreign('ttbr_list_id', 'ttbr_list_fk')
                ->references('ttbr_list_id')
                ->on('teacher_transfer_board_recommendation_lists');

            $table->foreign('created_by', 'ttbr_created_fk')
                ->references('people_id')
                ->on('people');

            $table->foreign('updated_by', 'ttbr_updated_fk')
                ->references('people_id')
                ->on('people');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_transfer_board_recommendations');
    }
};
