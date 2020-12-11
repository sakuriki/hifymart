<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;

class JWTCheckToken
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
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json([
                    'code'   => 101, // means auth error in the api,
                    'response' => null // nothing to show
                ]);
            }
        } catch (TokenExpiredException $e) {
            // If the token is expired, then it will be refreshed and added to the headers
            try {
                $refreshed = JWTAuth::refresh(JWTAuth::getToken());
                $user = JWTAuth::setToken($refreshed)->toUser();
                $request->headers->set('Authorization', 'Bearer ' . $refreshed);
                // header('Authorization: Bearer ' . $refreshed);
            } catch (JWTException $e) {
                dd($e);
                return response()->json([
                    'code'   => 103, // means not refreshable
                    'response' => null // nothing to show
                ]);
            }
        } catch (JWTException $e) {
            return response()->json([
                'code'   => 101, // means auth error in the api,
                'response' => null // nothing to show
            ]);
        }

        // Login the user instance for global usage
        Auth::login($user, false);

        return  $next($request);
    }
}
