<?php

namespace App\Services;


use App\Contracts\IStripeService;

class StripeService implements IStripeService
{
    public function __construct()
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    /**
     * Create new plan
     * @param $data
     * @return mixed
     */
    public function createPlan($data)
    {
        return \Stripe\Plan::create($data);
    }

    /**
     * Delete plan
     * @param $plan
     * @return mixed
     */
    public function deletePlan($plan)
    {
        $stripePlan = \Stripe\Plan::retrieve($plan['id']);
        $stripePlan->delete();

        $stripeProduct = \Stripe\Product::retrieve($plan['product']);
        return $stripeProduct->delete();
    }

    /**
     * Change user subscription
     * @param $user
     * @param $plan
     * @return mixed
     */
    public function swapSubscription($user, $plan)
    {
        $user->subscription('default')->swap($plan->stripe_plan_id);
        $user->current_billing_plan = $plan->id;

        return $user->save();
    }

    /**
     * Create new subscription for user
     * @param $user
     * @param $plan
     * @param $token
     * @return mixed
     */
    public function subscribeToPlan($user, $plan, $token = null)
    {
        $user->newSubscription('default', $plan->stripe_plan_id)->create($token);
        $user->current_billing_plan = $plan->id;
        $user->trial_ends_at = null;

        return $user->save();
    }

    /**
     * Resume User Subscription
     * @param $user
     * @return mixed
     */
    public function resumeSubscription($user)
    {
        return $user->subscription('default')->resume();
    }

    /**
     * Cancel User Subscription
     * @param $user
     * @return mixed
     */
    public function cancelSubscription($user)
    {
        return $user->subscription('default')->cancel();
    }

    /**
     * Get user payment methods
     * @param $customer
     * @return mixed
     */
    public function getPaymentMethods($customer)
    {
        $customer = \Stripe\Customer::retrieve($customer);

        return [
            'paymentMethods' => $customer->sources->all(['object' => 'card']),
            'defaultMethod' => $customer['default_source'],
        ];
    }

    /**
     * Create new payment method for user
     * @param $user
     * @param $token
     * @return mixed
     */
    public function createPaymentMethod($user, $token)
    {
        if ($user->stripe_id) {
            $customer = \Stripe\Customer::retrieve($user->stripe_id);
            return $customer->sources->create(["source" => $token]);
        } else {
            $customer = $this->createCustomer($user, $token);
            return $customer->sources->retrieve($customer['default_source']);
        }
    }

    /**
     * Update user default payment method
     * @param $user
     * @param $card
     * @return mixed|\Stripe\Customer
     */
    public function updateDefaultPaymentMethod($user, $card)
    {
        $customer = \Stripe\Customer::retrieve($user->stripe_id);
        $customer->default_source = $card;

        return $customer->save();
    }


    /**
     * Remove payment method
     * @param $user
     * @param $card
     * @return mixed
     */
    public function removePaymentMethod($user, $card)
    {
        $customer = \Stripe\Customer::retrieve($user);
        return $customer->sources->retrieve($card)->delete();
    }

    /**
     * Create Stripe customer
     * @param $user
     * @param $token
     * @return \Stripe\Customer
     */
    public function createCustomer($user, $token)
    {
        $customer = \Stripe\Customer::create([
            "source" => $token,
            "email" => $user->email
        ]);

        $user->stripe_id = $customer['id'];
        $user->save();

        return $customer;
    }

    /**
     * Delete Stripe customer
     * @param $user
     * @return \Stripe\Customer
     */
    public function deleteCustomer($user)
    {
        $customer = \Stripe\Customer::retrieve($user->stripe_id);

        return $customer->delete();
    }
}