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
        Schema::create('grades_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Grade 1, Grade 2...
            $table->integer('order')->default(0);
            $table->boolean('active_status')->default(true);
            $table->timestamps();
        });

        // Seed basic grades
        $grades = [];
        for ($i = 1; $i <= 13; $i++) {
            $grades[] = [
                'name' => 'Grade ' . $i,
                'order' => $i,
                'active_status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('grades_lists')->insert($grades);
    }

    public function down(): void
    {
        Schema::dropIfExists('grades_lists');
    }
};
