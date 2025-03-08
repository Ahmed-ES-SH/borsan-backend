<?php

namespace App\Http\Controllers;

use ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{

    use ApiResponse;

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }


    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleuser = Socialite::driver('google')->stateless()->user();
            $user = User::firstOrCreate(
                ['email' => $googleuser->getEmail()],
                [
                    'name' => $googleuser->getName(),
                    'social_id' => $googleuser->getId(),
                    'social_type' => "google",
                    'image' => $googleuser->getAvatar()
                ]
            );
            if (!$user->image) {
                $user->update(['image' => $googleuser->getAvatar()]);
            }
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'User login successful',
                'user' => $user,
                'token' => $token,
                'type' => 'user',
            ], 200);



            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Login successful',
                    'user' => $user,
                    'token' => $token,
                ], 200);
            }


            return redirect(config('app.frontend_url') . "/auth/callback?" . http_build_query([
                'token' => $token
            ]));
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Google login failed',
                    'error' => $e->getMessage(),
                ], 500);
            }

            // إعادة التوجيه مع رسالة خطأ
            return redirect(config('app.frontend_url') . "/auth/callback?error=" . urlencode($e->getMessage()));
        }
    }



    public function login(Request $request)
    {
        try {
            $validation  = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
                'password' => 'required|string'
            ]);


            if ($validation->fails()) {
                return response()->json([
                    'errors' => $validation->errors()
                ], 422);
            }

            $user = User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'message' => 'User login successful',
                    'user' => $user,
                    'token' => $token,
                    'type' => 'user',
                ], 200);
            }
            // في حالة فشل تسجيل الدخول
            return response()->json(['message' => 'Invalid email or password'], 401);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['message' => 'User not authenticated'], 401);
            }

            $user->currentAccessToken()->delete();
            return response()->json(['message' => 'Logged out successfully'], 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function getCurrentUser(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'User not authenticated'], 401);
            }

            return response()->json([
                'data' => $user,
            ], 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
