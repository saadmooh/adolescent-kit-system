<?php

namespace Database\Seeders;

use App\Models\ActivityReport;
use App\Models\Attendance;
use App\Models\Beneficiary;
use App\Models\Institution;
use App\Models\TrainingSession;
use App\Models\User;
use App\Services\ReportAggregationService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(KitCatalogSeeder::class);
        $this->call(RolesAndPermissionsSeeder::class);

        // 1. المستخدمون والميسرون
        $admin = User::firstOrCreate(
            ['email' => 'admin@adolescent-kit.org'],
            [
                'name' => 'مدير النظام',
                'phone' => '0550123456',
                'role' => 'admin',
                'wilaya' => 'الجزائر',
                'municipality' => 'الجزائر الوسطى',
                'password' => Hash::make('password123'),
            ]
        );

        $facilitator1 = User::firstOrCreate(
            ['email' => 'facilitator1@adolescent-kit.org'],
            [
                'name' => 'أحمد بلقاسم (ميسر رئيسي)',
                'phone' => '0661234567',
                'role' => 'facilitator',
                'wilaya' => 'الجزائر',
                'municipality' => 'باب الوادي',
                'password' => Hash::make('password123'),
            ]
        );

        $facilitator2 = User::firstOrCreate(
            ['email' => 'facilitator2@adolescent-kit.org'],
            [
                'name' => 'سارة المنصوري (ميسرة رئيسية)',
                'phone' => '0770987654',
                'role' => 'facilitator',
                'wilaya' => 'الجزائر',
                'municipality' => 'بولوغين',
                'password' => Hash::make('password123'),
            ]
        );

        // 2. المؤسسة المستضيفة
        $institution = Institution::firstOrCreate(
            ['name' => 'متوسطة الشهيد العربي بن مهيدي'],
            [
                'wilaya' => 'الجزائر',
                'municipality' => 'بولوغين',
                'address' => 'شارع الشهداء، رقم 15',
                'category' => 'متمدرسين',
                'contact_person_name' => 'محمد رابحي',
                'contact_person_title' => 'مدير المؤسسة',
                'contact_person_email' => 'rabhi.med@education.dz',
                'contact_person_phone' => '021987654',
            ]
        );

        // 3. جلسة تدريبية
        $session = TrainingSession::firstOrCreate(
            [
                'institution_id' => $institution->id,
                'session_date' => Carbon::parse('2026-11-20'),
            ],
            [
                'title' => 'تدريب حقيبة الابتكار والتعبير لليافعين Kit Adolescent - الفوج الأول',
                'venue' => 'قاعة الأنشطة الثقافية والرياضية',
                'wilaya' => 'الجزائر',
                'municipality' => 'بولوغين',
                'lead_facilitator_1_id' => $facilitator1->id,
                'lead_facilitator_2_id' => $facilitator2->id,
                'assistant_facilitators' => [
                    'فاطمة الزهراء قاسمي',
                    'يوسف قدور',
                    'إيمان حداد',
                    'كريم بن عودة'
                ],
                'transit_from_municipality' => 'باب الوادي',
                'transit_to_municipality' => 'بولوغين',
                'status' => 'completed',
            ]
        );

        // 4. تسجيل 20 يافعاً واقعياً بأعمار ومستويات مختلفة
        $sampleAdolescents = [
            ['full_name' => 'ياسين حمادي', 'birth' => '2013-05-14', 'gender' => 'ذكر', 'edu' => 'متوسط', 'schooled' => true, 'chronic' => false, 'disability' => false],
            ['full_name' => 'أمينة براهمي', 'birth' => '2014-02-19', 'gender' => 'أنثى', 'edu' => 'متوسط', 'schooled' => true, 'chronic' => false, 'disability' => false],
            ['full_name' => 'أنس سعيدي', 'birth' => '2012-08-30', 'gender' => 'ذكر', 'edu' => 'متوسط', 'schooled' => true, 'chronic' => true, 'chronic_detail' => 'حساسية صدرية (ربو)', 'disability' => false],
            ['full_name' => 'مريم بن الشيخ', 'birth' => '2011-11-12', 'gender' => 'أنثى', 'edu' => 'ثانوي', 'schooled' => true, 'chronic' => false, 'disability' => false],
            ['full_name' => 'رياض زواوي', 'birth' => '2015-03-25', 'gender' => 'ذكر', 'edu' => 'ابتدائي', 'schooled' => true, 'chronic' => false, 'disability' => false],
            ['full_name' => 'نور الهدى مزيان', 'birth' => '2013-07-08', 'gender' => 'أنثى', 'edu' => 'متوسط', 'schooled' => true, 'chronic' => false, 'disability' => true, 'dis_type' => 'حركية', 'dis_det' => 'إعاقة خفيفة بالطرف السفلي'],
            ['full_name' => 'صهيب لعربي', 'birth' => '2010-09-03', 'gender' => 'ذكر', 'edu' => 'ثانوي', 'schooled' => true, 'chronic' => false, 'disability' => false],
            ['full_name' => 'خديجة بوخاري', 'birth' => '2012-04-16', 'gender' => 'أنثى', 'edu' => 'متوسط', 'schooled' => true, 'chronic' => false, 'disability' => false],
            ['full_name' => 'عبد الرحمن طيبي', 'birth' => '2009-12-01', 'gender' => 'ذكر', 'edu' => 'تكوين مهني', 'schooled' => true, 'chronic' => false, 'disability' => false],
            ['full_name' => 'لينا غربي', 'birth' => '2014-06-22', 'gender' => 'أنثى', 'edu' => 'متوسط', 'schooled' => true, 'chronic' => false, 'disability' => false],
            ['full_name' => 'إسماعيل شارف', 'birth' => '2013-10-10', 'gender' => 'ذكر', 'edu' => 'متوسط', 'schooled' => true, 'chronic' => false, 'disability' => true, 'dis_type' => 'بصرية', 'dis_det' => 'ضعف بصر شديد'],
            ['full_name' => 'وفاء بوعلام', 'birth' => '2011-01-18', 'gender' => 'أنثى', 'edu' => 'متوسط', 'schooled' => true, 'chronic' => false, 'disability' => false],
            ['full_name' => 'وليد بلحاج', 'birth' => '2012-05-09', 'gender' => 'ذكر', 'edu' => 'متوسط', 'schooled' => true, 'chronic' => false, 'disability' => false],
            ['full_name' => 'سناء محفوض', 'birth' => '2015-08-14', 'gender' => 'أنثى', 'edu' => 'ابتدائي', 'schooled' => true, 'chronic' => false, 'disability' => false],
            ['full_name' => 'أيوب بن عيسى', 'birth' => '2008-04-05', 'gender' => 'ذكر', 'edu' => 'تكوين مهني', 'schooled' => true, 'chronic' => true, 'chronic_detail' => 'سكري أطفال نوع 1', 'disability' => false],
            ['full_name' => 'شيماء تواتي', 'birth' => '2013-09-29', 'gender' => 'أنثى', 'edu' => 'متوسط', 'schooled' => true, 'chronic' => false, 'disability' => false],
            ['full_name' => 'بلال معوش', 'birth' => '2011-03-15', 'gender' => 'ذكر', 'edu' => 'متوسط', 'schooled' => true, 'chronic' => false, 'disability' => false],
            ['full_name' => 'آية رحموني', 'birth' => '2014-11-20', 'gender' => 'أنثى', 'edu' => 'متوسط', 'schooled' => true, 'chronic' => false, 'disability' => false],
            ['full_name' => 'حمزة قاسم', 'birth' => '2010-06-07', 'gender' => 'ذكر', 'edu' => 'ثانوي', 'schooled' => true, 'chronic' => false, 'disability' => false],
            ['full_name' => 'هاجر عمروش', 'birth' => '2012-02-28', 'gender' => 'أنثى', 'edu' => 'متوسط', 'schooled' => true, 'chronic' => false, 'disability' => false],
        ];

        foreach ($sampleAdolescents as $idx => $data) {
            $beneficiary = Beneficiary::firstOrCreate(
                ['full_name' => $data['full_name']],
                [
                    'birth_date' => $data['birth'],
                    'gender' => $data['gender'],
                    'municipality' => 'بولوغين',
                    'education_level' => $data['edu'],
                    'is_schooled' => $data['schooled'],
                    'has_chronic_disease' => $data['chronic'],
                    'chronic_disease_details' => $data['chronic_detail'] ?? null,
                    'has_disability' => $data['disability'],
                    'disability_type' => $data['dis_type'] ?? null,
                    'disability_details' => $data['dis_det'] ?? null,
                ]
            );

            Attendance::firstOrCreate(
                [
                    'training_session_id' => $session->id,
                    'beneficiary_id' => $beneficiary->id,
                ],
                [
                    'serial_number' => $idx + 1,
                    'attended' => true,
                ]
            );
        }

        // 5. إنشاء تقرير النشاط
        $report = ActivityReport::firstOrCreate(
            ['training_session_id' => $session->id],
            [
                'selected_activities' => [
                    'الصمت والأصوات',
                    'كيف نبدو من الداخل والخارج',
                    'جمل تبدأ بكلمة «أنا...» أو «لدي...» أو «أستطيع...»',
                    'خارطة العلاقات',
                ],
                'selected_competencies' => [
                    'التواصل والتعبير',
                    'الهوية والاعتزاز بالنفس',
                    'التعاون والعمل الجماعي',
                    'التعاطف والاحترام',
                    'الابداع والابتكار',
                ],
                'facilities' => 'توفير قاعة ملائمة، أجهزة عرض، ترحيب وتعاون كبير من إدارة المؤسسة والطاقم التربوي.',
                'difficulties' => 'ضيق الوقت المخصص لتنفيذ جميع الأنشطة المخططة مع العدد الكبير لليافعين.',
                'strengths' => 'تفاعل عالي وحماس من طرف اليافعين، مشاركة فعالة من الإناث والذكور على حد سواء.',
                'weaknesses' => 'الحاجة إلى مكبرات صوت إضافية لنشاط الصمت والأصوات.',
                'engagement_notes' => 'أظهر اليافعون قدرة ممتازة على التعبير عن المشاعر وتحديد نقاط قوتهم الشخصية.',
                'attendance_notes' => 'انضباط كامل في المواعيد وحضور جميع المسجلين دون غياب.',
                'general_remarks' => 'لوحظ اندماج سريع لذوي الاحتياجات الخاصة وسط أقرانهم بأريحية تامة.',
                'recommendations' => 'برمجة دورات لاحقة معمقة في مهارات حل المشكلات والتفكير الناقد.',
                'rapporteur_id' => $facilitator1->id,
                'rapporteur_wilaya' => 'الجزائر',
                'rapporteur_date' => Carbon::parse('2026-11-20'),
                'host_contact_name' => 'محمد رابحي',
                'host_contact_title' => 'مدير المؤسسة',
                'host_contact_phone' => '021987654',
                'host_contact_email' => 'rabhi.med@education.dz',
                'host_wilaya' => 'الجزائر',
                'host_approval_date' => Carbon::parse('2026-11-20'),
            ]
        );

        // حساب ومزامنة الإحصائيات آلياً
        app(ReportAggregationService::class)->syncReportCache($session);
    }
}
