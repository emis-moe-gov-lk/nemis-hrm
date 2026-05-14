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
        Schema::create('institution_groups', function (Blueprint $table) {
            $table->id();
            $table->char('group_code', 10)->unique();
            $table->string('parent_office_id', 10)->index();
            $table->string('group_name');
            $table->text('group_description')->nullable();
            $table->string('is_assigned', 12)->nullable()->index();
            $table->string('created_by', 12)->nullable()->index();
            $table->string('updated_by', 12)->nullable()->index();
            $table->boolean('active_status')->default(true)->comment('true: Active, false: Inactive');
            $table->timestamps();

            $table->foreign('created_by')->references('people_id')->on('people');
            $table->foreign('updated_by')->references('people_id')->on('people');
            $table->foreign('is_assigned')->references('people_id')->on('people');
            $table->foreign('parent_office_id')->references('workplace_id')->on('workplaces');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institution_groups');
    }
};
