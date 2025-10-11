<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounts_taxes', function (Blueprint $table) {
            $table->boolean('is_default')->default(false)->after('amount');
        });

        $rows = DB::table('accounts_taxes')
            ->select('company_id', DB::raw('MIN(id) as id'))
            ->groupBy('company_id')
            ->get();

        foreach ($rows as $row) {
            DB::table('accounts_taxes')
                ->where('id', $row->id)
                ->update(['is_default' => true]);
        }
    }

    public function down(): void
    {
        Schema::table('accounts_taxes', function (Blueprint $table) {
            $table->dropColumn('is_default');
        });
    }
};
