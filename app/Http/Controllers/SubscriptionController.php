<?php

namespace App\Http\Controllers;

use App\Contracts\IPlanService;
use App\Contracts\IStripeService;
use App\Helpers\ApiResponse;
use App\Http\Requests\SubscribeRequest;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * @var IStripeService
     */
    private $stripeService;
    /**
     * @var IPlanService
     */
    private $planService;

    public function __construct(IPlanService $planService, IStripeService $stripeService)
    {
        $this->stripeService = $stripeService;
        $this->planService = $planService;
    }

    /**
     * Create user subscription with new card
     * @param SubscribeRequest $request
     * @return array
     */
    public function subscribeWithNewCard(SubscribeRequest $request)
    {
        $user = $request->user();

        try {
            $plan = $this->planService->getDetails($request->plan);
            $newCard = $this->stripeService->createPaymentMethod($user, $request->stripe_token);
            $this->stripeService->updateDefaultPaymentMethod($user, $newCard);
            $this->stripeService->subscribeToPlan($user, $plan, $request->stripe_token);
            return ApiResponse::success('You have successfully subscribed to '.$plan->name.' plan', []);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error subscribing to plan, try again later');
        }
    }

    /**
     * Swap user subscription with new card
     * @param SubscribeRequest $request
     * @return array
     */
    public function swapWithNewCard(SubscribeRequest $request)
    {
        $user = $request->user();

        if ($user->subscribed('default')) {
            try {
                $plan = $this->planService->getDetails($request->plan);
                $newCard = $this->stripeService->createPaymentMethod($user, $request->stripe_token);
                $this->stripeService->updateDefaultPaymentMethod($user, $newCard);
                $this->stripeService->swapSubscription($user, $plan);
                return ApiResponse::success('You have successfully subscribed to '.$plan->name.' plan', []);
            } catch (\Exception $e) {
                report($e);
                return ApiResponse::error('001', 'Error subscribing to plan, try again later');
            }
        }

        return ApiResponse::error('001', 'Error subscribing to plan, try again later');

    }

    /**
     * Create user subscription with exiting card
     * @param Request $request
     * @return array
     */
    public function subscribeWithExitingCard(Request $request)
    {
        $user = $request->user();

        try {
            $plan = $this->planService->getDetails($request->plan);
            $this->stripeService->updateDefaultPaymentMethod($user, $request->card);
            $this->stripeService->subscribeToPlan($user, $plan, null);
            return ApiResponse::success('You have successfully subscribed to '.$plan->name.' plan', []);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error subscribing to plan, try again later');
        }
    }

    /**
     * Swap user subscription with exiting card
     * @param Request $request
     * @return array
     */
    public function swapWithExitingCard(Request $request)
    {
        $user = $request->user();

        if ($user->subscribed('default')) {
            try {
                $plan = $this->planService->getDetails($request->plan);
                $this->stripeService->updateDefaultPaymentMethod($user, $request->card);
                $this->stripeService->swapSubscription($user, $plan);
                return ApiResponse::success('You have successfully subscribed to '.$plan->name.' plan', []);
            } catch (\Exception $e) {
                report($e);
                return ApiResponse::error('001', 'Error subscribing to plan, try again later');
            }
        }

        return ApiResponse::error('001', 'Error subscribing to plan, try again later');
    }

    /**
     * Resume User Subscription
     * @param Request $request
     * @return array
     */
    public function resumeSubscription(Request $request)
    {
        $user = $request->user();

        if (isSubscribed($user)) {
            $stirpeId = $request->subscription;

            $userSubscription = $user->subscriptions->filter(function ($subscription) use ($stirpeId) {
                return $subscription->stripe_id === $stirpeId;
            });

            $subscription = $userSubscription->first();

            if ($subscription && $subscription->ends_at > \Carbon\Carbon::now()) {
                try {
                    $plan = $this->planService->getDetails($request->plan);
                    $this->stripeService->resumeSubscription($request->user());
                    return ApiResponse::success('You have successfully resumed your subscription to '.$plan->name.' plan', []);
                } catch(\Exception $e) {
                    report($e);
                    return ApiResponse::error('001', 'Error resuming subscription, try again later');
                }
            }
        }

        return ApiResponse::error('001', 'Your subscription to this plan is expired, you can not resume subscription');
    }

    /**
     * Cancel user subscription
     * @param Request $request
     * @return array
     */
    public function cancelSubscription(Request $request)
    {
        try {
            $plan = $this->planService->getDetails($request->plan);
            $this->stripeService->cancelSubscription($request->user());
            return ApiResponse::success('You have successfully unsubscribed from '.$plan->name.' plan', []);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error canceling subscription, try again later');
        }
    }

}
