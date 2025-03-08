<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Services\ImageService;
use App\Http\Traits\ApiResponse;
use App\Mail\VerifyEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    use ApiResponse;

    protected $imageservice;

    public function __construct(ImageService $imageService)
    {
        $this->imageservice = $imageService;
    }



    public function index() // admin Route
    {
        try {
            $users = User::orderBy('created_at', 'desc')
                ->select('id', 'name', 'image', 'email', 'role', 'created_at')
                ->paginate(30);

            if ($users->isEmpty()) {
                return $this->noContentResponse();
            }

            return $this->paginationResponse($users, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    public function store(StoreUserRequest $request)
    {
        try {
            $data = $request->validated();

            // تشفير كلمة المرور إن وجدت
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            // إنشاء المستخدم وملء البيانات
            $user = User::create($data);

            // معالجة الصورة إذا تم رفعها
            if ($request->hasFile('image')) {
                $this->imageservice->ImageUploaderwithvariable($request, $user, 'images/users', 'image');
            }

            return $this->successResponse($user, 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }




    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            return $this->successResponse($user, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $user = User::findOrFail($id);

            // تحديث كلمة المرور بعد التحقق من وجودها
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            // تحديث البيانات
            $user->update($data);

            // تحديث الصورة إذا تم رفع صورة جديدة
            if ($request->hasFile('image')) {
                $this->imageservice->ImageUploaderwithvariable($request, $user, 'images/users', 'image');
            }

            return $this->successResponse($user->fresh(), 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }




    public function destroy($id) // admin route
    {
        try {
            $user = User::findOrFail($id);

            // حذف الصورة إذا وُجدت
            if (!empty($user->image)) {
                $imageDeleted = $this->imageservice->deleteOldImage($user, 'images/users');
                if (!$imageDeleted) {
                    return $this->errorResponse('Failed to delete user image', 500);
                }
            }

            $user->delete();

            return $this->successResponse(['name', $user->name], 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    public function searchForUsersByName(Request $request) // admin Route
    {
        try {
            // التحقق من الإدخال
            $validatedData = $request->validate([
                'name' => 'required|string|min:2|max:255'
            ]);

            $name = strtolower($validatedData['name']);

            // البحث مع تحسين الأداء
            $users = User::select('id', 'name', 'image', 'email', 'role', 'created_at')
                ->whereRaw('LOWER(name) LIKE ?', ['%' . $name . '%'])
                ->orderBy('created_at', 'desc')
                ->paginate(30);

            // التأكد من وجود نتائج
            if ($users->total() === 0) {
                return $this->noContentResponse();
            }

            return $this->paginationResponse($users, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function getUsersIds() // admin Route
    {
        try {
            // استخدام cursor() لتحميل البيانات بشكل تدريجي
            $usersIds = User::cursor()->pluck('id')->toArray();

            // التحقق من وجود بيانات
            if (empty($usersIds)) {
                return $this->noContentResponse();
            }

            return $this->successResponse(array_values($usersIds), 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function getUsersCount()
    {
        try {
            $usersCount = User::count();
            return $this->successResponse($usersCount, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    public function checkPassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string'
        ]);

        try {
            $user = User::findOrFail($id);

            if (Hash::check($request->password, $user->password)) {
                return $this->successResponse(['Message' => 'Password is Correct'], 'Done', 200);
            } else {
                return $this->errorResponse("Incorrect Password", ['message' => 'Password does not match'], 401);
            }
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function sendVerfiyEmail(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return $this->notFoundResponse("User Not Found With This Email");
            }

            if ($user->email_verified_at) {
                return response()->json(['message' => 'تم تفعيل الحساب مسبقًا.'], 400);
            } else {
                // إنشاء رمز تحقق جديد
                $user->email_verification_token = sha1(time());
                $user->save();

                Mail::to($user->email)->send(new VerifyEmail($user));

                return response()->json(['message' => "Email Send Successfully"]);
            }
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function verfiyEmail($id, Request $request)
    {
        try {
            $user =  User::find($id);

            if (!$user) {
                return response()->json(['message' => 'الحساب غير موجود.'], 404);
            }

            if ($user->email_verified_at) {
                return response()->json(['message' => 'تم تفعيل الحساب مسبقًا.'], 400);
            }

            if ($user->email_verification_token !== $request->token) {
                return response()->json(['message' => 'رمز التحقق غير صالح.'], 400);
            }


            // تحديث حالة الحساب وتفعيل البريد
            $user->email_verified_at = now();
            $user->email_verification_token = null; // إزالة التوكن بعد الاستخدام
            $user->save();

            // إعادة التوجيه إلى الموقع بعد التفعيل
            return redirect('http://localhost:3000')->with('success', 'تم تفعيل حسابك بنجاح!');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
