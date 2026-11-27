<?php

namespace App\Traits;

use App\Models\TrainingSession;
use Illuminate\Support\Facades\Auth;

trait AuthorizesSessionAccess
{
    /**
     * التحقق الصارم من صلاحية المستخدم للوصول إلى الجلسة وتعديلها
     * (فقط المدير، أو الميسر الرئيسي 1 و 2، أو المساعدون المعينون بالجلسة)
     */
    protected function authorizeSessionAccess(TrainingSession $session): void
    {
        $user = Auth::user();
        if (!$user) {
            abort(401, 'يجب تسجيل الدخول أولاً.');
        }

        // من يملك صلاحية عرض كل الأنشطة يتجاوز قيد الملكية
        if ($user->hasPermission('sessions.view_all')) {
            return;
        }

        // الميسران الرئيسيان المعينان للجلسة
        if ($session->lead_facilitator_1_id === $user->id || $session->lead_facilitator_2_id === $user->id) {
            return;
        }

        // المساعدون المعينون رسمياً في الجلسة
        $assistants = $session->assistant_facilitators ?? [];
        if (in_array($user->name, $assistants, true) || in_array((string) $user->id, $assistants, true)) {
            return;
        }

        abort(403, 'غير مصرح لك بالوصول إلى هذه الجلسة.');
    }
}
