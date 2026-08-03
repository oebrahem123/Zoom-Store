<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'name' => 'super_admin',
                'display_name' => 'Super Admin',
                'description' => 'Full system access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'admin',
                'display_name' => 'Admin',
                'description' => 'Administrative access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'designer',
                'display_name' => 'Designer',
                'description' => 'Design management access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'shipping',
                'display_name' => 'Shipping',
                'description' => 'Shipping and tracking access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'customer_service',
                'display_name' => 'Customer Service',
                'description' => 'Customer and order management',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'accountant',
                'display_name' => 'Accountant',
                'description' => 'Orders, reports, and payments',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'customer',
                'display_name' => 'Customer',
                'description' => 'Regular website customer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
