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
        Schema::create('change_logs', function (Blueprint $table) {
            $table->id();
            $table->string('version_id', 10)->index();
            $table->enum('type', ['added', 'fixed', 'modified', 'removed', 'security'])->default('modified');
            $table->string('title');
            $table->string('description');
            $table->string('created_by', 12)->nullable();
            $table->string('updated_by', 12)->nullable();
            $table->timestamps();

            $table->foreign('version_id')->references('version_id')->on('versions')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('change_logs');
    }
};
