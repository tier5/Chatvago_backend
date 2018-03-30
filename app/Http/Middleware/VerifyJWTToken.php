<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Middleware\BaseMiddleware;

class VerifyJWTToken extends BaseMiddleware
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
        if (!$token = $this->auth->setRequest($request)->getToken()) {
            return  response()->json([
                'status'    => false,
                'message'     => trans('messages.errors.jwt.token_not_found')
            ], config('response.codes.400'));
        }
        
        try {
            
            $user = $this->auth->authenticate($token);
            
        } catch (TokenExpiredException $tokenExpiredException) {
            
            return response()->json([
                'status'        => false,
                'message'         => trans('messages.errors.jwt.token_expired'),
                'error_info'    => $tokenExpiredException->getMessage()
            ], $tokenExpiredException->getStatusCode());
            
        } catch (JWTException $JWTException) {
            
            return response()->json([
                'status'        => false,
                'message'         => trans('messages.errors.jwt.token_invalid'),
                'error_info'    => $JWTException->getMessage()
            ], $JWTException->getStatusCode());
            
        }
        if (!$user) {
            
            response()->json([
                'status'    => false,
                'message'     => trans('messages.errors.users.not_found'),
            ], config('response.codes.404'));
            
        }
        
        /** @noinspection PhpUndefinedMethodInspection */
        $this->events->fire('tymon.jwt.valid', $user);
        
        return $next($request);
    }
}
