<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Middleware\BaseMiddleware;

class RefreshJWTToken extends BaseMiddleware
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
        $response = $next($request);

        try {

            $newToken = $this->auth->setRequest($request)->parseToken()->refresh();

        } catch (TokenExpiredException $tokenExpiredException) {

            return response()->json([
                'status'        => false,
                'error'         => trans('messages.jwt.token_expired'),
                'error_info'    => $tokenExpiredException->getMessage()
            ], $tokenExpiredException->getStatusCode());

        } catch (JWTException $JWTException) {
            return response()->json([
                'status'        => false,
                'error'         => trans('messages.jwt.token_invalid'),
                'error_info'    => $JWTException->getMessage()
            ], $JWTException->getStatusCode());
        }
        /*
         * Send the refreshed token back to the client
         */
        $response->headers->set('Authorization', 'Bearer '.$newToken);
        
        return $response;
    }
}
