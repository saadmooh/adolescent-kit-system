<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
        ], [
            'email.unique' => 'البريد الإلكتروني مسجل مسبقاً لميسر آخر.',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'wilaya' => $validated['wilaya'] ?? null,
            'municipality' => $validated['municipality'] ?? null,
            'password' => Hash::make('password123'), // كلمة مرور افتراضية
        ]);

        return redirect()->to(route('admin.dashboard') . '#facilitators-section')
            ->with('success', "تمت إضافة الميسر ({$validated['name']}) بنجاح في لوحة التحكم.");
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
            'password' => 'nullable|string|min:6',
        ], [
            'email.unique' => 'البريد الإلكتروني مسجل مسبقاً لمستخدم آخر.',
            'password.min' => 'كلمة المرور يجب أن لا تقل عن 6 أحرف.',
        ]);

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
            ->with('success', "تم تحديث بيانات الميسر ({$user->name}) في لوحة التحكم بنجاح.");
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
