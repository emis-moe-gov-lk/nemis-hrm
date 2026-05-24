<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('teacher_transfer_applications')) {
            return;
        }

        Schema::table('teacher_transfer_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('teacher_transfer_applications', 'cwp_facilities_id')) {
                $table->char('cwp_facilities_id', 10)
                    ->nullable()
                    ->after('current_workplace_join_date')
                    ->comment('Current working place facilities id');
            }

            if (!Schema::hasColumn('teacher_transfer_applications', 'ns_cat')) {
                $table->string('ns_cat', 1)
                    ->nullable()
                    ->after('cwp_facilities_id')
                    ->comment('National school category A, B, C, D,..');
            }

            if (!Schema::hasColumn('teacher_transfer_applications', 'latitude')) {
                $table->decimal('latitude', 10, 7)
                    ->nullable()
                    ->after('permanent_address');
            }

            if (!Schema::hasColumn('teacher_transfer_applications', 'longitude')) {
                $table->decimal('longitude', 10, 7)
                    ->nullable()
                    ->after('latitude');
            }

            if (!Schema::hasColumn('teacher_transfer_applications', 'temp_latitude')) {
                $table->decimal('temp_latitude', 10, 7)
                    ->nullable()
                    ->after('temporary_address');
            }

            if (!Schema::hasColumn('teacher_transfer_applications', 'temp_longitude')) {
                $table->decimal('temp_longitude', 10, 7)
                    ->nullable()
                    ->after('temp_latitude');
            }

            if (!Schema::hasColumn('teacher_transfer_applications', 'created_by')) {
                $table->string('created_by', 12)
                    ->nullable()
                    ->after('current_step');
            }

            if (!Schema::hasColumn('teacher_transfer_applications', 'updated_by')) {
                $table->string('updated_by', 12)
                    ->nullable()
                    ->after('created_by');
            }
        });

        Schema::table('teacher_transfer_applications', function (Blueprint $table) {
            if (Schema::hasColumn('teacher_transfer_applications', 'cwp_facilities_id')
                && $this->missingForeignKey(
                    'teacher_transfer_applications',
                    'tta_app_cwp_fac_fk',
                    'cwp_facilities_id',
                    'institutional_facilities',
                    'facilities_id'
                )) {
                $table->foreign('cwp_facilities_id', 'tta_app_cwp_fac_fk')
                    ->references('facilities_id')
                    ->on('institutional_facilities');
            }

            if (Schema::hasColumn('teacher_transfer_applications', 'transfer_category')
                && $this->missingForeignKey(
                    'teacher_transfer_applications',
                    'tta_app_cat_fk',
                    'transfer_category',
                    'teacher_transfer_categories',
                    'transfer_category_id'
                )) {
                $table->foreign('transfer_category', 'tta_app_cat_fk')
                    ->references('transfer_category_id')
                    ->on('teacher_transfer_categories');
            }

            if (Schema::hasColumn('teacher_transfer_applications', 'created_by')
                && $this->missingForeignKey(
                    'teacher_transfer_applications',
                    'tta_app_created_by_fk',
                    'created_by',
                    'people',
                    'people_id'
                )) {
                $table->foreign('created_by', 'tta_app_created_by_fk')
                    ->references('people_id')
                    ->on('people');
            }

            if (Schema::hasColumn('teacher_transfer_applications', 'updated_by')
                && $this->missingForeignKey(
                    'teacher_transfer_applications',
                    'tta_app_updated_by_fk',
                    'updated_by',
                    'people',
                    'people_id'
                )) {
                $table->foreign('updated_by', 'tta_app_updated_by_fk')
                    ->references('people_id')
                    ->on('people');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('teacher_transfer_applications')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('teacher_transfer_applications', function (Blueprint $table) {
            $this->dropForeignIfExists(
                $table,
                'teacher_transfer_applications',
                'tta_app_cwp_fac_fk',
                'cwp_facilities_id',
                'institutional_facilities',
                'facilities_id'
            );

            $this->dropForeignIfExists(
                $table,
                'teacher_transfer_applications',
                'teacher_transfer_applications_cwp_facilities_id_foreign',
                'cwp_facilities_id',
                'institutional_facilities',
                'facilities_id'
            );

            $this->dropForeignIfExists(
                $table,
                'teacher_transfer_applications',
                'tta_app_cat_fk',
                'transfer_category',
                'teacher_transfer_categories',
                'transfer_category_id'
            );

            $this->dropForeignIfExists(
                $table,
                'teacher_transfer_applications',
                'teacher_transfer_applications_transfer_category_foreign',
                'transfer_category',
                'teacher_transfer_categories',
                'transfer_category_id'
            );

            $this->dropForeignIfExists(
                $table,
                'teacher_transfer_applications',
                'tta_app_created_by_fk',
                'created_by',
                'people',
                'people_id'
            );

            $this->dropForeignIfExists(
                $table,
                'teacher_transfer_applications',
                'teacher_transfer_applications_created_by_foreign',
                'created_by',
                'people',
                'people_id'
            );

            $this->dropForeignIfExists(
                $table,
                'teacher_transfer_applications',
                'tta_app_updated_by_fk',
                'updated_by',
                'people',
                'people_id'
            );

            $this->dropForeignIfExists(
                $table,
                'teacher_transfer_applications',
                'teacher_transfer_applications_updated_by_foreign',
                'updated_by',
                'people',
                'people_id'
            );

            $dropColumns = [];

            foreach ([
                'cwp_facilities_id',
                'ns_cat',
                'latitude',
                'longitude',
                'temp_latitude',
                'temp_longitude',
                'created_by',
                'updated_by',
            ] as $column) {
                if (Schema::hasColumn('teacher_transfer_applications', $column)) {
                    $dropColumns[] = $column;
                }
            }

            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }

    private function dropForeignIfExists(
        Blueprint $table,
        string $tableName,
        string $constraintName,
        string $columnName,
        string $referencedTable,
        string $referencedColumn
    ): void {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        if (!$this->missingForeignKey(
            $tableName,
            $constraintName,
            $columnName,
            $referencedTable,
            $referencedColumn
        )) {
            $table->dropForeign($constraintName);
        }
    }

    private function missingForeignKey(
        string $tableName,
        string $constraintName,
        string $columnName,
        string $referencedTable,
        string $referencedColumn
    ): bool
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            $foreignKeys = DB::select(sprintf(
                "PRAGMA foreign_key_list('%s')",
                $this->sqliteLiteralTableName($tableName)
            ));

            foreach ($foreignKeys as $foreignKey) {
                if (($foreignKey->from ?? null) === $columnName
                    && ($foreignKey->table ?? null) === $referencedTable
                    && ($foreignKey->to ?? null) === $referencedColumn) {
                    return false;
                }
            }

            return true;
        }

        if ($driver !== 'mysql') {
            return true;
        }

        return DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $tableName)
            ->where('CONSTRAINT_NAME', $constraintName)
            ->where('CONSTRAINT_TYPE', 'FOREIGN KEY')
            ->doesntExist();
    }

    private function sqliteLiteralTableName(string $tableName): string
    {
        if (!preg_match('/\A[a-zA-Z0-9_]+\z/', $tableName)) {
            throw new InvalidArgumentException('Unsafe SQLite table name provided.');
        }

        return $tableName;
    }
};
