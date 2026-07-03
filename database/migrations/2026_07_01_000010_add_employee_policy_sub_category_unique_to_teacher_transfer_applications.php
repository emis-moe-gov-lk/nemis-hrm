<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE = 'teacher_transfer_applications';

    private const INDEX = 'tta_employee_policy_subcat_unique';

    public function up(): void
    {
        if (! Schema::hasTable(self::TABLE)
            || ! Schema::hasColumn(self::TABLE, 'employee_id')
            || ! Schema::hasColumn(self::TABLE, 'policy_id')
            || ! Schema::hasColumn(self::TABLE, 'transfer_sub_category_id')
            || $this->indexExists(self::INDEX)
        ) {
            return;
        }

        $duplicates = DB::table(self::TABLE)
            ->select('employee_id', 'policy_id', 'transfer_sub_category_id', DB::raw('COUNT(*) as duplicate_count'))
            ->whereNotNull('transfer_sub_category_id')
            ->groupBy('employee_id', 'policy_id', 'transfer_sub_category_id')
            ->havingRaw('COUNT(*) > 1')
            ->limit(5)
            ->get();

        if ($duplicates->isNotEmpty()) {
            $examples = $duplicates
                ->map(fn ($row) => "{$row->employee_id} / {$row->policy_id} / {$row->transfer_sub_category_id} ({$row->duplicate_count})")
                ->implode(', ');

            throw new RuntimeException(
                'Cannot add unique index to teacher_transfer_applications. Duplicate employee_id + policy_id + transfer_sub_category_id rows exist: ' . $examples
            );
        }

        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->unique(['employee_id', 'policy_id', 'transfer_sub_category_id'], self::INDEX);
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable(self::TABLE) || ! $this->indexExists(self::INDEX)) {
            return;
        }

        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropUnique(self::INDEX);
        });
    }

    private function indexExists(string $indexName): bool
    {
        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            return DB::table('information_schema.statistics')
                ->where('table_schema', DB::getDatabaseName())
                ->where('table_name', self::TABLE)
                ->where('index_name', $indexName)
                ->exists();
        }

        if ($driver === 'sqlite') {
            return collect(DB::select("PRAGMA index_list('" . self::TABLE . "')"))
                ->contains(fn ($index) => ($index->name ?? null) === $indexName);
        }

        return false;
    }
};
