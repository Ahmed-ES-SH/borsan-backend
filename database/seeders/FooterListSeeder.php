<?php

namespace Database\Seeders;

use App\Models\FooterLink;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FooterListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        FooterLink::truncate();
        $links = [
            ['list_id' => 1, 'link_title_en' => 'Home', 'link_title_ar' => 'الرئيسية', 'link_url' => '/home'],
            ['list_id' => 1, 'link_title_en' => 'About Us', 'link_title_ar' => 'من نحن', 'link_url' => '/about'],
            ['list_id' => 1, 'link_title_en' => 'Contact', 'link_title_ar' => 'اتصل بنا', 'link_url' => '/contact'],

            ['list_id' => 2, 'link_title_en' => 'Services', 'link_title_ar' => 'الخدمات', 'link_url' => '/services'],
            ['list_id' => 2, 'link_title_en' => 'Portfolio', 'link_title_ar' => 'أعمالنا', 'link_url' => '/portfolio'],
            ['list_id' => 2, 'link_title_en' => 'Careers', 'link_title_ar' => 'الوظائف', 'link_url' => '/careers'],

            ['list_id' => 3, 'link_title_en' => 'Blog', 'link_title_ar' => 'المدونة', 'link_url' => '/blog'],
            ['list_id' => 3, 'link_title_en' => 'News', 'link_title_ar' => 'الأخبار', 'link_url' => '/news'],
            ['list_id' => 3, 'link_title_en' => 'Press', 'link_title_ar' => 'الصحافة', 'link_url' => '/press'],

            ['list_id' => 4, 'link_title_en' => 'Privacy Policy', 'link_title_ar' => 'سياسة الخصوصية', 'link_url' => '/privacy-policy'],
            ['list_id' => 4, 'link_title_en' => 'Terms of Service', 'link_title_ar' => 'شروط الخدمة', 'link_url' => '/terms'],
            ['list_id' => 4, 'link_title_en' => 'Support', 'link_title_ar' => 'الدعم', 'link_url' => '/support'],

            ['list_id' => 5, 'link_title_en' => 'FAQ', 'link_title_ar' => 'الأسئلة الشائعة', 'link_url' => '/faq'],
            ['list_id' => 5, 'link_title_en' => 'Help Center', 'link_title_ar' => 'مركز المساعدة', 'link_url' => '/help-center'],
            ['list_id' => 5, 'link_title_en' => 'Contact Sales', 'link_title_ar' => 'اتصل بالمبيعات', 'link_url' => '/contact-sales'],
        ];


        FooterLink::insert($links);
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
