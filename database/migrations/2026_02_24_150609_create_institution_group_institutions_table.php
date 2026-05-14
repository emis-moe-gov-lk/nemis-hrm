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
        Schema::create('institution_group_institutions', function (Blueprint $table) {
            $table->id();
            $table->char('group_code', 10)->index();
            $table->char('institution_id', 10)->index()->comment('Institution ID');
            $table->string('created_by', 12)->nullable()->index();
            $table->string('updated_by', 12)->nullable()->index();

            $table->timestamps();

            $table->foreign('group_code')->references('group_code')->on('institution_groups')->onDelete('cascade');
            $table->foreign('institution_id')->references('workplace_id')->on('institutions');
            $table->foreign('created_by')->references('people_id')->on('people');
            $table->foreign('updated_by')->references('people_id')->on('people');

            $table->unique(['group_code', 'institution_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institution_group_institutions');
    }
};
