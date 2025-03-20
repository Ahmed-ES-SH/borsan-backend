<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UsersSeeder::class,
            ArticleCategorySeeder::class,
            FooterListSeeder::class,
            PrivacyPolicySeeder::class,
            TermsConditionSeeder::class,
            SocialContactInfoSeeder::class,
            QuestionAnswerSeeder::class,
            ContactMessagesSeeder::class
        ]);
    }
}
