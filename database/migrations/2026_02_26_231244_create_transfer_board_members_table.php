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
        Schema::create('transfer_board_members', function (Blueprint $table) {

            $table->id();

            // Unique member ID (optional but useful)
            $table->string('tbm_id', 20)->unique('tbm_id_unique');

            // Relations
            $table->string('board_id', 20);

            $table->string('people_id', 12);

            $table->string('association')->nullable();

            // Role in board
            $table->string('role')->comment('Chairman, Member, Secretary');

            // Status
            $table->boolean('active_status')
                ->default(true)
                ->comment('true: Active, false: Inactive');

            // Audit
            $table->string('created_by', 12)->nullable();
            $table->string('updated_by', 12)->nullable();

            $table->timestamps();

            $table->foreign('board_id', 'tbm_board_fk')
                ->references('board_id')
                ->on('transfer_boards');

            // composite unique (IMPORTANT)
            $table->unique(['board_id', 'people_id'], 'tbm_board_people_unique');

            $table->foreign('people_id', 'tbm_people_fk')
                ->references('people_id')
                ->on('people');

            $table->foreign('created_by', 'tbm_created_fk')
                ->references('people_id')
                ->on('people');

            $table->foreign('updated_by', 'tbm_updated_fk')
                ->references('people_id')
                ->on('people');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_board_members');
    }
};
