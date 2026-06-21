<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthenticated.',
            ], 401);
        }

        // Cek akun dinonaktifkan oleh Admin.
        if (!$user->is_active) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Akun Anda telah dinonaktifkan. Silakan hubungi Admin.',
            ], 403);
        }

        // Cocokkan case-insensitive, supaya tidak gagal karena
        // perbedaan kapitalisasi antara route ('ADMIN_SEKOLAH')
        // dan nilai asli di database ('admin_sekolah')
        $userRole     = strtolower($user->role);
        $allowedRoles = array_map('strtolower', $roles);

        if (in_array($userRole, $allowedRoles)) {
            return $next($request);
        }

        // Jika token valid tapi rolenya salah (Misal: Guru mencoba akses rute Admin)
        return response()->json([
            'status'  => 'error',
            'message' => 'Akses ditolak. Anda tidak memiliki otoritas untuk fitur ini.'
        ], 403);
    }
}