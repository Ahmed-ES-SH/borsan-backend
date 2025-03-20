<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        // Article::truncate();

        $path = 'images/articles'; // المجلد داخل public
        $fullpath = public_path($path);

        // جلب الصور الموجودة في المجلد
        $images = scandir($fullpath);
        $imagesArray = array_filter($images, function ($image) {
            return in_array(pathinfo($image, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        });

        $idsarray = DB::table('article_categories')->pluck('id')->toArray();
        $usersidsarray = DB::table('users')->pluck('id')->toArray();

        $contents = [
            [
                'title_en' => 'Eye Care',
                'title_ar' => 'العناية بالعيون',
                'description_en' => 'We provide comprehensive eye care services for all ages, from newborns to the elderly. Our expert ophthalmologists offer advanced eye exams, early detection of eye diseases, and treatment options for conditions such as glaucoma, cataracts, and macular degeneration. We also specialize in providing personalized vision correction treatments including LASIK surgery, contact lenses, and prescription glasses. Our goal is to ensure the long-term health of your eyes and to help you maintain clear and healthy vision throughout your life. We also offer regular follow-ups and preventative care to catch any potential issues early on.',
                'description_ar' => 'نقدم خدمات شاملة للعناية بالعيون لجميع الأعمار، من حديثي الولادة إلى كبار السن. يقدم أطباؤنا المتخصصون في طب العيون فحوصات شاملة للعيون، والكشف المبكر عن أمراض العين، وخيارات علاج لحالات مثل الجلوكوما، إعتام عدسة العين، والتنكس البقعي. كما نخصص خدمات تصحيح الرؤية الشخصية مثل جراحة الليزك، العدسات اللاصقة، والنظارات الطبية. هدفنا هو ضمان صحة عيونك على المدى الطويل ومساعدتك في الحفاظ على رؤية واضحة وصحية طوال حياتك. كما نقدم متابعة دورية ورعاية وقائية للكشف المبكر عن أي مشاكل قد تظهر.',
            ],
            [
                'title_en' => 'Dental Services',
                'title_ar' => 'خدمات طب الأسنان',
                'description_en' => 'Our dental clinic offers a full range of dental services designed to maintain optimal oral health and provide solutions for various dental concerns. This includes regular check-ups, professional cleanings, fillings, and preventive treatments such as sealants and fluoride applications. We specialize in cosmetic dentistry services including teeth whitening, veneers, and crowns to enhance the appearance of your smile. Our team of experienced dental professionals also treats gum disease, tooth sensitivity, and performs root canals when necessary. We are dedicated to providing pain-free dental care and ensuring that every visit is comfortable and stress-free.',
                'description_ar' => 'عيادتنا لطب الأسنان تقدم مجموعة كاملة من الخدمات المصممة للحفاظ على صحة الفم المثلى وتقديم حلول لمختلف القضايا الأسنان. يشمل ذلك الفحوصات الدورية، التنظيف المهني، الحشوات، والعلاجات الوقائية مثل تطبيقات السيلانت والفلواريد. نحن متخصصون في خدمات طب الأسنان التجميلية مثل تبييض الأسنان، الفينير، والتيجان لتحسين مظهر ابتسامتك. كما يعالج فريقنا من المتخصصين في طب الأسنان أمراض اللثة، حساسية الأسنان، ويقوم بعمل قنوات الجذور عند الحاجة. نحن ملتزمون بتقديم رعاية أسنان خالية من الألم وضمان أن كل زيارة مريحة وخالية من التوتر.',
            ],
            [
                'title_en' => 'Cardiology',
                'title_ar' => 'أمراض القلب',
                'description_en' => 'Our cardiology department offers specialized care for heart health and cardiovascular diseases. Our team of expert cardiologists provides comprehensive heart evaluations, diagnostic testing, and personalized treatment plans for conditions such as heart disease, hypertension, arrhythmias, and high cholesterol. We offer cutting-edge diagnostic technologies such as ECGs, echocardiograms, and stress tests to monitor heart health. For patients with more serious conditions, we provide advanced procedures including angioplasty, stent placement, and heart surgery. Our goal is to help prevent heart attacks, strokes, and other cardiovascular events, ensuring the best possible quality of life for our patients.',
                'description_ar' => 'يقدم قسم أمراض القلب لدينا رعاية متخصصة لصحة القلب والأمراض القلبية الوعائية. يقدم فريق من أطباء القلب الخبراء فحوصات قلب شاملة، اختبارات تشخيصية، وخطط علاج مخصصة لحالات مثل أمراض القلب، ارتفاع ضغط الدم، اضطرابات النظم القلبي، وارتفاع الكوليسترول. نحن نقدم تقنيات تشخيصية متطورة مثل تخطيط القلب الكهربائي، تخطيط صدى القلب، واختبارات الإجهاد لمتابعة صحة القلب. بالنسبة للمرضى الذين يعانون من حالات أكثر خطورة، نقدم إجراءات متقدمة مثل بالون الأوعية الدموية، وضع الدعامة، وجراحة القلب. هدفنا هو المساعدة في الوقاية من النوبات القلبية، السكتات الدماغية، والأحداث القلبية الوعائية الأخرى، وضمان أفضل نوعية حياة ممكنة لمرضانا.',
            ],
            [
                'title_en' => 'Pediatrics',
                'title_ar' => 'طب الأطفال',
                'description_en' => 'We provide expert healthcare services for children of all ages, from newborns to adolescents. Our pediatricians are dedicated to providing high-quality medical care, focusing on preventing and treating childhood illnesses. We offer regular check-ups to monitor growth and development, administer vaccinations, and provide treatments for common pediatric issues such as asthma, allergies, ear infections, and skin conditions. Our pediatricians are also skilled in managing chronic conditions like diabetes, ADHD, and developmental delays. We emphasize a holistic approach to children’s health, incorporating both physical and emotional well-being into our care.',
                'description_ar' => 'نقدم خدمات رعاية صحية متخصصة للأطفال من جميع الأعمار، من حديثي الولادة إلى المراهقين. أطباؤنا المتخصصون في طب الأطفال ملتزمون بتقديم رعاية طبية عالية الجودة، مع التركيز على الوقاية من الأمراض وعلاجها في مرحلة الطفولة. نحن نقدم فحوصات منتظمة لمتابعة النمو والتطور، نقدم التطعيمات، ونوفر العلاجات للمشاكل الصحية الشائعة للأطفال مثل الربو، والحساسية، والتهابات الأذن، وحالات الجلد. كما يتقن أطباؤنا إدارة الحالات المزمنة مثل السكري، فرط النشاط، والتأخر في النمو. نحن نؤكد على النهج الشمولي لصحة الأطفال، حيث ندمج الرفاهية الجسدية والعاطفية في رعايتنا.',
            ],
            [
                'title_en' => 'Orthopedics',
                'title_ar' => 'جراحة العظام',
                'description_en' => 'Our orthopedic department provides specialized care for bones, joints, muscles, and ligaments. We offer treatments for a wide range of orthopedic issues including fractures, arthritis, sports injuries, back pain, and joint disorders. Our orthopedic surgeons are experts in performing surgeries such as joint replacements, arthroscopy, and bone realignment. We also provide physical therapy and rehabilitation services to help patients recover from injuries and surgeries. Our goal is to restore mobility, reduce pain, and improve quality of life for our patients.',
                'description_ar' => 'يقدم قسم جراحة العظام لدينا رعاية متخصصة للعظام والمفاصل والعضلات والأربطة. نحن نقدم علاجات لمجموعة واسعة من المشاكل العظمية مثل الكسور، التهاب المفاصل، الإصابات الرياضية، آلام الظهر، واضطرابات المفاصل. جراحونا المتخصصون في جراحة العظام خبراء في إجراء عمليات مثل استبدال المفاصل، منظار المفصل، وإعادة ترتيب العظام. كما نقدم خدمات العلاج الطبيعي وإعادة التأهيل لمساعدة المرضى على التعافي من الإصابات والجراحة. هدفنا هو استعادة الحركة، تقليل الألم، وتحسين جودة حياة مرضانا.',
            ],
        ];

        $articles = [];

        foreach ($contents as $content) {
            $randomImage = $imagesArray[array_rand($imagesArray)];
            $imagePath = "$fullpath/$randomImage";

            // رفع الصورة إلى `storage/app/public/users`
            $uploadedPath = Storage::disk('public')->putFile('users', $imagePath);
            $imageUrl = Storage::url($uploadedPath); // توليد رابط للصورة

            $articles[] = [
                'title_en' => $content['title_en'],
                'title_ar' => $content['title_ar'],
                'content_en' => $content['description_en'],
                'content_ar' => $content['description_ar'],
                'category_id' => $idsarray[array_rand($idsarray)],
                'author_id' => $usersidsarray[array_rand($usersidsarray)],
                'image' => $imageUrl,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // تنفيذ إدخال جماعي دفعة واحدة
        Article::insert($articles);

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
