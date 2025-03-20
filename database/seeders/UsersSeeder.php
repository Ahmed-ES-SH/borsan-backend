<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('users')->truncate();

        $faker = Faker::create();
        $path = 'images/users'; // المجلد داخل public
        $fullpath = public_path($path);

        // جلب الصور الموجودة في المجلد
        $images = scandir($fullpath);
        $imagesArray = array_filter($images, function ($image) {
            return in_array(pathinfo($image, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        });

        // قائمة بأسماء الدول العشوائية
        $countries = ['USA', 'UK', 'Germany', 'France', 'Italy', 'Canada', 'Spain', 'Egypt', 'Brazil', 'India'];

        foreach (range(1, 50) as $index) {
            $randomImage = $imagesArray[array_rand($imagesArray)];
            $imagePath = "$fullpath/$randomImage";

            // رفع الصورة إلى `storage/app/public/users`
            $uploadedPath = Storage::disk('public')->putFile('users', $imagePath);
            $imageUrl = Storage::url($uploadedPath); // توليد رابط للصورة

            DB::table('users')->insert([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('asd'),
                'image' => $imageUrl,
                'country' => $faker->randomElement($countries),
                'gender' => $faker->randomElement(['male', 'female']),
                'birth_date' => $faker->date('Y-m-d'),
                'phone' => $faker->phoneNumber,
                'role' => $faker->randomElement(['admin', 'student']),
                'created_at' => $faker->dateTimeBetween('-1 years', 'now'),
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
