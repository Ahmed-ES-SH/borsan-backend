<?php

namespace App\Http\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

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




    public function ImageUploaderwithvariable(Request $request, $model, string $storagePath = 'images/unkowun', $variable = 'image')
    {
        if ($request->hasFile($variable)) {
            // -------------------------
            // تحديد الملف المراد رفعه
            // -------------------------
            $imageFile = $request->file($variable);

            // -------------------------
            // حذف الصورة القديمة إذا كانت موجودة
            // -------------------------
            if ($model->{$variable}) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $model->{$variable}));
            }

            // -------------------------
            // رفع الصورة إلى مجلد storage/app/public
            // -------------------------
            $path = $imageFile->store($storagePath, 'public');

            // -------------------------
            // تخزين رابط الصورة في قاعدة البيانات
            // -------------------------
            $model->{$variable} = Storage::url($path);
            $model->save();
        }
    }




    public function deleteOldImage($model, $storagePath = 'images/unkowun', $column = 'image')
    {
        if ($model) {
            $oldFile = $model->{$column};
            if ($oldFile) {
                // استخراج اسم الملف فقط من الرابط الكامل
                $oldFileName = basename(parse_url($oldFile, PHP_URL_PATH));

                // تحديد المسار داخل مجلد التخزين
                $filePath = $storagePath . '/' . $oldFileName;

                // حذف الصورة من التخزين
                Storage::disk('public')->delete($filePath);
            }
        }
    }



    public function uploadChatAttachment(Request $request, $model, string $storagePath = 'attachments/messages', string $variable = 'attachment'): ?string
    {
        if ($request->hasFile($variable)) {
            // تحديد الملف المراد رفعه
            $file = $request->file($variable);

            // رفع الملف إلى مجلد التخزين المحدد
            $path = $file->store($storagePath, 'public');

            // تخزين رابط الملف الجديد في قاعدة البيانات
            $model->{$variable} = Storage::url($path);
            $model->save();

            return $model->{$variable}; // إرجاع رابط المرفق الجديد
        }

        return null;
    }


    public function deleteChatAttachment($model, string $variable = 'attachment'): bool
    {
        if ($model->{$variable}) {
            // استخراج مسار الملف من الرابط المخزن
            $filePath = str_replace('/storage/', '', $model->{$variable});

            // حذف الملف من التخزين
            Storage::disk('public')->delete($filePath);

            // إزالة المرفق من قاعدة البيانات
            $model->{$variable} = null;
            $model->save();

            return true; // تم الحذف بنجاح
        }

        return false; // لا يوجد مرفق للحذف
    }
}
