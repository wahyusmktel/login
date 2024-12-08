<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorMiddleware
{
    public function handle(Request $request, Closure $next)
{
    // Jika 2FA belum diverifikasi, arahkan ke halaman verifikasi kecuali sudah di sana
    if (!session('admin_2fa_verified') && Auth::guard('admin')->check() && Auth::guard('admin')->user()->two_factor_enabled) {
        if (!$request->is('admin/verify-2fa')) {
            return redirect()->route('admin.verify-2fa');
        }
    }

    return $next($request);
}

}
