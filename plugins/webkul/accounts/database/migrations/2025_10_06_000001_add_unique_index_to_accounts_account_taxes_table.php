<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Clean duplicates in a DB-agnostic way before adding unique index
        $duplicates = \Illuminate\Support\Facades\DB::table('accounts_account_taxes')
            ->select('account_id', 'tax_id', \Illuminate\Support\Facades\DB::raw('COUNT(*) as cnt'))
            ->groupBy('account_id', 'tax_id')
            ->having('cnt', '>', 1)
            ->get();

        foreach ($duplicates as $dup) {
            // Delete (cnt - 1) arbitrary duplicates for this pair
            $toRemove = max(0, (int) $dup->cnt - 1);
            while ($toRemove > 0) {
                // Delete one row at a time for the pair; LIMIT is supported on MySQL
                \Illuminate\Support\Facades\DB::table('accounts_account_taxes')
                    ->where('account_id', $dup->account_id)
                    ->where('tax_id', $dup->tax_id)
                    ->limit(1)
                    ->delete();
                $toRemove--;
            }
        }

        Schema::table('accounts_account_taxes', function (Blueprint $table) {
            $table->unique(['account_id', 'tax_id']);
        });
    }

    public function down(): void
    {
        Schema::table('accounts_account_taxes', function (Blueprint $table) {
            $table->dropUnique('accounts_account_taxes_account_id_tax_id_unique');
        });
    }
};
