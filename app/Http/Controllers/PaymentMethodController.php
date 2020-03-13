<?php

namespace App\Http\Controllers;

use App\Contracts\IStripeService;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    /**
     * @var IStripeService
     */
    private $stripeService;

    public function __construct(IStripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Get user payment methods
     * @param $customer
     * @return array
     */
    public function list($customer)
    {
        if ($customer != 'null') {
            try {
                $paymentMethods = $this->stripeService->getPaymentMethods($customer);
                return ApiResponse::success('Success', $paymentMethods);
            } catch(\Exception $e) {
                report($e);
                return ApiResponse::error('001', 'Error getting payment methods');
            }
        }

        return ApiResponse::success('Success', []);
    }

    /**
     * Create new payment method
     * @param Request $request
     * @return array
     */
    public function create(Request $request)
    {
        $token = $request->stripe_token;
        $user = $request->user();
        try {
            $card = $this->stripeService->createPaymentMethod($user, $token);
            return ApiResponse::success('Successfully added new payment method', $card);
        } catch(\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error creating payment method, try again later');
        }
    }

    /**
     * Change default payment method
     * @param Request $request
     * @return array
     */
    public function setAsDefault(Request $request)
    {
        $user = $request->user();
        $card = $request->id;

        try {
            $this->stripeService->updateDefaultPaymentMethod($user, $card);
            return ApiResponse::success('Default payment method changed successfully');
        } catch(\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error changing default payment method, try again later');
        }
    }

    /**
     * Remove payment method
     * @param Request $request
     * @return array
     */
    public function delete(Request $request)
    {
        $user = $request->customer;
        $card = $request->id;

        try {
            $this->stripeService->removePaymentMethod($user, $card);
            return ApiResponse::success('Payment method removed successfully');
        } catch(\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error removing payment method, try again later');
        }
    }
}
