<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_transfer_application_preferences', function (Blueprint $table) {

            $table->id();

            $table->string('transfer_application_id', 20);
            $table->unsignedTinyInteger('preference_order'); // 1–5

            $table->char('zeo_wp_id', 10);
            $table->char('ins_wp_id', 10);
            $table->decimal('distance', 5, 2)->comment('teacher permanent address to institution distance in km');

            $table->timestamps();

            // Short index name
            $table->index('transfer_application_id', 'ttap_app_idx');

            // Foreign keys with short names
            $table->foreign('transfer_application_id', 'ttap_app_fk')
                ->references('transfer_application_id')
                ->on('teacher_transfer_applications')
                ->cascadeOnDelete();

            $table->foreign('zeo_wp_id', 'ttap_zeo_fk')
                ->references('workplace_id')
                ->on('zonal_education_offices');

            $table->foreign('ins_wp_id', 'ttap_ins_fk')
                ->references('workplace_id')
                ->on('institutions');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_transfer_application_preferences');
    }
};
