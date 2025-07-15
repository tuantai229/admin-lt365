<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateLastLoginAt
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            
            // Chỉ cập nhật nếu last_login_at chưa được cập nhật trong phiên này
            if (!$request->session()->has('last_login_updated')) {
                $user->update(['last_login_at' => now()]);
                $request->session()->put('last_login_updated', true);
            }
        }
        
        return $response;
    }
}