<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

/**
 * إدارة الأدوار والصلاحيات ديناميكياً (RBAC)
 */
class RoleController extends Controller
{
    public function index()
    {
        return view('roles.index', [
            'roles'       => Role::with('permissions')->get(),
            'permissions' => Permission::all()->groupBy('group'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:100',
            'slug'          => 'required|string|max:60|regex:/^[a-z0-9_\-]+$/|unique:roles,slug',
            'description'   => 'nullable|string|max:255',
            'permissions'   => 'array',
            'permissions.*' => 'exists:permissions,id',
        ], [
            'name.required' => 'يرجى إدخال اسم الدور.',
            'slug.regex'    => 'المعرّف يجب أن يكون بالإنجليزية فقط (أحرف صغيرة وأرقام وشرطات وشرطة سفلية).',
            'slug.unique'   => 'هذا المعرّف مستخدم لدور آخر.',
        ]);

        $role = Role::create([
            'slug'        => $validated['slug'],
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_builtin'  => false,
        ]);
        $role->permissions()->sync($validated['permissions'] ?? []);

        return back()->with('success', "تم إنشاء دور «{$role->name}» بنجاح.");
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:100',
            'description'   => 'nullable|string|max:255',
            'permissions'   => 'array',
            'permissions.*' => 'exists:permissions,id',
        ], [
            'name.required' => 'يرجى إدخال اسم الدور.',
        ]);

        $grantedPermissions = array_map('intval', $validated['permissions'] ?? []);
        $rolesManageId = Permission::where('key', 'roles.manage')->value('id');

        // حماية من القفل الذاتي: منع سحب roles.manage عن دور يملكه مستخدمون حاليون
        if ($role->users()->exists()
            && $role->hasPermission('roles.manage')
            && $rolesManageId
            && !in_array((int) $rolesManageId, $grantedPermissions, true)) {
            return back()->withErrors([
                'error' => 'لا يمكن سحب صلاحية إدارة الأدوار عن دور مُسنَد لمستخدمين حاليين (لتجنب قفل النظام على نفسه).',
            ]);
        }

        $role->update([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? $role->description,
        ]);
        $role->permissions()->sync($grantedPermissions);

        return back()->with('success', "تم تحديث صلاحيات دور «{$role->name}».");
    }

    public function destroy(Role $role)
    {
        if ($role->is_builtin) {
            return back()->withErrors(['error' => 'لا يمكن حذف دور مدمج في النظام.']);
        }

        if ($role->users()->exists()) {
            return back()->withErrors(['error' => 'لا يمكن حذف دور مُسنَد لمستخدمين حاليين. غيّر أدوارهم أولاً.']);
        }

        $role->delete();

        return back()->with('success', 'تم حذف الدور بنجاح.');
    }
}
