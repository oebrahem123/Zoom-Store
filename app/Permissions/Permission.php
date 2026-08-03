<?php

namespace App\Permissions;

class Permission
{
    const DASHBOARD_VIEW = 'dashboard.view';

    const EMPLOYEES_VIEW = 'employees.view';
    const EMPLOYEES_CREATE = 'employees.create';
    const EMPLOYEES_EDIT = 'employees.edit';
    const EMPLOYEES_TOGGLE_STATUS = 'employees.toggle-status';

    const PRODUCTS_VIEW = 'products.view';
    const PRODUCTS_CREATE = 'products.create';
    const PRODUCTS_EDIT = 'products.edit';
    const PRODUCTS_DELETE = 'products.delete';

    const CATEGORIES_VIEW = 'categories.view';
    const CATEGORIES_CREATE = 'categories.create';
    const CATEGORIES_EDIT = 'categories.edit';
    const CATEGORIES_DELETE = 'categories.delete';

    const ORDERS_VIEW = 'orders.view';
    const ORDERS_STATUS = 'orders.status';
    const ORDERS_DESIGN_REVIEW = 'orders.design-review';

    const SHIPMENTS_VIEW = 'shipments.view';
    const SHIPMENTS_WORKFLOW = 'shipments.workflow';

    const DESIGNS_VIEW = 'designs.view';
    const DESIGNS_CREATE = 'designs.create';
    const DESIGNS_EDIT = 'designs.edit';
    const DESIGNS_DELETE = 'designs.delete';

    const REPORTS_VIEW = 'reports.view';
    const SETTINGS_VIEW = 'settings.view';

    const DELETE_LOGS_VIEW = 'delete-logs.view';
    const AUDIT_LOGS_VIEW = 'audit-logs.view';

    public static function all(): array
    {
        return [
            self::DASHBOARD_VIEW,
            self::EMPLOYEES_VIEW,
            self::EMPLOYEES_CREATE,
            self::EMPLOYEES_EDIT,
            self::EMPLOYEES_TOGGLE_STATUS,
            self::PRODUCTS_VIEW,
            self::PRODUCTS_CREATE,
            self::PRODUCTS_EDIT,
            self::PRODUCTS_DELETE,
            self::CATEGORIES_VIEW,
            self::CATEGORIES_CREATE,
            self::CATEGORIES_EDIT,
            self::CATEGORIES_DELETE,
            self::ORDERS_VIEW,
            self::ORDERS_STATUS,
            self::ORDERS_DESIGN_REVIEW,
            self::SHIPMENTS_VIEW,
            self::SHIPMENTS_WORKFLOW,
            self::DESIGNS_VIEW,
            self::DESIGNS_CREATE,
            self::DESIGNS_EDIT,
            self::DESIGNS_DELETE,
            self::REPORTS_VIEW,
            self::SETTINGS_VIEW,
            self::DELETE_LOGS_VIEW,
            self::AUDIT_LOGS_VIEW,
        ];
    }

    public static function groups(): array
    {
        return [
            'dashboard' => [self::DASHBOARD_VIEW],
            'employees' => [self::EMPLOYEES_VIEW, self::EMPLOYEES_CREATE, self::EMPLOYEES_EDIT, self::EMPLOYEES_TOGGLE_STATUS],
            'products' => [self::PRODUCTS_VIEW, self::PRODUCTS_CREATE, self::PRODUCTS_EDIT, self::PRODUCTS_DELETE],
            'categories' => [self::CATEGORIES_VIEW, self::CATEGORIES_CREATE, self::CATEGORIES_EDIT, self::CATEGORIES_DELETE],
            'orders' => [self::ORDERS_VIEW, self::ORDERS_STATUS, self::ORDERS_DESIGN_REVIEW],
            'shipments' => [self::SHIPMENTS_VIEW, self::SHIPMENTS_WORKFLOW],
            'logs' => [self::DELETE_LOGS_VIEW, self::AUDIT_LOGS_VIEW],
            'designs' => [self::DESIGNS_VIEW, self::DESIGNS_CREATE, self::DESIGNS_EDIT, self::DESIGNS_DELETE],
            'reports' => [self::REPORTS_VIEW],
            'settings' => [self::SETTINGS_VIEW],
        ];
    }

    public static function displayNames(): array
    {
        return [
            self::DASHBOARD_VIEW => 'عرض لوحة التحكم',
            self::EMPLOYEES_VIEW => 'عرض الموظفين',
            self::EMPLOYEES_CREATE => 'إضافة موظف',
            self::EMPLOYEES_EDIT => 'تعديل موظف',
            self::EMPLOYEES_TOGGLE_STATUS => 'تفعيل/تعطيل موظف',
            self::PRODUCTS_VIEW => 'عرض المنتجات',
            self::PRODUCTS_CREATE => 'إضافة منتج',
            self::PRODUCTS_EDIT => 'تعديل منتج',
            self::PRODUCTS_DELETE => 'حذف منتج',
            self::CATEGORIES_VIEW => 'عرض الأقسام',
            self::CATEGORIES_CREATE => 'إضافة قسم',
            self::CATEGORIES_EDIT => 'تعديل قسم',
            self::CATEGORIES_DELETE => 'حذف قسم',
            self::ORDERS_VIEW => 'عرض الطلبات',
            self::ORDERS_STATUS => 'تحديث حالة الطلب',
            self::ORDERS_DESIGN_REVIEW => 'مراجعة التصاميم',
            self::SHIPMENTS_VIEW => 'عرض الشحنات',
            self::SHIPMENTS_WORKFLOW => 'إجراءات الشحن',
            self::DESIGNS_VIEW => 'عرض التصميمات',
            self::DESIGNS_CREATE => 'إضافة تصميم',
            self::DESIGNS_EDIT => 'تعديل تصميم',
            self::DESIGNS_DELETE => 'حذف تصميم',
            self::REPORTS_VIEW => 'عرض التقارير',
            self::SETTINGS_VIEW => 'عرض الإعدادات',
            self::DELETE_LOGS_VIEW => 'عرض سجل الحذف',
            self::AUDIT_LOGS_VIEW => 'عرض سجل النشاط',
        ];
    }
}
