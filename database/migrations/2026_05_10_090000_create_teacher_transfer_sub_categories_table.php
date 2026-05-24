<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_transfer_sub_categories', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_sub_category_id', 20)->unique('ttsc_id_unique');
            $table->string('code', 50)->unique('ttsc_code_unique');
            $table->string('name');
            $table->text('description')->nullable();
            $table->char('policy_office_level_id', 10);
            $table->char('first_board_office_level_id', 10);
            $table->char('second_board_office_level_id', 10)->nullable();
            $table->boolean('requires_target_province_selection')->default(false);
            $table->string('zone_scope_mode', 50);
            $table->string('institution_scope_mode', 50);
            $table->boolean('active_status')->default(true);
            $table->unsignedSmallInteger('display_order')->default(1);
            $table->timestamps();

            $table->foreign('policy_office_level_id', 'ttsc_policy_office_fk')
                ->references('office_level_id')
                ->on('office_levels');

            $table->foreign('first_board_office_level_id', 'ttsc_first_board_office_fk')
                ->references('office_level_id')
                ->on('office_levels');

            $table->foreign('second_board_office_level_id', 'ttsc_second_board_office_fk')
                ->references('office_level_id')
                ->on('office_levels');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_transfer_sub_categories');
    }
};
