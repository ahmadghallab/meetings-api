<?php

namespace App\Http\Middleware;
use Closure;
use Exception;

use App\User;

use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;

class AuthMiddleware
{
    public function handle($request, Closure $next, $guard = null)
    {
        $token = $request->header('Authorization'); // get token from request header
        
        if(!$token) {
          
          // Unauthorized response if token not there
            return response()->json([
                'status' => 401,
                'error' => 'Token required.'
            ], 401);
        }
      
        try {
          
            $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
          
        } catch(ExpiredException $e) {
          
            return response()->json([
                'error' => 'Provided token is expired.'
            ], 400);
          
        } catch(Exception $e) {
            return response()->json([
                'error' => 'An error while decoding token.'
            ], 400);
        }
      
        $user = User::find($credentials->sub);
      
        // Now let's put the user in the request class so that you can grab it from there
        if(!empty($user)){
          
            $request->user_id = $user->id;
          
        }else{
          
            return response()->json([
                'error' => 'Provided token is invalid.'
            ], 400);
        }
        
        return $next($request);
    }
}