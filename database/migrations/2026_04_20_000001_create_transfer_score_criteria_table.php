<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_score_criteria', function (Blueprint $table) {
            $table->id();
            $table->string('criteria_id', 20)->unique();
            $table->string('criteria_key', 60)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('display_order')->default(0)->index();
            $table->boolean('active_status')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('transfer_score_criteria');
        Schema::enableForeignKeyConstraints();
    }
};
