<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_transfer_score_route_distances', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_application_id', 20);
            $table->string('current_workplace_id', 20)->nullable();
            $table->decimal('origin_latitude', 10, 7);
            $table->decimal('origin_longitude', 10, 7);
            $table->decimal('destination_latitude', 10, 7);
            $table->decimal('destination_longitude', 10, 7);
            $table->string('route_hash', 64)->unique();
            $table->decimal('distance_km', 10, 2);
            $table->string('provider', 40)->default('osrm');
            $table->timestamp('calculated_at')->nullable();
            $table->timestamps();

            $table->index(['transfer_application_id', 'current_workplace_id'], 'ttsrc_application_workplace_index');
            $table->foreign('transfer_application_id', 'ttsrc_application_fk')
                ->references('transfer_application_id')
                ->on('teacher_transfer_applications')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_transfer_score_route_distances');
    }
};
