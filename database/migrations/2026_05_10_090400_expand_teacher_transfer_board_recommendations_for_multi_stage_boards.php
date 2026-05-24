<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('teacher_transfer_board_recommendations')) {
            return;
        }

        $this->dropApplicationForeignKey();

        Schema::table('teacher_transfer_board_recommendations', function (Blueprint $table) {
            if ($this->indexExists('ttbr_app_unique')) {
                $table->dropUnique('ttbr_app_unique');
            }

            if (!$this->indexExists('ttbr_app_board_unique')) {
                $table->unique(['transfer_application_id', 'transfer_board_id'], 'ttbr_app_board_unique');
            }
        });

        $this->restoreApplicationForeignKey();
    }

    public function down(): void
    {
        if (! Schema::hasTable('teacher_transfer_board_recommendations')) {
            return;
        }

        $this->dropApplicationForeignKey();

        Schema::table('teacher_transfer_board_recommendations', function (Blueprint $table) {
            if ($this->indexExists('ttbr_app_board_unique')) {
                $table->dropUnique('ttbr_app_board_unique');
            }

            if (!$this->indexExists('ttbr_app_unique')) {
                $table->unique('transfer_application_id', 'ttbr_app_unique');
            }
        });

        $this->restoreApplicationForeignKey();
    }

    protected function dropApplicationForeignKey(): void
    {
        if (! $this->supportsMysqlConstraintStatements()) {
            return;
        }

        if ($this->foreignKeyExists('ttbr_app_fk')) {
            DB::statement('ALTER TABLE `teacher_transfer_board_recommendations` DROP FOREIGN KEY `ttbr_app_fk`');
        }
    }

    protected function restoreApplicationForeignKey(): void
    {
        if (! $this->supportsMysqlConstraintStatements()) {
            return;
        }

        if (!$this->foreignKeyExists('ttbr_app_fk')) {
            DB::statement(
                'ALTER TABLE `teacher_transfer_board_recommendations` ' .
                'ADD CONSTRAINT `ttbr_app_fk` FOREIGN KEY (`transfer_application_id`) ' .
                'REFERENCES `teacher_transfer_applications` (`transfer_application_id`) ON DELETE CASCADE'
            );
        }
    }

    protected function indexExists(string $indexName): bool
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            foreach (DB::select("PRAGMA index_list('teacher_transfer_board_recommendations')") as $index) {
                if (($index->name ?? null) === $indexName) {
                    return true;
                }
            }

            return false;
        }

        $database = DB::connection()->getDatabaseName();

        return DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', 'teacher_transfer_board_recommendations')
            ->where('index_name', $indexName)
            ->exists();
    }

    protected function foreignKeyExists(string $constraintName): bool
    {
        if (! $this->supportsMysqlConstraintStatements()) {
            return false;
        }

        $database = DB::connection()->getDatabaseName();

        return DB::table('information_schema.table_constraints')
            ->where('constraint_schema', $database)
            ->where('table_name', 'teacher_transfer_board_recommendations')
            ->where('constraint_name', $constraintName)
            ->where('constraint_type', 'FOREIGN KEY')
            ->exists();
    }

    protected function supportsMysqlConstraintStatements(): bool
    {
        return in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true);
    }
};
