<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Some existing databases were migrated before short_name was added to the
     * base office_levels migration. Keep this as an idempotent compatibility fix.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('office_levels', 'short_name')) {
            Schema::table('office_levels', function (Blueprint $table) {
                $table->string('short_name', 10)
                    ->nullable()
                    ->after('office_level_name')
                    ->comment('e.g., INS, DEO, ZEO, PEO, MOE');
            });
        }
    }

    public function down(): void
    {
        // No-op: short_name is part of the base schema for fresh installs.
    }
};
