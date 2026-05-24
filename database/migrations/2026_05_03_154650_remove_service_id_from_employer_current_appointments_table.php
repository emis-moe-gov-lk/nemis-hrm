<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('employer_current_appointments') || ! Schema::hasColumn('employer_current_appointments', 'service_id')) {
            return;
        }

        $foreignKeyExists = $this->foreignKeyExists('employer_current_appointments_service_id_foreign');

        Schema::table('employer_current_appointments', function (Blueprint $table) use ($foreignKeyExists) {
            if ($foreignKeyExists) {
                $table->dropForeign(['service_id']);
            }

            $table->dropColumn('service_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('employer_current_appointments') || Schema::hasColumn('employer_current_appointments', 'service_id')) {
            return;
        }

        Schema::table('employer_current_appointments', function (Blueprint $table) {
            $table->string('service_id')->nullable()->after('appoint_date');
        });

        try {
            Schema::table('employer_current_appointments', function (Blueprint $table) {
                $table->foreign('service_id')
                    ->references('service_id')
                    ->on('services')
                    ->onUpdate('cascade')
                    ->onDelete('no action');
            });
        } catch (\Throwable) {
            // Skip re-adding the foreign key on connections that cannot support it during tests.
        }
    }

    private function foreignKeyExists(string $constraintName): bool
    {
        if (! in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true)) {
            return false;
        }

        return DB::table('information_schema.table_constraints')
            ->where('constraint_schema', DB::getDatabaseName())
            ->where('table_name', 'employer_current_appointments')
            ->where('constraint_name', $constraintName)
            ->where('constraint_type', 'FOREIGN KEY')
            ->exists();
    }
};
