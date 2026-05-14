<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_transfer_application_recommendations', function (Blueprint $table) {

            $table->id();

            $table->string('transfer_application_id', 20);
            $table->char('workplace_id', 10);
            $table->char('approved_by', 12)->nullable();
            $table->string('transfer_recommendation_list_id', 10)->nullable();
            $table->text('remarks')->nullable();
            $table->boolean('recommendation_status')->default(false)->comment('true: Recommendation given, false: Pending');
            // Audit
            $table->string('created_by', 12)->nullable();
            $table->string('updated_by', 12)->nullable();
            $table->boolean('active_status')
                ->default(true)
                ->comment('true: Active, false: Inactive');

            $table->timestamps();

            // Short FK names
            $table->foreign('transfer_application_id', 'ttar_app_fk')
                ->references('transfer_application_id')
                ->on('teacher_transfer_applications')
                ->cascadeOnDelete();

            $table->foreign('transfer_recommendation_list_id', 'ttar_list_fk')
                ->references('transfer_recommendation_list_id')
                ->on('teacher_transfer_recommendation_lists');

            $table->foreign('workplace_id', 'ttar_wp_fk')
                ->references('workplace_id')
                ->on('workplaces');

            $table->foreign('approved_by', 'ttar_people_fk')
                ->references('people_id')
                ->on('people');

            $table->foreign('created_by')->references('people_id')->on('people');
            $table->foreign('updated_by')->references('people_id')->on('people');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_transfer_application_recommendations');
    }
};
