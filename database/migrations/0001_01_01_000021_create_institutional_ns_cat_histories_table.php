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
        Schema::create('institutional_ns_cat_histories', function (Blueprint $table) {
            $table->id();
            $table->char('workplace_id', 10)->index()->comment('Office ID');
            $table->string('ns_cat', 1)->comment('National school catogory A, B, C, D,.. N=Non-National School');
            $table->year('effective_year');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('remarks', 255)->nullable();
            $table->timestamps();

            $table->foreign('workplace_id')->references('workplace_id')->on('institutions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institutional_ns_cat_histories');
    }
};
