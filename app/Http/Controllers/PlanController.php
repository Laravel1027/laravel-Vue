<?php

namespace App\Http\Controllers;

use App\Contracts\IPlanService;
use App\Contracts\IStripeService;
use App\Helpers\ApiResponse;
use App\Http\Requests\PlanRequest;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    protected $planService;
    protected $stripeService;

    public function __construct(IPlanService $planService, IStripeService $stripeService)
    {
        $this->planService = $planService;
        $this->stripeService = $stripeService;
    }

    /**
     * Get all plans
     * @return array
     */
    public function list()
    {
        try {
            $plans = $this->planService->list();
            return ApiResponse::success('Success', $plans);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error getting plans. Try again later');
        }
    }

    /**
     * Create new plan
     * @param PlanRequest $request
     * @return array
     */
    public function store(PlanRequest $request)
    {
        $data = $request->except(['busy', 'successful', 'errors']);
        $data['price'] = $data['price'] * 100;

        $stripePlanData = [
            "amount" => $data['price'],
            "interval" => "month",
            "product" => array(
                "name" => $data['name'],
            ),
            "currency" => "usd",
        ];

        //Create Stripe plan
        try {
            $stripePlan = $this->stripeService->createPlan($stripePlanData);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error creating plan on Stripe. Try again later');
        }

        //Saving plan data in database
        try {
            $plan = $this->planService->store($data, $stripePlan);
            return ApiResponse::success('Plan created successfully', $plan);
        } catch (\Exception $e) {
            report($e);
            $this->stripeService->deletePlan($stripePlan);
            return ApiResponse::error('001', 'Error creating plan. Try again later');
        }
    }

    /**
     * Delete plan
     * @param $planId
     * @return array
     */
    public function delete($planId)
    {
        //Delete plan from Stripe
        try {
            $plan = $this->planService->getDetails($planId);
            $this->stripeService->deletePlan([
                'id' => $plan->stripe_plan_id,
                'product' => $plan->stripe_product_id
            ]);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error deleting plan from Stripe. Try again later');
        }

        //Delete plan from database
        try {
            $this->planService->delete($planId);
            return ApiResponse::success('Plan deleted successfully');
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error deleting plan. Try again later');
        }
    }
}
