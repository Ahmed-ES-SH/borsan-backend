<?php

namespace App\Http\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ImageService
{
    /**
     * رفع وتحديث صورة المستخدم
     *
     * @param  Request $request
     * @param  $user
     * @param  string $storagePath
     * @return void
     */

    public function ImageUploaderwithvariable(Request $request, $user, string $storagePath = 'images/users', $variable = 'image')
    {
        if ($request->hasFile($variable)) {
            // -------------------------
            // تحديد الملف المراد رفعه
            // -------------------------
            $imageFile = $request->file($variable);

            // -------------------------
            // تحديد اسم العمود لتخزين الرابط
            // -------------------------
            $columnName = $variable;

            // -------------------------
            // حذف الصورة القديمة
            // -------------------------
            $old_image = $user->{$columnName};
            if ($old_image) {
                $old_image_name = basename(parse_url($old_image, PHP_URL_PATH));
                $file_path = public_path($storagePath . '/' . $old_image_name);
                if (File::exists($file_path)) {
                    File::delete($file_path);
                }
            }

            // -------------------------
            // تحديث الصورة الجديدة
            // -------------------------
            // -------------------------
            // إنشاء اسم الملف الجديد
            // -------------------------
            $originalName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $imageFile->getClientOriginalExtension();
            $filename = $originalName . '_' . uniqid() . '.' . $extension;
            $imageFile->move(public_path($storagePath), $filename);

            // تحديث مسار الصورة في نموذج المستخدم
            $user->{$columnName} = url('/') . '/'  . $storagePath . '/' . $filename;
            $user->save();
        }
    }



    public function deleteOldImage($model, $storagePath)
    {
        if ($model) {
            $old_image = $model->image;
            $old_icon = $model->icon;

            if ($old_icon) {
                $oldIconName = basename(parse_url($old_icon, PHP_URL_PATH));
                $filePath = public_path($storagePath . '/' . $oldIconName);
                if (File::exists($filePath)) {
                    File::delete($filePath);
                }
            }
            if ($old_image) {
                // استخراج اسم الصورة من الرابط
                $oldImageName = basename(parse_url($old_image, PHP_URL_PATH));
                // تحديد المسار الفعلي للصورة في الخادم
                $filePath = public_path($storagePath . '/' . $oldImageName);

                // التحقق إذا كانت الصورة موجودة ثم حذفها
                if (File::exists($filePath)) {
                    File::delete($filePath);
                }
            }
        }
    }
}
