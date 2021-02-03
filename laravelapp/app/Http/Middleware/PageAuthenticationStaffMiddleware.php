<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PageAuthenticationStaffMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(auth::check()){
            $user = Auth::user();
            if($user->business_no == 3){
                return $next($request);
            }else{
                Auth::logout();
                Session::flush();
                return redirect('login')->withHeaders(['Cache-Control' => 'no-store']);
            }
        }else{
            return redirect ('/login');
        }
    }
}
