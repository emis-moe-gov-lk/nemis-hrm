<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('teacher_transfer_application_recommendations')) {
            return;
        }

        Schema::table('teacher_transfer_application_recommendations', function (Blueprint $table) {
            if (Schema::hasColumn('teacher_transfer_application_recommendations', 'approved_by')) {
                $table->char('approved_by', 12)->nullable()->change();
            }

            if (Schema::hasColumn('teacher_transfer_application_recommendations', 'transfer_recommendation_list_id')) {
                $table->string('transfer_recommendation_list_id', 10)->nullable()->change();
            }
        });

        Schema::table('teacher_transfer_application_recommendations', function (Blueprint $table) {
            if (!Schema::hasColumn('teacher_transfer_application_recommendations', 'recommendation_status')) {
                $table->boolean('recommendation_status')
                    ->default(false)
                    ->after('remarks')
                    ->comment('true: Recommendation given, false: Pending');
            }

            if (!Schema::hasColumn('teacher_transfer_application_recommendations', 'created_by')) {
                $table->string('created_by', 12)->nullable()->after('recommendation_status');
            }

            if (!Schema::hasColumn('teacher_transfer_application_recommendations', 'updated_by')) {
                $table->string('updated_by', 12)->nullable()->after('created_by');
            }
        });

        Schema::table('teacher_transfer_application_recommendations', function (Blueprint $table) {
            if ($this->missingForeignKey('teacher_transfer_application_recommendations', 'ttar_created_by_fk')) {
                $table->foreign('created_by', 'ttar_created_by_fk')
                    ->references('people_id')
                    ->on('people');
            }

            if ($this->missingForeignKey('teacher_transfer_application_recommendations', 'ttar_updated_by_fk')) {
                $table->foreign('updated_by', 'ttar_updated_by_fk')
                    ->references('people_id')
                    ->on('people');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('teacher_transfer_application_recommendations')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('teacher_transfer_application_recommendations', function (Blueprint $table) {
            $this->dropForeignIfExists(
                $table,
                'teacher_transfer_application_recommendations',
                'ttar_created_by_fk'
            );

            $this->dropForeignIfExists(
                $table,
                'teacher_transfer_application_recommendations',
                'teacher_transfer_application_recommendations_created_by_foreign'
            );

            $this->dropForeignIfExists(
                $table,
                'teacher_transfer_application_recommendations',
                'ttar_updated_by_fk'
            );

            $this->dropForeignIfExists(
                $table,
                'teacher_transfer_application_recommendations',
                'teacher_transfer_application_recommendations_updated_by_foreign'
            );

            $dropColumns = [];

            if (Schema::hasColumn('teacher_transfer_application_recommendations', 'recommendation_status')) {
                $dropColumns[] = 'recommendation_status';
            }

            if (Schema::hasColumn('teacher_transfer_application_recommendations', 'created_by')) {
                $dropColumns[] = 'created_by';
            }

            if (Schema::hasColumn('teacher_transfer_application_recommendations', 'updated_by')) {
                $dropColumns[] = 'updated_by';
            }

            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });

        Schema::table('teacher_transfer_application_recommendations', function (Blueprint $table) {
            if (Schema::hasColumn('teacher_transfer_application_recommendations', 'approved_by')) {
                $table->char('approved_by', 12)->nullable(false)->change();
            }

            if (Schema::hasColumn('teacher_transfer_application_recommendations', 'transfer_recommendation_list_id')) {
                $table->string('transfer_recommendation_list_id', 10)->nullable(false)->change();
            }
        });
    }

    private function dropForeignIfExists(
        Blueprint $table,
        string $tableName,
        string $constraintName
    ): void {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        if (!$this->missingForeignKey($tableName, $constraintName)) {
            $table->dropForeign($constraintName);
        }
    }

    private function missingForeignKey(string $tableName, string $constraintName): bool
    {
        return ! collect(Schema::getForeignKeys($tableName))
            ->pluck('name')
            ->contains($constraintName);
    }
};
