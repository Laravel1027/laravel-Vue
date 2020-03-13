<?php

namespace App\Http\Middleware;

use Closure;
use Stripe\WebhookSignature;
use Stripe\Error\SignatureVerification;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Config\Repository as Config;

class VerifyStripeSignature
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Config
     */
    protected $config;

    public function __construct(Application $app, Config $config)
    {

        $this->app = $app;
        $this->config = $config;
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
        try {
            WebhookSignature::verifyHeader(
                $request->getContent(),
                $request->header('Stripe-Signature'),
                $this->config->get('services.stripe.webhook.secret'),
                $this->config->get('services.stripe.webhook.tolerance')
            );
        } catch (SignatureVerification $exception) {
            $this->app->abort(403);
        }

        return $next($request);
    }
}
