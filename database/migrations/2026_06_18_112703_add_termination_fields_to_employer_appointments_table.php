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
        // Only modify if resign_date exists (meaning it's the old schema)
        if (Schema::hasColumn('employer_appointments', 'resign_date')) {
            Schema::table('employer_appointments', function (Blueprint $table) {
                $table->dropColumn('resign_date');
                
                if (!Schema::hasColumn('employer_appointments', 'termination_date')) {
                    $table->date('termination_date')->nullable()->after('retirement_date');
                }
                
                if (!Schema::hasColumn('employer_appointments', 'termination_id')) {
                    $table->char('termination_id', 10)->nullable()->after('termination_date')->comment('Foreign key referencing reasons_for_termination_of_services table');
                    
                    $table->foreign('termination_id')
                        ->references('termination_id')
                        ->on('reasons_for_termination_of_services')
                        ->cascadeOnDelete()
                        ->cascadeOnUpdate();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // If the new columns exist, but resign_date does not, we rollback by restoring resign_date
        if (!Schema::hasColumn('employer_appointments', 'resign_date') && Schema::hasColumn('employer_appointments', 'termination_date')) {
            Schema::table('employer_appointments', function (Blueprint $table) {
                // Drop foreign key first if it exists
                // We use DB schema to drop foreign key safely
                try {
                    $table->dropForeign(['termination_id']);
                } catch (\Exception $e) {
                    // Ignore if it doesn't exist
                }
                
                $table->dropColumn(['termination_id', 'termination_date']);
                $table->date('resign_date')->nullable()->after('retirement_date');
            });
        }
    }
};
