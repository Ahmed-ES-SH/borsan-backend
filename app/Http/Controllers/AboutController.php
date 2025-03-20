<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAboutContentRequest;
use App\Http\Services\ImageService;
use App\Http\Traits\ApiResponse;
use App\Models\About;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    use ApiResponse;
    protected $imageservice;

    public function __construct(ImageService $imageservice)
    {
        $this->imageservice = $imageservice;
    }





    public function index()
    {
        try {
            $companydetailes = About::findOrFail(1);
            return $this->successResponse($companydetailes, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAboutContentRequest $request)
    {
        try {
            $detailes = About::findOrFail(1);
            $data = $request->validated();
            $detailes->fill($data);

            // معالجة الصور
            $imageFields = ['first_section_image', 'second_section_image', 'thired_section_image', 'fourth_section_image'];
            foreach ($imageFields as $field) {
                if ($request->has($field)) {
                    $this->imageservice->ImageUploaderwithvariable($request, $detailes, 'images/companydetailes', $field);
                }
            }

            // معالجة الفيديو
            if ($request->has('main_video')) {
                $mainVideo = $request->input('main_video');

                if ($request->hasFile('main_video')) {
                    // إذا كان ملف، يتم رفعه
                    $videoFile = $request->file('main_video');
                    $filename = 'video_' . date('YmdHis') . '.' . $videoFile->getClientOriginalExtension();
                    $videoFile->move(public_path('videos/companydetailes'), $filename);
                    $videoUrl = url('videos/companydetailes/' . $filename);

                    // تخزين الرابط في الحقل
                    $detailes->main_video = $videoUrl;
                } else {
                    // إذا كان رابط، يتم تخزينه مباشرة
                    $detailes->main_video = $mainVideo;
                }
            }

            // حفظ البيانات
            $detailes->save();

            return $this->successResponse($detailes, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
