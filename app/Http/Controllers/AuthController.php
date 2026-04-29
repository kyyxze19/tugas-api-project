<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register user baru.
     * POST /api/registerUser
     */
    public function registerUser(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil didaftarkan.',
            'data' => [
                'user' => $user,
            ],
        ], 201);
    }

    /**
     * Login user dan generate token Sanctum.
     * Token abilities otomatis diambil dari roles user di tabel user_role.
     * POST /api/loginUser
     */
    public function loginUser(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Cek kredensial
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Login gagal. Email atau password salah.',
            ], 401);
        }

        /** @var User $user */
        $user = Auth::user();

        // Ambil semua role_name dari relasi user -> roles
        $abilities = $user->roles()->pluck('role_name')->toArray();

        // Jika user tidak punya role, beri ability kosong (tidak bisa akses endpoint apapun)
        // Ini lebih aman daripada memberikan wildcard ['*']
        if (empty($abilities)) {
            $abilities = [];
        }

        // Buat token Sanctum dengan abilities sesuai roles
        $token = $user->createToken('auth_token', $abilities)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => [
                'user' => $user,
                'abilities' => $abilities,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 200);
    }
}
