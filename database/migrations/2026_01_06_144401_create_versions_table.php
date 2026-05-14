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
        Schema::create('versions', function (Blueprint $table) {
            $table->id();
            $table->string('version_id', 10)->unique()->index();
            $table->string('version', 10);
            $table->date('release_date');
            $table->string('title');
            $table->string('description');
            $table->boolean('is_latest')->default(true)->comment('true: Latest, false: Old');
            $table->string('created_by', 12)->nullable();
            $table->string('updated_by', 12)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('versions');
    }
};
