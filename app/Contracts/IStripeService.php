<?php

namespace App\Contracts;


interface IStripeService
{
    /**
     * Create new plan
     * @param $data
     * @return mixed
     */
    public function createPlan($data);

    /**
     * Delete plan
     * @param $plan
     * @return mixed
     */
    public function deletePlan($plan);

    /**
     * Change user subscription
     * @param $user
     * @param $plan
     * @return mixed
     */
    public function swapSubscription($user, $plan);

    /**
     * Create new subscription for user
     * @param $user
     * @param $plan
     * @param $token
     * @return mixed
     */
    public function subscribeToPlan($user, $plan, $token);

    /**
     * Resume User Subscription
     * @param $user
     * @return mixed
     */
    public function resumeSubscription($user);

    /**
     * Cancel User Subscription
     * @param $user
     * @return mixed
     */
    public function cancelSubscription($user);

    /**
     * Get user payment methods
     * @param $customer
     * @return mixed
     */
    public function getPaymentMethods($customer);

    /**
     * Create new payment method for user
     * @param $user
     * @param $token
     * @return mixed
     */
    public function createPaymentMethod($user, $token);

    /**
     * Update user default payment method
     * @param $user
     * @param $card
     * @return mixed
     */
    public function updateDefaultPaymentMethod($user, $card);

    /**
     * Remove payment method
     * @param $user
     * @param $card
     * @return mixed
     */
    public function removePaymentMethod($user, $card);

    /**
     * Create Stripe customer
     * @param $user
     * @param $token
     * @return \Stripe\Customer
     */
    public function createCustomer($user, $token);

    /**
     * Delete Stripe customer
     * @param $user
     * @return mixed
     */
    public function deleteCustomer($user);
}