<?php

namespace App\Http\Middleware;

use Closure;

class CheckRolesApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $roles)
    {

        $roles = explode('|', $roles);

        foreach(auth('api')->user()->roles as $rol)
        {
            if (in_array($rol->rol, $roles))
            {
                return $next($request);
            }
        }

        $instance = \Instantiation::instance();
        return redirect()->route('home',$instance);

    }
}
