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
        Schema::table('partners_partners', function (Blueprint $table) {
            if (! Schema::hasColumn('partners_partners', 'language')) {
                $table->string('language', 5)->nullable()->after('color');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partners_partners', function (Blueprint $table) {
            if (Schema::hasColumn('partners_partners', 'language')) {
                $table->dropColumn('language');
            }
        });
    }
};