<?php

// app/Http/Middleware/RoleMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // cek apakah role user ada di daftar role yg diizinkan
        if (! in_array($user->role->name, $roles)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return $next($request);
    }
}
