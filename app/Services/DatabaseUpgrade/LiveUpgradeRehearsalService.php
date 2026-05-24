<?php

namespace App\Services\DatabaseUpgrade;

use App\Support\DatabaseUpgrade\LiveUpgradePlan;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Throwable;

class LiveUpgradeRehearsalService
{
    /**
     * @param  array{
     *     connection: string,
     *     source: string,
     *     target: string,
     *     reference: string,
     *     fresh: bool,
     *     dry_run: bool,
     *     keep_failed_target: bool,
     *     patch_dir: string,
     *     report_dir: string
     * }  $options
     * @return array<string, mixed>
     */
    public function rehearse(array $options): array
    {
        $startedAt = microtime(true);
        $targetCreated = false;
        $report = [
            'status' => 'running',
            'started_at' => now()->toIso8601String(),
            'connection' => $options['connection'],
            'source_database' => $options['source'],
            'target_database' => $options['target'],
            'reference_database' => $options['reference'],
            'dry_run' => $options['dry_run'],
            'commands' => [],
            'applied_patches' => [],
            'reconciled_migrations' => [],
            'drift' => [],
            'validation' => [],
            'cleanup' => null,
            'error' => null,
        ];

        try {
            $baseConfig = $this->baseConnectionConfig($options['connection']);
            $this->guardInputs($options['source'], $options['target'], $options['reference']);

            $adminConnection = $this->registerRuntimeConnection('__live_upgrade_admin', $baseConfig);
            $sourceConnection = $this->registerRuntimeConnection('__live_upgrade_source', array_merge($baseConfig, [
                'database' => $options['source'],
            ]));
            $referenceConnection = $this->registerRuntimeConnection('__live_upgrade_reference', array_merge($baseConfig, [
                'database' => $options['reference'],
            ]));

            $report['preflight'] = [
                'source_exists' => $this->databaseExists($adminConnection, $options['source']),
                'reference_exists' => $this->databaseExists($adminConnection, $options['reference']),
                'target_exists' => $this->databaseExists($adminConnection, $options['target']),
                'reconciliation_rules' => LiveUpgradePlan::reconciliationRules(),
                'targeted_seeders' => LiveUpgradePlan::targetedSeeders(),
            ];

            if (! $report['preflight']['source_exists']) {
                throw new RuntimeException("Source database [{$options['source']}] does not exist.");
            }

            if (! $report['preflight']['reference_exists']) {
                throw new RuntimeException("Reference database [{$options['reference']}] does not exist.");
            }

            if ($options['dry_run']) {
                $report['status'] = 'dry-run';

                return $this->finalizeReport($report, $options['report_dir'], $startedAt);
            }

            $this->prepareTargetDatabase(
                $adminConnection,
                $sourceConnection,
                $options['source'],
                $options['target'],
                $options['fresh']
            );
            $targetCreated = true;

            $targetConnection = $this->registerRuntimeConnection('__live_upgrade_target', array_merge($baseConfig, [
                'database' => $options['target'],
            ]));

            $report['clone'] = $this->cloneDatabase(
                $sourceConnection,
                $options['source'],
                $options['target']
            );

            $report['applied_patches'] = $this->applyManualPatches(
                $targetConnection,
                $options['patch_dir']
            );
            $report['auto_increment_fixes'] = $this->normalizeAutoIncrementPrimaryKeys(
                $targetConnection,
                $referenceConnection,
                $options['target'],
                $options['reference']
            );
            $report['migration_table_fix'] = $this->normalizeMigrationsTable($targetConnection);

            $drift = $this->compareReconciliationRules(
                $targetConnection,
                $referenceConnection,
                $options['target'],
                $options['reference']
            );

            if ($drift !== []) {
                $report['drift'] = $drift;

                throw new RuntimeException(
                    'Schema drift detected in reconciled tables/columns. Review the report and add a manual patch before rerunning.'
                );
            }

            $report['reconciled_migrations'] = $this->reconcileMigrationLedger($targetConnection);

            $migrateCommand = $this->runArtisanCommand('migrate', [
                '--database' => $targetConnection,
                '--force' => true,
                '--no-interaction' => true,
            ]);
            $report['commands'][] = $migrateCommand;
            $this->ensureSuccessfulCommand($migrateCommand);

            foreach (LiveUpgradePlan::targetedSeeders() as $seeder) {
                $seedCommand = $this->runArtisanCommand('db:seed', [
                    '--database' => $targetConnection,
                    '--class' => $seeder,
                    '--force' => true,
                    '--no-interaction' => true,
                ]);
                $report['commands'][] = $seedCommand;
                $this->ensureSuccessfulCommand($seedCommand);
            }

            $statusCommand = $this->runArtisanCommand('migrate:status', [
                '--database' => $targetConnection,
                '--no-interaction' => true,
            ]);
            $report['commands'][] = $statusCommand;
            $this->ensureSuccessfulCommand($statusCommand);

            $report['validation'] = $this->validateOutcome(
                $sourceConnection,
                $targetConnection,
                $options['source'],
                $options['target']
            );

            if ($report['validation']['failures'] !== []) {
                throw new RuntimeException(
                    'Upgrade rehearsal completed with validation failures. Review the report before rerunning.'
                );
            }

            $report['status'] = 'success';
        } catch (Throwable $throwable) {
            $report['status'] = 'failed';
            $report['error'] = $throwable->getMessage();

            if ($targetCreated && ! $options['keep_failed_target']) {
                try {
                    $adminConnection = '__live_upgrade_admin';

                    if (DB::connection($adminConnection)->getDriverName() === 'mysql') {
                        DB::connection($adminConnection)->statement(
                            'DROP DATABASE IF EXISTS '.$this->quoteIdentifier($options['target'])
                        );
                        $report['cleanup'] = 'Dropped failed rehearsal database.';
                    }
                } catch (Throwable $cleanupError) {
                    $report['cleanup'] = 'Failed to drop the rehearsal database automatically.';
                    $report['cleanup_error'] = $cleanupError->getMessage();
                }
            } elseif ($targetCreated) {
                $report['cleanup'] = 'Failed rehearsal database was kept for inspection.';
            }
        }

        return $this->finalizeReport($report, $options['report_dir'], $startedAt);
    }

    /**
     * @return array<string, mixed>
     */
    protected function baseConnectionConfig(string $connection): array
    {
        $config = config("database.connections.{$connection}");

        if (! is_array($config)) {
            throw new RuntimeException("Database connection [{$connection}] is not configured.");
        }

        if (($config['driver'] ?? null) !== 'mysql') {
            throw new RuntimeException('The live upgrade rehearsal only supports MySQL-compatible connections.');
        }

        return $config;
    }

    protected function guardInputs(string $source, string $target, string $reference): void
    {
        foreach ([$source, $target, $reference] as $database) {
            if (! preg_match('/\A[a-zA-Z0-9_]+\z/', $database)) {
                throw new RuntimeException("Unsafe database name [{$database}] was provided.");
            }
        }

        if ($source === $target || $reference === $target) {
            throw new RuntimeException('Target database must be different from both source and reference databases.');
        }
    }

    protected function registerRuntimeConnection(string $name, array $config): string
    {
        config(["database.connections.{$name}" => $config]);
        DB::purge($name);

        return $name;
    }

    protected function databaseExists(string $connection, string $database): bool
    {
        $row = DB::connection($connection)->selectOne(
            'select schema_name from information_schema.schemata where schema_name = ?',
            [$database]
        );

        return $row !== null;
    }

    protected function prepareTargetDatabase(
        string $adminConnection,
        string $sourceConnection,
        string $sourceDatabase,
        string $targetDatabase,
        bool $fresh
    ): void {
        $targetExists = $this->databaseExists($adminConnection, $targetDatabase);

        if ($targetExists && ! $fresh) {
            throw new RuntimeException(
                "Target database [{$targetDatabase}] already exists. Re-run with --fresh to recreate it."
            );
        }

        if ($targetExists) {
            DB::connection($adminConnection)->statement(
                'DROP DATABASE IF EXISTS '.$this->quoteIdentifier($targetDatabase)
            );
        }

        $schema = DB::connection($sourceConnection)->selectOne(
            'select
                default_character_set_name as default_character_set_name,
                default_collation_name as default_collation_name
             from information_schema.schemata
             where schema_name = ?',
            [$sourceDatabase]
        );

        if ($schema === null) {
            throw new RuntimeException("Could not resolve charset and collation for [{$sourceDatabase}].");
        }

        DB::connection($adminConnection)->statement(sprintf(
            'CREATE DATABASE %s CHARACTER SET %s COLLATE %s',
            $this->quoteIdentifier($targetDatabase),
            $this->plainSqlToken((string) $schema->default_character_set_name),
            $this->plainSqlToken((string) $schema->default_collation_name)
        ));
    }

    /**
     * @return array<string, mixed>
     */
    protected function cloneDatabase(string $sourceConnection, string $sourceDatabase, string $targetDatabase): array
    {
        $tables = DB::connection($sourceConnection)->select(
            'select table_name as table_name
             from information_schema.tables
             where table_schema = ? and table_type = ?
             order by table_name',
            [$sourceDatabase, 'BASE TABLE']
        );

        DB::connection($sourceConnection)->statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            foreach ($tables as $tableRow) {
                $table = (string) $tableRow->table_name;
                $createTable = $this->getCreateTableStatement($sourceConnection, $sourceDatabase, $table);
                $createTable = preg_replace(
                    '/^CREATE TABLE `[^`]+`/i',
                    'CREATE TABLE '.$this->quoteQualifiedIdentifier($targetDatabase, $table),
                    $createTable,
                    1
                ) ?? $createTable;

                DB::connection($sourceConnection)->unprepared($createTable);
                DB::connection($sourceConnection)->statement(sprintf(
                    'INSERT INTO %s SELECT * FROM %s',
                    $this->quoteQualifiedIdentifier($targetDatabase, $table),
                    $this->quoteQualifiedIdentifier($sourceDatabase, $table)
                ));
            }
        } finally {
            DB::connection($sourceConnection)->statement('SET FOREIGN_KEY_CHECKS=1');
        }

        return [
            'table_count' => count($tables),
            'tables' => array_map(
                static fn (object $row): string => (string) $row->table_name,
                $tables
            ),
        ];
    }

    /**
     * @return array<int, array{path: string, table: string}>
     */
    protected function applyManualPatches(string $targetConnection, string $patchDirectory): array
    {
        $applied = [];

        if (! is_dir($patchDirectory)) {
            return $applied;
        }

        $patchFiles = File::files($patchDirectory);
        usort($patchFiles, static fn ($left, $right): int => strcmp($left->getFilename(), $right->getFilename()));

        foreach ($patchFiles as $patchFile) {
            if (strtolower($patchFile->getExtension()) !== 'sql') {
                continue;
            }

            $path = $patchFile->getPathname();
            $sql = trim(File::get($path));

            if ($sql === '') {
                continue;
            }

            DB::connection($targetConnection)->unprepared($sql);
            $applied[] = [
                'table' => pathinfo($patchFile->getFilename(), PATHINFO_FILENAME),
                'path' => $path,
            ];
        }

        return $applied;
    }

    /**
     * @return array<int, array{table: string, column_type: string}>
     */
    protected function normalizeAutoIncrementPrimaryKeys(
        string $targetConnection,
        string $referenceConnection,
        string $targetDatabase,
        string $referenceDatabase
    ): array {
        $rows = DB::connection($referenceConnection)->select(
            'select
                reference_columns.table_name as table_name,
                reference_columns.column_type as column_type
             from information_schema.columns as reference_columns
             inner join information_schema.columns as target_columns
                on target_columns.table_schema = ?
               and target_columns.table_name = reference_columns.table_name
               and target_columns.column_name = reference_columns.column_name
             where reference_columns.table_schema = ?
               and reference_columns.column_name = ?
               and reference_columns.column_key = ?
               and reference_columns.extra like ?
               and target_columns.extra not like ?
               and reference_columns.table_name <> ?
             order by reference_columns.table_name',
            [
                $targetDatabase,
                $referenceDatabase,
                'id',
                'PRI',
                '%auto_increment%',
                '%auto_increment%',
                'migrations',
            ]
        );

        $normalized = [];

        foreach ($rows as $row) {
            $table = (string) $row->table_name;
            $columnType = (string) $row->column_type;

            DB::connection($targetConnection)->statement(sprintf(
                'ALTER TABLE %s MODIFY COLUMN `id` %s NOT NULL AUTO_INCREMENT',
                $this->quoteQualifiedIdentifier($targetDatabase, $table),
                $columnType
            ));

            $normalized[] = [
                'table' => $table,
                'column_type' => $columnType,
            ];
        }

        return $normalized;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function compareReconciliationRules(
        string $targetConnection,
        string $referenceConnection,
        string $targetDatabase,
        string $referenceDatabase
    ): array {
        $drift = [];

        foreach (LiveUpgradePlan::reconciliationRules() as $rule) {
            if ($rule['mode'] === 'table') {
                $targetCreate = $this->getTableStructureSignature($targetConnection, $targetDatabase, $rule['table']);
                $referenceCreate = $this->getTableStructureSignature($referenceConnection, $referenceDatabase, $rule['table']);

                if ($targetCreate !== $referenceCreate) {
                    $drift[] = [
                        'migration' => $rule['migration'],
                        'table' => $rule['table'],
                        'mode' => $rule['mode'],
                        'target_signature' => $targetCreate,
                        'reference_signature' => $referenceCreate,
                    ];
                }

                continue;
            }

            $targetColumns = $this->getColumnSignatures($targetConnection, $targetDatabase, $rule['table'], $rule['columns']);
            $referenceColumns = $this->getColumnSignatures($referenceConnection, $referenceDatabase, $rule['table'], $rule['columns']);

            if ($targetColumns !== $referenceColumns) {
                $drift[] = [
                    'migration' => $rule['migration'],
                    'table' => $rule['table'],
                    'mode' => $rule['mode'],
                    'columns' => $rule['columns'],
                    'target_signature' => $targetColumns,
                    'reference_signature' => $referenceColumns,
                ];
            }
        }

        return $drift;
    }

    /**
     * @return array{
     *     columns: array<int, array{name: string, type: string, nullable: string, default: mixed, extra: string, collation: ?string, comment: string}>,
     *     primary_key: array<int, string>,
     *     indexes: array<int, array{unique: bool, columns: array<int, string>}>,
     *     foreign_keys: array<int, array{columns: array<int, string>, references: array{table: string, columns: array<int, string>}, update: string, delete: string}>
     * }
     */
    protected function getTableStructureSignature(string $connection, string $database, string $table): array
    {
        return [
            'columns' => $this->getTableColumnSignatures($connection, $database, $table),
            'primary_key' => $this->getPrimaryKeySignature($connection, $database, $table),
            'indexes' => $this->getIndexSignatures($connection, $database, $table),
            'foreign_keys' => $this->getForeignKeySignatures($connection, $database, $table),
        ];
    }

    /**
     * @return array<int, array{name: string, type: string, nullable: string, default: mixed, extra: string, collation: ?string, comment: string}>
     */
    protected function getColumnSignatures(
        string $connection,
        string $database,
        string $table,
        array $columns
    ): array {
        if ($columns === []) {
            return [];
        }

        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        $bindings = array_merge([$database, $table], $columns);

        $rows = DB::connection($connection)->select(
            "select
                column_name as name,
                column_type as type,
                is_nullable as nullable,
                column_default as `default`,
                extra as extra,
                collation_name as collation,
                column_comment as comment
             from information_schema.columns
             where table_schema = ? and table_name = ? and column_name in ({$placeholders})
             order by ordinal_position",
            $bindings
        );

        return array_map(
            static fn (object $row): array => [
                'name' => (string) $row->name,
                'type' => (string) $row->type,
                'nullable' => (string) $row->nullable,
                'default' => $row->default,
                'extra' => (string) $row->extra,
                'collation' => $row->collation ? (string) $row->collation : null,
                'comment' => (string) $row->comment,
            ],
            $rows
        );
    }

    /**
     * @return array<int, array{name: string, type: string, nullable: string, default: mixed, extra: string, collation: ?string, comment: string}>
     */
    protected function getTableColumnSignatures(string $connection, string $database, string $table): array
    {
        $rows = DB::connection($connection)->select(
            'select
                column_name as name,
                column_type as type,
                is_nullable as nullable,
                column_default as `default`,
                extra as extra,
                collation_name as collation,
                column_comment as comment
             from information_schema.columns
             where table_schema = ? and table_name = ?
             order by ordinal_position',
            [$database, $table]
        );

        return array_map(
            static fn (object $row): array => [
                'name' => (string) $row->name,
                'type' => (string) $row->type,
                'nullable' => (string) $row->nullable,
                'default' => $row->default,
                'extra' => (string) $row->extra,
                'collation' => $row->collation ? (string) $row->collation : null,
                'comment' => (string) $row->comment,
            ],
            $rows
        );
    }

    /**
     * @return array<int, string>
     */
    protected function getPrimaryKeySignature(string $connection, string $database, string $table): array
    {
        $rows = DB::connection($connection)->select(
            'select column_name as column_name
             from information_schema.key_column_usage
             where table_schema = ? and table_name = ? and constraint_name = ?
             order by ordinal_position',
            [$database, $table, 'PRIMARY']
        );

        return array_map(
            static fn (object $row): string => (string) $row->column_name,
            $rows
        );
    }

    /**
     * @return array<int, array{unique: bool, columns: array<int, string>}>
     */
    protected function getIndexSignatures(string $connection, string $database, string $table): array
    {
        $rows = DB::connection($connection)->select(
            'select
                index_name as index_name,
                non_unique as non_unique,
                seq_in_index as seq_in_index,
                column_name as column_name
             from information_schema.statistics
             where table_schema = ? and table_name = ? and index_name <> ?
             order by index_name, seq_in_index',
            [$database, $table, 'PRIMARY']
        );

        $grouped = [];

        foreach ($rows as $row) {
            $indexName = (string) $row->index_name;
            $grouped[$indexName]['unique'] = ((int) $row->non_unique) === 0;
            $grouped[$indexName]['columns'][] = (string) $row->column_name;
        }

        $signatures = array_values($grouped);

        usort($signatures, static fn (array $left, array $right): int => strcmp(
            json_encode($left, JSON_UNESCAPED_SLASHES),
            json_encode($right, JSON_UNESCAPED_SLASHES)
        ));

        return $signatures;
    }

    /**
     * @return array<int, array{columns: array<int, string>, references: array{table: string, columns: array<int, string>}, update: string, delete: string}>
     */
    protected function getForeignKeySignatures(string $connection, string $database, string $table): array
    {
        $rows = DB::connection($connection)->select(
            'select
                kcu.constraint_name as constraint_name,
                kcu.column_name as column_name,
                kcu.ordinal_position as ordinal_position,
                kcu.referenced_table_name as referenced_table_name,
                kcu.referenced_column_name as referenced_column_name,
                rc.update_rule as update_rule,
                rc.delete_rule as delete_rule
             from information_schema.key_column_usage kcu
             inner join information_schema.referential_constraints rc
                on rc.constraint_schema = kcu.table_schema
               and rc.constraint_name = kcu.constraint_name
               and rc.table_name = kcu.table_name
             where kcu.table_schema = ? and kcu.table_name = ? and kcu.referenced_table_name is not null
             order by kcu.constraint_name, kcu.ordinal_position',
            [$database, $table]
        );

        $grouped = [];

        foreach ($rows as $row) {
            $constraintName = (string) $row->constraint_name;
            $grouped[$constraintName]['columns'][] = (string) $row->column_name;
            $grouped[$constraintName]['references']['table'] = (string) $row->referenced_table_name;
            $grouped[$constraintName]['references']['columns'][] = (string) $row->referenced_column_name;
            $grouped[$constraintName]['update'] = (string) $row->update_rule;
            $grouped[$constraintName]['delete'] = (string) $row->delete_rule;
        }

        $signatures = array_values($grouped);

        usort($signatures, static fn (array $left, array $right): int => strcmp(
            json_encode($left, JSON_UNESCAPED_SLASHES),
            json_encode($right, JSON_UNESCAPED_SLASHES)
        ));

        return $signatures;
    }

    /**
     * @return array<int, string>
     */
    protected function reconcileMigrationLedger(string $targetConnection): array
    {
        $repository = DB::connection($targetConnection);
        $maxBatch = (int) ($repository->table('migrations')->max('batch') ?? 0);
        $batch = max($maxBatch, 1);
        $inserted = [];

        foreach (LiveUpgradePlan::reconciliationRules() as $rule) {
            $exists = $repository->table('migrations')
                ->where('migration', $rule['migration'])
                ->exists();

            if ($exists) {
                continue;
            }

            $repository->table('migrations')->insert([
                'migration' => $rule['migration'],
                'batch' => $batch,
            ]);

            $inserted[] = $rule['migration'];
        }

        return $inserted;
    }

    /**
     * @param  array<string, mixed>  $arguments
     * @return array{command: string, arguments: array<string, mixed>, exit_code: int, output: string}
     */
    protected function runArtisanCommand(string $command, array $arguments): array
    {
        $exitCode = Artisan::call($command, $arguments);

        return [
            'command' => $command,
            'arguments' => $arguments,
            'exit_code' => $exitCode,
            'output' => Artisan::output(),
        ];
    }

    /**
     * @param  array{command: string, arguments: array<string, mixed>, exit_code: int, output: string}  $command
     */
    protected function ensureSuccessfulCommand(array $command): void
    {
        if ($command['exit_code'] === 0) {
            return;
        }

        throw new RuntimeException(trim($command['output']) !== ''
            ? trim($command['output'])
            : "Artisan command [{$command['command']}] failed.");
    }

    /**
     * @return array<string, mixed>
     */
    protected function validateOutcome(
        string $sourceConnection,
        string $targetConnection,
        string $sourceDatabase,
        string $targetDatabase
    ): array {
        $failures = [];
        $warnings = [];
        $counts = [];

        foreach (LiveUpgradePlan::coreCountTables() as $table) {
            $sourceCount = DB::connection($sourceConnection)->table($table)->count();
            $targetCount = DB::connection($targetConnection)->table($table)->count();

            $counts[$table] = [
                'source' => $sourceCount,
                'target' => $targetCount,
            ];

            if ($sourceCount !== $targetCount) {
                $failures[] = "Row count mismatch detected for [{$table}] ({$sourceCount} vs {$targetCount}).";
            }
        }

        $expectedTables = [];

        foreach (LiveUpgradePlan::expectedNewTables() as $table) {
            $exists = $this->tableExists($targetConnection, $targetDatabase, $table);
            $expectedTables[$table] = $exists;

            if (! $exists) {
                $failures[] = "Expected table [{$table}] is missing from the rehearsal database.";
            }
        }

        $seedCounts = [];

        foreach (LiveUpgradePlan::seedExpectations() as $table => $minimum) {
            $count = DB::connection($targetConnection)->table($table)->count();
            $seedCounts[$table] = $count;

            if ($count < $minimum) {
                $failures[] = "Seed validation failed for [{$table}] (expected at least {$minimum}, found {$count}).";
            }
        }

        $sourceMetrics = $this->metricSnapshot($sourceConnection, $sourceDatabase);
        $targetMetrics = $this->metricSnapshot($targetConnection, $targetDatabase);

        foreach ($targetMetrics as $metric => $value) {
            if (! array_key_exists($metric, $sourceMetrics)) {
                continue;
            }

            if ($sourceMetrics[$metric] !== 0 && $value !== $sourceMetrics[$metric]) {
                $failures[] = "Preserved metric [{$metric}] changed unexpectedly ({$sourceMetrics[$metric]} vs {$value}).";
            }

            if ($sourceMetrics[$metric] === 0 && $value === 0 && str_contains($metric, 'grade_mask')) {
                $failures[] = 'Subject grade masks were created but remain at default values only.';
            }

            if ($sourceMetrics[$metric] === 0 && $value === 0 && $metric === 'grade_spans.with_bounds') {
                $failures[] = 'Grade span bounds are still empty after the rehearsal.';
            }
        }

        $pendingMigrations = $this->pendingMigrationNames($targetConnection);

        if ($pendingMigrations !== []) {
            $failures[] = 'Unexpected pending migrations remain after the rehearsal run.';
        }

        if (($targetMetrics['subject_lists.language_mask_non_default'] ?? 0) === 0) {
            $warnings[] = 'No non-default language masks are present in the target snapshot; confirm this is acceptable for the live dataset.';
        }

        return [
            'core_counts' => $counts,
            'expected_tables' => $expectedTables,
            'seed_counts' => $seedCounts,
            'source_metrics' => $sourceMetrics,
            'target_metrics' => $targetMetrics,
            'pending_migrations' => $pendingMigrations,
            'failures' => $failures,
            'warnings' => $warnings,
        ];
    }

    /**
     * @return array<string, int>
     */
    protected function metricSnapshot(string $connection, string $database): array
    {
        $metrics = [];

        foreach (LiveUpgradePlan::preservedMetricQueries() as $label => $sql) {
            try {
                $row = DB::connection($connection)->selectOne($sql);
                $metrics[$label] = (int) ($row->aggregate ?? 0);
            } catch (Throwable) {
                $metrics[$label] = 0;
            }
        }

        return $metrics;
    }

    /**
     * @return array<int, string>
     */
    protected function pendingMigrationNames(string $connection): array
    {
        $files = app('migrator')->getMigrationFiles([database_path('migrations')]);
        $ran = DB::connection($connection)->table('migrations')->pluck('migration')->all();

        return array_values(array_diff(array_keys($files), $ran));
    }

    protected function tableExists(string $connection, string $database, string $table): bool
    {
        $row = DB::connection($connection)->selectOne(
            'select table_name as table_name
             from information_schema.tables
             where table_schema = ? and table_name = ? and table_type = ?',
            [$database, $table, 'BASE TABLE']
        );

        return $row !== null;
    }

    protected function getCreateTableStatement(string $connection, string $database, string $table): string
    {
        $row = DB::connection($connection)->selectOne(
            'SHOW CREATE TABLE '.$this->quoteQualifiedIdentifier($database, $table)
        );

        if ($row === null) {
            throw new RuntimeException("Could not read CREATE TABLE for [{$database}.{$table}].");
        }

        $values = array_values((array) $row);

        return (string) ($values[1] ?? '');
    }

    protected function normalizeMigrationsTable(string $targetConnection): array
    {
        $database = DB::connection($targetConnection)->getDatabaseName();
        $row = DB::connection($targetConnection)->selectOne(
            'select extra as extra
             from information_schema.columns
             where table_schema = ? and table_name = ? and column_name = ?',
            [$database, 'migrations', 'id']
        );

        $extra = strtolower((string) ($row->extra ?? ''));

        if (str_contains($extra, 'auto_increment')) {
            return [
                'changed' => false,
                'message' => 'Legacy migrations table already uses AUTO_INCREMENT on id.',
            ];
        }

        DB::connection($targetConnection)->statement(
            'ALTER TABLE `migrations` MODIFY COLUMN `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT'
        );

        return [
            'changed' => true,
            'message' => 'Updated legacy migrations.id to AUTO_INCREMENT for Laravel compatibility.',
        ];
    }

    protected function quoteIdentifier(string $identifier): string
    {
        if (! preg_match('/\A[a-zA-Z0-9_]+\z/', $identifier)) {
            throw new RuntimeException("Unsafe SQL identifier [{$identifier}] was provided.");
        }

        return '`'.$identifier.'`';
    }

    protected function quoteQualifiedIdentifier(string $database, string $table): string
    {
        return $this->quoteIdentifier($database).'.'.$this->quoteIdentifier($table);
    }

    protected function plainSqlToken(string $value): string
    {
        if (! preg_match('/\A[a-zA-Z0-9_]+\z/', $value)) {
            throw new RuntimeException("Unsafe SQL token [{$value}] was provided.");
        }

        return $value;
    }

    /**
     * @param  array<string, mixed>  $report
     * @return array<string, mixed>
     */
    protected function finalizeReport(array $report, string $reportDirectory, float $startedAt): array
    {
        $report['completed_at'] = now()->toIso8601String();
        $report['duration_seconds'] = round(microtime(true) - $startedAt, 2);
        $report['report_paths'] = $this->writeReport($report, $reportDirectory);

        return $report;
    }

    /**
     * @param  array<string, mixed>  $report
     * @return array{json: string, markdown: string}
     */
    protected function writeReport(array $report, string $reportDirectory): array
    {
        File::ensureDirectoryExists($reportDirectory);

        $stamp = now()->format('Ymd_His');
        $baseName = sprintf(
            'live-upgrade-rehearsal-%s-%s',
            $stamp,
            $report['target_database']
        );

        $jsonPath = rtrim($reportDirectory, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$baseName.'.json';
        $markdownPath = rtrim($reportDirectory, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$baseName.'.md';

        File::put($jsonPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        File::put($markdownPath, $this->renderMarkdownReport($report));

        return [
            'json' => $jsonPath,
            'markdown' => $markdownPath,
        ];
    }

    /**
     * @param  array<string, mixed>  $report
     */
    protected function renderMarkdownReport(array $report): string
    {
        $lines = [
            '# Live Upgrade Rehearsal Report',
            '',
            '- Status: `'.$report['status'].'`',
            '- Source: `'.$report['source_database'].'`',
            '- Target: `'.$report['target_database'].'`',
            '- Reference: `'.$report['reference_database'].'`',
            '- Started: `'.$report['started_at'].'`',
            '- Completed: `'.$report['completed_at'].'`',
            '- Duration (s): `'.$report['duration_seconds'].'`',
            '',
            '## Reconciled Migrations',
        ];

        foreach ($report['reconciled_migrations'] as $migration) {
            $lines[] = '- `'.$migration.'`';
        }

        if ($report['reconciled_migrations'] === []) {
            $lines[] = '- None';
        }

        $lines[] = '';
        $lines[] = '## Applied Patches';

        foreach ($report['applied_patches'] as $patch) {
            $lines[] = '- `'.$patch['table'].'`: `'.$patch['path'].'`';
        }

        if ($report['applied_patches'] === []) {
            $lines[] = '- None';
        }

        $lines[] = '';
        $lines[] = '## Validation';

        foreach ($report['validation']['failures'] ?? [] as $failure) {
            $lines[] = '- Failure: '.$failure;
        }

        foreach ($report['validation']['warnings'] ?? [] as $warning) {
            $lines[] = '- Warning: '.$warning;
        }

        if (($report['validation']['failures'] ?? []) === [] && ($report['validation']['warnings'] ?? []) === []) {
            $lines[] = '- No validation warnings or failures.';
        }

        $lines[] = '';
        $lines[] = '## Manual Smoke Checklist';
        $lines[] = '- Point the local app to `'.$report['target_database'].'`.';
        $lines[] = '- Load `/login`.';
        $lines[] = '- Verify one dashboard page.';
        $lines[] = '- Verify teacher list, institution profile, and one transfer page.';
        $lines[] = '- Confirm no `500` responses in the local logs.';

        if ($report['error']) {
            $lines[] = '';
            $lines[] = '## Error';
            $lines[] = '- '.$report['error'];
        }

        if (! empty($report['drift'])) {
            $lines[] = '';
            $lines[] = '## Drift';

            foreach ($report['drift'] as $entry) {
                $lines[] = '- `'.$entry['table'].'` mismatched `'.$entry['migration'].'`';
            }
        }

        return implode(PHP_EOL, $lines).PHP_EOL;
    }
}
