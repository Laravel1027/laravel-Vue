<?php

namespace App\Http\Middleware\API\V1;

use App\APIToken;
use App\Http\Resources\API\V1\ErrorResource;
use Closure;

/**
 * Class Authorized
 * Middlware for API requests
 * @package App\Http\Middleware\API\V1
 */
class Authorized
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return ErrorResource|mixed
     */
    public function handle($request, Closure $next)
    {
        $token = APIToken::where('token', $request->header('Authorization'))->first();

        if ($token && $token->user) {
            return $next($request);
        }

        $response = [
            'status' => 'Unauthorized action',
            'code' => 401,
            'message' => 'Invalid token'
        ];

        return new ErrorResource((object) $response);
    }
}
