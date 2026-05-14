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
        Schema::create('transfer_reasons', function (Blueprint $table) {

            $table->id();

            $table->string('reason_id', 10)->unique()->index();

            // Display Title
            $table->string('title');

            // Optional Description
            $table->text('description')->nullable();

            // Reason Category Type (optional grouping)
            $table->enum('category', [
                'personal',
                'medical',
                'service',
                'disciplinary',
                'other'
            ])->nullable();

            // Active status
            $table->boolean('is_active')->default(true);

            // Ordering in dropdown
            $table->integer('display_order')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_reasons');
    }
};
