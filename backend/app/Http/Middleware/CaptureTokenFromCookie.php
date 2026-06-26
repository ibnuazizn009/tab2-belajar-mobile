<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class CaptureTokenFromCookie
{
    /**
     * Ambil token dari cookie (web/Blade) terlebih dahulu,
     * fallback ke Bearer header (mobile/Expo) kalau cookie tidak ada.
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->cookie('token_jwt') ?? $request->bearerToken();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Token tidak ditemukan.'
            ], 401);
        }

        return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');

        try {
            $user = JWTAuth::setToken($token)->authenticate();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan.',
                ], 401);
            }
        } catch (TokenExpiredException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi telah berakhir, silakan login kembali.',
                'code'    => 'token_expired',
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid.',
                'code'    => 'token_invalid',
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token bermasalah: ' . $e->getMessage(),
            ], 401);
        }

        return $next($request);
    }
}