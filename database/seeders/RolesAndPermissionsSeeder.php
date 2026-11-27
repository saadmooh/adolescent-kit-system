<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * زراعة الأدوار والصلاحيات الأولية (قابلة للتعديل لاحقاً من واجهة الإدارة)
     */
    public function run(): void
    {
        $permissions = [
            // الإحصائيات
            ['key' => 'stats.view',       'label' => 'عرض الإحصائيات',          'group' => 'الإحصائيات'],
            // المستخدمون
            ['key' => 'users.view',       'label' => 'عرض قائمة المستخدمين',    'group' => 'المستخدمون'],
            ['key' => 'users.approve',    'label' => 'اعتماد طلبات التسجيل',     'group' => 'المستخدمون'],
            ['key' => 'users.manage',     'label' => 'تعديل وحذف المستخدمين',    'group' => 'المستخدمون'],
            // الأدوار
            ['key' => 'roles.manage',     'label' => 'إدارة الأدوار والصلاحيات', 'group' => 'الأدوار'],
            // الأنشطة
            ['key' => 'sessions.view_all','label' => 'عرض كل الأنشطة',          'group' => 'الأنشطة'],
            ['key' => 'sessions.create',  'label' => 'إنشاء أنشطة',             'group' => 'الأنشطة'],
            ['key' => 'sessions.edit',    'label' => 'تعديل الأنشطة',           'group' => 'الأنشطة'],
            ['key' => 'sessions.delete',  'label' => 'حذف الأنشطة',             'group' => 'الأنشطة'],
            ['key' => 'sessions.approve', 'label' => 'اعتماد الأنشطة',          'group' => 'الأنشطة'],
            // الحضور
            ['key' => 'attendance.view',  'label' => 'عرض كشف الحضور',          'group' => 'الحضور'],
            ['key' => 'attendance.manage','label' => 'إدارة كشف الحضور',        'group' => 'الحضور'],
            // التقارير
            ['key' => 'reports.view',     'label' => 'عرض التقارير',            'group' => 'التقارير'],
            ['key' => 'reports.manage',   'label' => 'إدارة التقارير',          'group' => 'التقارير'],
            // الوسائط والتصدير
            ['key' => 'media.upload',     'label' => 'رفع الوسائط',             'group' => 'الوسائط'],
            ['key' => 'exports.print',    'label' => 'طباعة وتصدير',            'group' => 'التصدير'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['key' => $permission['key']], $permission);
        }

        $allPermissionIds = Permission::pluck('id');

        // الدور 1: مدير النظام — كل الصلاحيات
        $admin = Role::firstOrCreate(
            ['slug' => 'admin'],
            ['name' => 'مدير النظام', 'is_builtin' => true, 'description' => 'صلاحيات كاملة على المنظومة']
        );
        $admin->permissions()->sync($allPermissionIds);

        // الدور 2: الميسر الرئيسي — أنشطته + الحضور + التقارير
        $facilitator = Role::firstOrCreate(
            ['slug' => 'facilitator'],
            ['name' => 'ميسر رئيسي', 'is_builtin' => true, 'description' => 'إنشاء وإدارة أنشطته الميدانية']
        );
        $facilitator->permissions()->sync(
            Permission::whereIn('key', [
                'stats.view',
                'sessions.create', 'sessions.edit', 'sessions.delete',
                'attendance.view', 'attendance.manage',
                'reports.view', 'reports.manage',
                'media.upload', 'exports.print',
            ])->pluck('id')
        );

        // الدور 3: الميسر المساعد — عرض وحده + إدخال الحضور والتقرير
        $assistant = Role::firstOrCreate(
            ['slug' => 'assistant'],
            ['name' => 'ميسر مساعد', 'is_builtin' => true, 'description' => 'إدخال الحضور والتقارير للجلسات المسندة إليه']
        );
        $assistant->permissions()->sync(
            Permission::whereIn('key', [
                'attendance.manage', 'reports.manage', 'exports.print',
            ])->pluck('id')
        );
    }
}
