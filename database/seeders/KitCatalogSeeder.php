<?php

namespace Database\Seeders;

use App\Models\KitCatalog;
use Illuminate\Database\Seeder;

class KitCatalogSeeder extends Seeder
{
    public function run(): void
    {
        // 11 نشاطاً أساسياً المحددة في استمارة Kit Adolescent
        $activities = [
            'الصمت والأصوات',
            'كيف نبدو من الداخل والخارج',
            'جمل تبدأ بكلمة «أنا..." أو " لدي..." أو "أستطيع..."',
            'ما نقدمه لأنفسنا وللآخرين',
            'قصص من منظور الحيوانات والحشرات والطيور',
            'خارطة العلاقات',
            'كتابة قصة',
            'بيئتنا',
            'ممارسة مهارات المقابلة',
            'جمع القصص',
            'أفراد عائلتنا وحوار الأسرة، والأصدقاء، والمجتمع',
        ];

        foreach ($activities as $index => $title) {
            KitCatalog::updateOrCreate(
                ['type' => 'activity', 'title_ar' => $title],
                [
                    'code' => 'ACT-' . str_pad($index + 1, 2, '0', STR_PAD_LEFT),
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }

        // 10 كفايات / مهارات أساسية محققة
        $competencies = [
            'التواصل والتعبير',
            'الهوية والاعتزاز بالنفس',
            'القيادة والتأثير',
            'حل المشكلات وإدارة الأزمات',
            'التعامل مع التوتر وإدارة العواطف',
            'التعاون والعمل الجماعي',
            'التعاطف والاحترام',
            'الأمل في المستقبل وتحديد الأهداف',
            'التفكير الناقد واتخاذ القرارات',
            'الابداع والابتكار',
        ];

        foreach ($competencies as $index => $title) {
            KitCatalog::updateOrCreate(
                ['type' => 'competency', 'title_ar' => $title],
                [
                    'code' => 'SKILL-' . str_pad($index + 1, 2, '0', STR_PAD_LEFT),
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }
    }
}
