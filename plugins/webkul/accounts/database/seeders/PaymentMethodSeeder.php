<?php

namespace Webkul\Account\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Avoid deleting to prevent FK violations from accounts_payment_method_lines.

        $user = User::first();

        $now = now();

        $paymentMethods = [
            [
                'id'           => 1,
                'code'         => 'manual',
                'payment_type' => 'inbound',
                'name'         => 'Manual Payment',
                'created_by'   => $user?->id,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'id'           => 2,
                'code'         => 'manual',
                'payment_type' => 'outbound',
                'name'         => 'Manual Payment',
                'created_by'   => $user?->id,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
        ];

        // Upsert by ID to make reseeding idempotent
        DB::table('accounts_payment_methods')->upsert($paymentMethods, ['id'], ['code', 'payment_type', 'name', 'created_by', 'created_at', 'updated_at']);
    }
}
