<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('teacher_transfer_recommendation_lists')) {
            return;
        }

        if (! Schema::hasColumn('teacher_transfer_recommendation_lists', 'rejects_application')) {
            Schema::table('teacher_transfer_recommendation_lists', function (Blueprint $table) {
                $table->boolean('rejects_application')
                    ->default(false)
                    ->after('decision')
                    ->comment('true: choosing this recommendation marks the application as not recommended/rejected');
            });
        }

        DB::table('teacher_transfer_recommendation_lists')
            ->where(function ($query) {
                foreach ([
                    'reject',
                    'cannot be released',
                    'cant be released',
                    'can t be released',
                    'not qualified',
                    'not recomemded',
                    'not recommended',
                ] as $term) {
                    $query->orWhereRaw('LOWER(decision) LIKE ?', ['%' . $term . '%']);
                }
            })
            ->update(['rejects_application' => true]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('teacher_transfer_recommendation_lists')) {
            return;
        }

        if (Schema::hasColumn('teacher_transfer_recommendation_lists', 'rejects_application')) {
            Schema::table('teacher_transfer_recommendation_lists', function (Blueprint $table) {
                $table->dropColumn('rejects_application');
            });
        }
    }
};
