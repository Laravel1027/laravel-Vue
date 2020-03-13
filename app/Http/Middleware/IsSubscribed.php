<?php

namespace App\Http\Middleware;

use App\Contracts\IBootstrapService;
use Closure;

class IsSubscribed
{
    /**
     * @var IBootstrapService
     */
    private $bootstrapService;

    public function __construct(IBootstrapService $bootstrapService)
    {
        $this->bootstrapService = $bootstrapService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (isClient($request->user()) || isCollaborator($request->user()) || isSubscribed($request->user())) {
            return $next($request);
        }

        return redirect()->to('/settings#/subscription');
    }
}
