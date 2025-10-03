<?php

namespace Webkul\Employee\Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\Security\Models\User;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\DB;

class SkillTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('employees_skills')->delete();

        DB::table('employees_skill_types')->delete();

        $user = User::first();

        $skillTypes = [
            [
                'id'         => 1,
                'name'       => 'Languages',
                'color'      => 'danger',
                'is_active'  => 1,
                'creator_id' => $user?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'         => 2,
                'name'       => 'Soft Skills',
                'color'      => 'success',
                'is_active'  => 1,
                'creator_id' => $user?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'         => 3,
                'name'       => 'Programming Languages',
                'color'      => 'warning',
                'is_active'  => 1,
                'creator_id' => $user?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'         => 4,
                'name'       => 'IT',
                'color'      => 'info',
                'is_active'  => 1,
                'creator_id' => $user?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'         => 5,
                'name'       => 'Marketing',
                'color'      => 'gray',
                'is_active'  => 1,
                'creator_id' => $user?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'         => 6,
                'name'       => 'Technical & Mechanical',
                'color'      => 'primary',
                'is_active'  => 1,
                'creator_id' => $user?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'         => 7,
                'name'       => 'Construction',
                'color'      => 'info',
                'is_active'  => 1,
                'creator_id' => $user?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'         => 8,
                'name'       => 'Maintenance & Repair',
                'color'      => 'success',
                'is_active'  => 1,
                'creator_id' => $user?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'         => 9,
                'name'       => 'Manufacturing & Production',
                'color'      => 'warning',
                'is_active'  => 1,
                'creator_id' => $user?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'         => 10,
                'name'       => 'Transport & Logistics',
                'color'      => 'danger',
                'is_active'  => 1,
                'creator_id' => $user?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'         => 11,
                'name'       => 'Safety & Compliance',
                'color'      => 'primary',
                'is_active'  => 1,
                'creator_id' => $user?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('employees_skill_types')->insert($skillTypes);
    }
}
