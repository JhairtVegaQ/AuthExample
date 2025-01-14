<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class JWTMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $token = JWTAuth::parseToken();
            $payload = $token->getPayload();
            $user = JWTAuth::authenticate();

            Log::channel('jwt-auth')->info('JWT Middleware', [
                'token' => $request->bearerToken(),
                'payload' => $payload,
                'auth' => $user
            ]);

            $request->merge(['authUser' => $user->load('estudiante', 'administrativo', 'docente')]);

            Log::channel('jwt-auth')->info('JWT Middleware', [
                'request' => $request->all()
            ]);
        } catch (TokenExpiredException $e) {
            return response()->json([
                'message' => 'No está autorizado al sistema: Token expirado',
                'error_code' => 'TOKEN_EXPIRED'
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'message' => 'No está autorizado al sistema: Token inválido',
                'error_code' => 'TOKEN_INVALID'
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'No está autorizado al sistema: Token no proporcionado',
                'error_code' => 'TOKEN_NOT_PROVIDED'
            ], 401);
        }

        return $next($request);
    }
}
