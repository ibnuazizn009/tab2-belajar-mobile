<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\LoginUser;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = LoginUser::where('username', $request->username)->first();
        if (!$user) {
            return response()->json(['message' => 'Username atau password salah.'], 401);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Username atau password salah.'], 401);
        }

        try {
            $token = auth('api')->login($user);
            
        } catch (JWTException $e) {
            return response()->json(['message' => 'Gagal membuat token, coba lagi.'], 500);
        }

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => config('jwt.ttl') * 60,
            'user'         => [
                'id'           => $user->id,
                'username'     => $user->username,
                'nama_petugas' => $user->nama_petugas,
                'kelas_id'     => $user->kelas_id,
            ],
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_petugas' => 'required|string|max:255',
            'sekolah_id'   => 'required|integer',
            'kelas_id'     => 'required|integer',
            'username'     => 'required|string|min:4|unique:login_user,username', // Cek unik ke tabel login_user
            'password'     => 'required|string|min:6',
        ], [
            'username.unique' => 'Username ini sudah terdaftar!',
            'username.min'    => 'Username minimal harus 4 karakter.',
            'password.min'    => 'Password minimal harus 6 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        try {
            $user = LoginUser::create([
                'nama_petugas' => $request->nama_petugas,
                'sekolah_id'   => $request->sekolah_id,
                'kelas_id'     => $request->kelas_id,
                'username'     => strtolower($request->username),
                'password'     => Hash::make($request->password),
                'jenjang_id'   => 1 
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Registrasi petugas berhasil! Silakan masuk.',
                'data'    => [
                    'id'           => $user->id,
                    'username'     => $user->username,
                    'nama_petugas' => $user->nama_petugas,
                    'sekolah_id'   => $user->sekolah_id,
                    'kelas_id'     => $user->kelas_id,
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan sistem saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }
}
