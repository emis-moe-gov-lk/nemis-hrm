<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_transfer_recommendation_lists', function (Blueprint $table) {

            $table->id();

            $table->string('transfer_recommendation_list_id', 10);
            $table->char('office_level_id', 10);
            $table->string('decision');

            $table->string('created_by', 12)->nullable();
            $table->string('updated_by', 12)->nullable();

            $table->boolean('active_status')
                ->default(true)
                ->comment('true: Active, false: Inactive');

            $table->timestamps();

            // Short unique index name
            $table->unique('transfer_recommendation_list_id', 'ttrl_id_unique');

            // Short foreign key names
            $table->foreign('office_level_id', 'ttrl_office_fk')
                ->references('office_level_id')
                ->on('office_levels');

            $table->foreign('created_by', 'ttrl_created_fk')
                ->references('people_id')
                ->on('people');

            $table->foreign('updated_by', 'ttrl_updated_fk')
                ->references('people_id')
                ->on('people');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_transfer_recommendation_lists');
    }
};
