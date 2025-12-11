<?php

namespace Plugins\ThemeCustomizer\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->session()->get('settings_authenticated') !== 1) {
            return redirect('/admin/auth');
        }

        return $next($request);
    }
}
