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
        Schema::create('institutional_facility_histories', function (Blueprint $table) {
            $table->id();
            $table->char('workplace_id', 10)->index()->comment('Office ID');
            $table->char('facilities_id', 10)->comment('Foreign key referencing institutional_facilities table');
            $table->year('effective_year');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('remarks', 255)->nullable();

            $table->timestamps();

            $table->foreign('workplace_id')->references('workplace_id')->on('institutions');
            $table->foreign('facilities_id')->references('facilities_id')->on('institutional_facilities');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institutional_facility_histories');
    }
};
