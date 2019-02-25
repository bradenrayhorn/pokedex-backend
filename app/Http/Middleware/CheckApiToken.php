<?php

namespace App\Http\Middleware;

use Closure;
use \App\User;

class CheckApiToken
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
        // make sure api token is not empty
        if(!empty(trim($request->input('api_token')))) {
            // make sure auth was able to login user
            $is_exists = User::where('id' , \Auth::guard('api')->id())->exists();
            if($is_exists){
                return $next($request);
            }
        }
        // token did not work
        return response()->json([
            "message" => "Invalid or nonexistent token."
        ], 401);
    }
}
