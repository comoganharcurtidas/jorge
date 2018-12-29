<?php

namespace App\Http\Middleware;

use Closure;

class CheckRedator
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
        if (\Auth::user() && \Auth::user()->redator === 'sim') {
            return $next($request);

        }
        return redirect('/home');}
}
