<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deleted_users_archive', function (Blueprint $table) {

            $table->bigIncrements('id');

            // Original user references
            $table->unsignedBigInteger('original_user_id');
            $table->string('people_id')->nullable();

            // JSON snapshots
            $table->json('user_data');
            $table->json('related_data')->nullable();

            // Deletion metadata
            $table->string('delete_reason')->nullable();
            $table->text('delete_note')->nullable();

            // Audit info
            $table->string('deleted_by');
            $table->string('deleted_ip')->nullable();

            // Restore tracking
            $table->boolean('is_restored')->default(false);
            $table->timestamp('restored_at')->nullable();

            // Optional integrity hash (recommended)
            $table->string('data_hash', 64)->nullable();

            // Timestamps (explicit)
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            // Indexes
            $table->index('original_user_id');
            $table->index('people_id');
            $table->index('deleted_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deleted_users_archive');
    }
};