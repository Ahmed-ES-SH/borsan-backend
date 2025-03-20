<?php

namespace Database\Seeders;

use App\Models\About;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AboutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        About::insert([
            [
                'first_section_title_en' => 'First Section Title (EN)',
                'first_section_title_ar' => 'عنوان القسم الأول',
                'first_section_contnet_ar' => 'محتوى القسم الأول بالعربية',
                'first_section_contnet_en' => 'First section content in English',
                'second_section_title_en' => 'Second Section Title (EN)',
                'second_section_title_ar' => 'عنوان القسم الثاني',
                'second_section_contnet_ar' => 'محتوى القسم الثاني بالعربية',
                'second_section_contnet_en' => 'Second section content in English',
                'thired_section_title_en' => 'Third Section Title (EN)',
                'thired_section_title_ar' => 'عنوان القسم الثالث',
                'thired_section_contnet_ar' => 'محتوى القسم الثالث بالعربية',
                'thired_section_contnet_en' => 'Third section content in English',
                'fourth_section_title_en' => 'Fourth Section Title (EN)',
                'fourth_section_title_ar' => 'عنوان القسم الرابع',
                'fourth_section_contnet_ar' => 'محتوى القسم الرابع بالعربية',
                'fourth_section_contnet_en' => 'Fourth section content in English',
                'show_map' => true,
                'address' => '1234 Street Name, City, Country',
                'first_section_image' => null,
                'second_section_image' => null,
                'thired_section_image' => null,
                'fourth_section_image' => null,
                'main_video' => null,
                'link_video' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
