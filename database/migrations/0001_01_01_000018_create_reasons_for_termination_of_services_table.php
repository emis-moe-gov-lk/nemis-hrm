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
        Schema::create('reasons_for_termination_of_services', function (Blueprint $table) {
            $table->id();
            $table->char('termination_id', 10)->unique()->index();
            $table->string('reason');
            $table->boolean('active_status')
                ->default(true)
                ->comment('true: Active, false: Inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reasons_for_termination_of_services');
    }
};
