<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Permissions\Permission as PermissionKey;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $groups = PermissionKey::groups();
        $displayNames = PermissionKey::displayNames();

        foreach ($groups as $group => $keys) {
            foreach ($keys as $key) {
                Permission::firstOrCreate(
                    ['key' => $key],
                    [
                        'display_name' => $displayNames[$key] ?? $key,
                        'group' => $group,
                    ]
                );
            }
        }

        $this->command?->info('Permissions seeded successfully.');
    }
}
