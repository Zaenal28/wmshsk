<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Login user & return JWT token
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau kata sandi tidak cocok.',
            ], 401);
        }

        $user = Auth::guard('api')->user();

        if (! $user->is_active) {
            Auth::guard('api')->logout();
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda dinonaktifkan. Silakan hubungi Super Admin.',
            ], 403);
        }

        // Login ke session web agar Filament Admin (/admin) langsung terautentikasi otomatis
        Auth::guard('web')->login($user, true);
        if ($request->hasSession()) {
            $request->session()->regenerate();
            $request->session()->save();
        }

        return $this->respondWithToken($token, $user);
    }

    /**
     * Get authenticated user profile
     */
    public function me(): JsonResponse
    {
        $user = Auth::guard('api')->user();
        return response()->json([
            'success' => true,
            'user' => $user,
        ]);
    }

    /**
     * Refresh JWT token
     */
    public function refresh(): JsonResponse
    {
        $newToken = Auth::guard('api')->refresh();
        $user = Auth::guard('api')->user();
        return $this->respondWithToken($newToken, $user);
    }

    /**
     * Logout user & invalidate token
     */
    public function logout(): JsonResponse
    {
        Auth::guard('api')->logout();
        return response()->json([
            'success' => true,
            'message' => 'Berhasil logout.',
        ]);
    }

    /**
     * Format response token
     */
    protected function respondWithToken(string $token, $user): JsonResponse
    {
        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'cabang' => $user->cabang,
                'pic' => $user->pic,
            ],
        ]);
    }
}
