<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\LoginUser;

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
}
