<?php

namespace App\Services;

use App\Models\TrainingSession;
use Carbon\Carbon;

class ReportAggregationService
{
    /**
     * حساب كافة المؤشرات الإحصائية والديموغرافية تلقائياً من قائمة الحضور الفعلية
     */
    public function aggregate(TrainingSession $session): array
    {
        $sessionDate = $session->session_date ?? Carbon::now();

        // جلب قائمة الحاضرين المؤكدين فقط
        $attendees = $session->attendances()
            ->where('attended', true)
            ->with('beneficiary')
            ->get()
            ->pluck('beneficiary')
            ->filter();

        $total = $attendees->count();
        $maleCount = $attendees->where('gender', 'ذكر')->count();
        $femaleCount = $attendees->where('gender', 'أنثى')->count();

        $schooledCount = $attendees->where('is_schooled', true)->count();
        $unschooledCount = $attendees->where('is_schooled', false)->count();

        // التوزيع حسب المستوى الدراسي
        $educationLevels = [
            'ابتدائي' => $attendees->where('education_level', 'ابتدائي')->count(),
            'متوسط' => $attendees->where('education_level', 'متوسط')->count(),
            'ثانوي' => $attendees->where('education_level', 'ثانوي')->count(),
            'تكوين مهني' => $attendees->where('education_level', 'تكوين مهني')->count(),
            'غير متمدرس' => $attendees->where('education_level', 'غير متمدرس')->count(),
        ];

        // توزيع الفئات العمرية التسع المحددة في استمارة تقرير النشاط
        $ageDistribution = [
            'من 10 الى 11' => 0,
            'من 11 الى 12' => 0,
            'من 12 الى 13' => 0,
            'من 13 الى 14' => 0,
            'من 14 الى 15' => 0,
            'من 15 الى 16' => 0,
            'من 16 الى 17' => 0,
            'من 17 الى 18' => 0,
            'من 18 الى 19' => 0,
            'أخرى' => 0,
        ];

        foreach ($attendees as $b) {
            $bracket = $b->getAgeBracketAtDate($sessionDate);
            if (isset($ageDistribution[$bracket])) {
                $ageDistribution[$bracket]++;
            } else {
                $ageDistribution['أخرى']++;
            }
        }

        // ذوو الاحتياجات الخاصة والإعاقات
        $disabledBeneficiaries = $attendees->where('has_disability', true);
        $disabledCount = $disabledBeneficiaries->count();

        $disabilitiesBreakdown = [
            'حركية' => $disabledBeneficiaries->where('disability_type', 'حركية')->count(),
            'بصرية' => $disabledBeneficiaries->where('disability_type', 'بصرية')->count(),
            'الصم البكم' => $disabledBeneficiaries->where('disability_type', 'الصم البكم')->count(),
            'شيء آخر' => $disabledBeneficiaries->where('disability_type', 'شيء آخر')->count(),
        ];

        // الأمراض المزمنة
        $chronicCount = $attendees->where('has_chronic_disease', true)->count();

        return [
            'total_attendees' => $total,
            'male_count' => $maleCount,
            'female_count' => $femaleCount,
            'male_percentage' => $total > 0 ? round(($maleCount / $total) * 100, 1) : 0,
            'female_percentage' => $total > 0 ? round(($femaleCount / $total) * 100, 1) : 0,
            'schooled_count' => $schooledCount,
            'unschooled_count' => $unschooledCount,
            'education_levels' => $educationLevels,
            'age_distribution' => $ageDistribution,
            'disabled_count' => $disabledCount,
            'disabilities_breakdown' => $disabilitiesBreakdown,
            'chronic_diseases_count' => $chronicCount,
        ];
    }

    /**
     * مزامنة وتحديث الإحصائيات المحفوظة في جدول تقرير النشاط
     */
    public function syncReportCache(TrainingSession $session): void
    {
        $report = $session->activityReport;
        if (!$report) {
            return;
        }

        $stats = $this->aggregate($session);

        $report->update([
            'cached_total_attendees' => $stats['total_attendees'],
            'cached_male_count' => $stats['male_count'],
            'cached_female_count' => $stats['female_count'],
            'cached_schooled_count' => $stats['schooled_count'],
            'cached_unschooled_count' => $stats['unschooled_count'],
            'cached_age_distribution' => $stats['age_distribution'],
            'cached_education_breakdown' => $stats['education_levels'],
            'cached_disabled_count' => $stats['disabled_count'],
            'cached_disabilities_breakdown' => $stats['disabilities_breakdown'],
            'cached_chronic_diseases_count' => $stats['chronic_diseases_count'],
        ]);
    }
}
