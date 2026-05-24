<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('teacher_transfer_categories')) {
            return;
        }

        $this->ensureColumnsExist();

        if (DB::table('teacher_transfer_categories')->exists()) {
            $this->backfillPolicyIds();
            $this->backfillOwnerWorkplaces();
            $this->ensureResolvedCategoryLinks();
        }

        $this->makeColumnsRequired();
        $this->ensureForeignKeysExist();
    }

    public function down(): void
    {
        // This migration aligns a live reference table with production data.
        // Rolling it back automatically is not safe because it would orphan
        // existing transfer category records and undo data backfills.
    }

    protected function ensureColumnsExist(): void
    {
        $hasPolicyColumn = Schema::hasColumn('teacher_transfer_categories', 'policy_id');
        $hasOwnerWorkplaceColumn = Schema::hasColumn('teacher_transfer_categories', 'transfer_owner_workplace_id');

        if ($hasPolicyColumn && $hasOwnerWorkplaceColumn) {
            return;
        }

        Schema::table('teacher_transfer_categories', function (Blueprint $table) use ($hasPolicyColumn, $hasOwnerWorkplaceColumn) {
            if (!$hasPolicyColumn) {
                $table->string('policy_id', 20)->nullable()->after('transfer_category_id');
            }

            if (!$hasOwnerWorkplaceColumn) {
                $table->string('transfer_owner_workplace_id', 10)->nullable()->after('office_level_id');
            }
        });
    }

    protected function backfillPolicyIds(): void
    {
        $policies = DB::table('teacher_transfer_policies')
            ->select('policy_id', 'transfer_authority')
            ->get();

        if ($policies->isEmpty()) {
            throw new RuntimeException('Cannot align transfer categories because no transfer policies exist.');
        }

        $singlePolicyId = $policies->count() === 1
            ? (string) $policies->first()->policy_id
            : null;

        $policiesByAuthority = $policies->groupBy('transfer_authority');

        $categories = DB::table('teacher_transfer_categories')
            ->select('id', 'transfer_category_id', 'policy_id', 'transfer_owner_workplace_id')
            ->whereNull('policy_id')
            ->get();

        foreach ($categories as $category) {
            $resolvedPolicyId = $this->resolvePolicyIdForCategory($category, $policiesByAuthority, $singlePolicyId);

            if (!$resolvedPolicyId) {
                continue;
            }

            DB::table('teacher_transfer_categories')
                ->where('id', $category->id)
                ->update(['policy_id' => $resolvedPolicyId]);
        }
    }

    protected function resolvePolicyIdForCategory(
        object $category,
        Collection $policiesByAuthority,
        ?string $singlePolicyId
    ): ?string {
        $usagePolicyIds = collect()
            ->merge(
                DB::table('teacher_transfer_applications')
                    ->where('transfer_category', $category->transfer_category_id)
                    ->whereNotNull('policy_id')
                    ->distinct()
                    ->pluck('policy_id')
            )
            ->merge(
                DB::table('teacher_transfer_boards')
                    ->where('transfer_category_id', $category->transfer_category_id)
                    ->whereNotNull('policy_id')
                    ->distinct()
                    ->pluck('policy_id')
            )
            ->filter()
            ->unique()
            ->values();

        if ($usagePolicyIds->count() === 1) {
            return (string) $usagePolicyIds->first();
        }

        if ($usagePolicyIds->count() > 1) {
            return null;
        }

        if (!empty($category->transfer_owner_workplace_id)) {
            $ownerPolicies = collect($policiesByAuthority->get($category->transfer_owner_workplace_id, collect()))
                ->pluck('policy_id')
                ->filter()
                ->unique()
                ->values();

            if ($ownerPolicies->count() === 1) {
                return (string) $ownerPolicies->first();
            }
        }

        return $singlePolicyId;
    }

    protected function backfillOwnerWorkplaces(): void
    {
        $policyAuthorities = DB::table('teacher_transfer_policies')
            ->pluck('transfer_authority', 'policy_id');

        $categories = DB::table('teacher_transfer_categories')
            ->select('id', 'policy_id', 'transfer_owner_workplace_id')
            ->whereNull('transfer_owner_workplace_id')
            ->get();

        foreach ($categories as $category) {
            $ownerWorkplaceId = $policyAuthorities[$category->policy_id] ?? null;

            if (!$ownerWorkplaceId) {
                continue;
            }

            DB::table('teacher_transfer_categories')
                ->where('id', $category->id)
                ->update(['transfer_owner_workplace_id' => $ownerWorkplaceId]);
        }
    }

    protected function ensureResolvedCategoryLinks(): void
    {
        $missingPolicyCount = DB::table('teacher_transfer_categories')
            ->whereNull('policy_id')
            ->count();

        if ($missingPolicyCount > 0) {
            throw new RuntimeException(
                'Transfer category alignment could not resolve policy_id for all records.'
            );
        }

        $missingOwnerCount = DB::table('teacher_transfer_categories')
            ->whereNull('transfer_owner_workplace_id')
            ->count();

        if ($missingOwnerCount > 0) {
            throw new RuntimeException(
                'Transfer category alignment could not resolve transfer_owner_workplace_id for all records.'
            );
        }
    }

    protected function makeColumnsRequired(): void
    {
        if ($this->columnAllowsNull('teacher_transfer_categories', 'policy_id')) {
            Schema::table('teacher_transfer_categories', function (Blueprint $table) {
                $table->string('policy_id', 20)->nullable(false)->change();
            });
        }

        if ($this->columnAllowsNull('teacher_transfer_categories', 'transfer_owner_workplace_id')) {
            Schema::table('teacher_transfer_categories', function (Blueprint $table) {
                $table->string('transfer_owner_workplace_id', 10)->nullable(false)->change();
            });
        }
    }

    protected function ensureForeignKeysExist(): void
    {
        if (!$this->foreignKeyExists('teacher_transfer_categories', 'tca_policy_fk')) {
            Schema::table('teacher_transfer_categories', function (Blueprint $table) {
                $table->foreign('policy_id', 'tca_policy_fk')
                    ->references('policy_id')
                    ->on('teacher_transfer_policies')
                    ->onDelete('cascade');
            });
        }

        if (!$this->foreignKeyExists('teacher_transfer_categories', 'tca_owner_wp_fk')) {
            Schema::table('teacher_transfer_categories', function (Blueprint $table) {
                $table->foreign('transfer_owner_workplace_id', 'tca_owner_wp_fk')
                    ->references('workplace_id')
                    ->on('workplaces');
            });
        }
    }

    protected function columnAllowsNull(string $tableName, string $columnName): bool
    {
        $column = collect(Schema::getColumns($tableName))
            ->firstWhere('name', $columnName);

        return (bool) ($column['nullable'] ?? false);
    }

    protected function foreignKeyExists(string $tableName, string $constraintName): bool
    {
        return collect(Schema::getForeignKeys($tableName))
            ->pluck('name')
            ->contains($constraintName);
    }
};
