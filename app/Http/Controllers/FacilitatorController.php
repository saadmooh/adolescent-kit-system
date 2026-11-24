<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FacilitatorController extends Controller
{
    public function index()
    {
        return redirect()->to(route('admin.dashboard') . '#facilitators-section');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:50',
            'role' => 'required|in:facilitator,assistant,admin',
            'wilaya' => 'nullable|string|max:100',
            'municipality' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:8',
        ], [
            'name.required' => 'يرجى إدخال اسم ولقب الميسر.',
            'email.required' => 'يرجى إدخال البريد الإلكتروني.',
            'email.unique' => 'البريد الإلكتروني مسجل مسبقاً لمستخدم آخر.',
            'password.min' => 'كلمة المرور يجب أن لا تقل عن 8 أحرف.',
        ]);

        $plainPassword = !empty($validated['password'])
            ? $validated['password']
            : Str::random(10) . rand(10, 99);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'wilaya' => $validated['wilaya'] ?? null,
            'municipality' => $validated['municipality'] ?? null,
            'password' => Hash::make($plainPassword),
            'approval_status' => 'approved',
            'is_active' => true,
            'approved_at' => now(),
            'admin_notes' => 'تم إنشاء الحساب واعتماده مباشرة من طرف مدير النظام.',
        ]);

        $roleArabic = match($user->role) {
            'admin' => 'مسؤول نظام (Admin)',
            'facilitator' => 'ميسر رئيسي (Lead Facilitator)',
            'assistant' => 'ميسر مساعد (Assistant)',
            default => 'مستخدم',
        };

        return redirect()->to(route('admin.dashboard') . '#facilitators-section')
            ->with('success', "تم تسجيل وتفعيل حساب {$roleArabic} ({$user->name}) بنجاح.")
            ->with('new_user_credentials', [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $roleArabic,
                'password' => $plainPassword,
            ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:50',
            'role' => 'required|in:facilitator,assistant,admin',
            'wilaya' => 'nullable|string|max:100',
            'municipality' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:8',
        ], [
            'email.unique' => 'البريد الإلكتروني مسجل مسبقاً لمستخدم آخر.',
            'password.min' => 'كلمة المرور يجب أن لا تقل عن 8 أحرف.',
        ]);

        // منع المسؤول من تجريد نفسه من صلاحية الإدارة
        if ($user->id === auth()->id() && $validated['role'] !== 'admin') {
            return redirect()->to(route('admin.dashboard') . '#facilitators-section')
                ->withErrors(['error' => 'لا يمكنك تجريد حسابك الحالي من صلاحيات مسؤول النظام (Admin).']);
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;
        $user->role = $validated['role'];
        $user->wilaya = $validated['wilaya'] ?? null;
        $user->municipality = $validated['municipality'] ?? null;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->to(route('admin.dashboard') . '#facilitators-section')
            ->with('success', "تم تحديث بيانات وصلاحيات الحساب ({$user->name}) في لوحة التحكم بنجاح.");
    }

    public function approveRegistration(Request $request, User $user)
    {
        $role = $request->input('role', $user->role);
        if (!in_array($role, ['facilitator', 'assistant', 'admin'])) {
            $role = 'facilitator';
        }

        $user->update([
            'approval_status' => 'approved',
            'is_active' => true,
            'role' => $role,
            'approved_at' => now(),
            'admin_notes' => 'تم قبول واعتماد الحساب من طرف مدير النظام.',
        ]);

        return redirect()->to(route('admin.dashboard') . '#facilitators-section')
            ->with('success', "تم قبول واعتماد حساب ({$user->name}) بنجاح! أصبح بإمكانه الآن تسجيل الدخول وإدخال بيانات الأنشطة.");
    }

    public function rejectRegistration(Request $request, User $user)
    {
        $user->update([
            'approval_status' => 'rejected',
            'is_active' => false,
            'admin_notes' => $request->input('notes', 'تم رفض طلب التسجيل من طرف الإدارة.'),
        ]);

        return redirect()->to(route('admin.dashboard') . '#facilitators-section')
            ->with('success', "تم رفض طلب تسجيل ({$user->name}).");
    }

    public function toggleActiveStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->to(route('admin.dashboard') . '#facilitators-section')
                ->withErrors(['error' => 'لا يمكنك تعطيل حسابك الحالي.']);
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'تفعيل' : 'تعطيل';
        return redirect()->to(route('admin.dashboard') . '#facilitators-section')
            ->with('success', "تم {$statusText} حساب ({$user->name}) بنجاح.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->to(route('admin.dashboard') . '#facilitators-section')
                ->withErrors(['error' => 'لا يمكنك حذف حسابك الحالي.']);
        }

        $user->delete();

        return redirect()->to(route('admin.dashboard') . '#facilitators-section')
            ->with('success', 'تم حذف الميسر من القائمة بنجاح.');
    }
}
