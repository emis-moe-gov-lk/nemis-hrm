<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trusted_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('selector', 64)->unique();
            $table->string('token_hash');
            $table->string('device_name')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->dateTime('last_used_at')->nullable();
            $table->dateTime('expires_at');
            $table->dateTime('revoked_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'expires_at']);
            $table->index(['user_id', 'revoked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trusted_devices');
    }
};
