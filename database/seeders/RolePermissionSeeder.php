<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Permissions\Permission as PermissionKey;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = Role::where('name', 'super_admin')->first();
        $admin = Role::where('name', 'admin')->first();
        $designer = Role::where('name', 'designer')->first();
        $shipping = Role::where('name', 'shipping')->first();
        $customerService = Role::where('name', 'customer_service')->first();
        $accountant = Role::where('name', 'accountant')->first();

        $allPermissions = Permission::all();

        if ($superAdmin) {
            $superAdmin->permissions()->sync($allPermissions->pluck('id'));
        }

        $perm = function (string $key) use ($allPermissions) {
            return $allPermissions->firstWhere('key', $key)?->id;
        };

        if ($admin) {
            $adminPerms = PermissionKey::all();
            $adminPerms = array_diff($adminPerms, [PermissionKey::SETTINGS_VIEW]);
            $admin->permissions()->sync(
                $allPermissions->whereIn('key', $adminPerms)->pluck('id')
            );
        }

        // Designer — designs + orders view + design review
        if ($designer) {
            $designer->permissions()->sync(array_filter([
                $perm(PermissionKey::DASHBOARD_VIEW),
                $perm(PermissionKey::DESIGNS_VIEW),
                $perm(PermissionKey::DESIGNS_CREATE),
                $perm(PermissionKey::DESIGNS_EDIT),
                $perm(PermissionKey::DESIGNS_DELETE),
                $perm(PermissionKey::ORDERS_VIEW),
                $perm(PermissionKey::ORDERS_DESIGN_REVIEW),
            ]));
        }

        // Shipping — shipments, orders
        if ($shipping) {
            $shipping->permissions()->sync(array_filter([
                $perm(PermissionKey::DASHBOARD_VIEW),
                $perm(PermissionKey::ORDERS_VIEW),
                $perm(PermissionKey::SHIPMENTS_VIEW),
                $perm(PermissionKey::SHIPMENTS_WORKFLOW),
            ]));
        }

        // Customer Service — orders, shipments
        if ($customerService) {
            $customerService->permissions()->sync(array_filter([
                $perm(PermissionKey::DASHBOARD_VIEW),
                $perm(PermissionKey::ORDERS_VIEW),
                $perm(PermissionKey::ORDERS_STATUS),
                $perm(PermissionKey::ORDERS_DESIGN_REVIEW),
                $perm(PermissionKey::SHIPMENTS_VIEW),
                $perm(PermissionKey::SHIPMENTS_WORKFLOW),
            ]));
        }

        // Accountant — orders, reports
        if ($accountant) {
            $accountant->permissions()->sync(array_filter([
                $perm(PermissionKey::DASHBOARD_VIEW),
                $perm(PermissionKey::ORDERS_VIEW),
                $perm(PermissionKey::REPORTS_VIEW),
            ]));
        }

        $this->command?->info('Role permissions seeded successfully.');
    }
}
