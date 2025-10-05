<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees_employees', function (Blueprint $table) {
            $table->string('work_address')->nullable()->after('address_id');
        });

        $employees = DB::table('employees_employees')
            ->whereNotNull('address_id')
            ->get(['id', 'address_id']);

        if ($employees->isEmpty()) {
            return;
        }

        $partnerNamesById = DB::table('partners_partners')
            ->whereIn('id', $employees->pluck('address_id')->all())
            ->pluck('name', 'id');

        foreach ($employees as $employee) {
            $address = $partnerNamesById->get($employee->address_id);

            if (! $address) {
                continue;
            }

            DB::table('employees_employees')
                ->where('id', $employee->id)
                ->update(['work_address' => $address]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees_employees', function (Blueprint $table) {
            $table->dropColumn('work_address');
        });
    }
};
