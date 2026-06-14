<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if ($user && in_array($user->role, $roles)) {
            return $next($request);
        }

        // Jika token valid tapi rolenya salah (Misal: Guru mencoba akses rute Admin)
        return response()->json([
            'status' => 'error',
            'message' => 'Akses ditolak. Anda tidak memiliki otoritas untuk fitur ini.'
        ], 403);
    }
}