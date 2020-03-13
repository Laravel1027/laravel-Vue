<?php

namespace App\Contracts;


interface IBootstrapService
{
    /**
     * Get user datas
     * @param $user
     * @return mixed
     */
    public function bootstrap($user);

    /**
     * Get User by ID
     * @param $id
     * @return mixed
     */
    public function getUserById($id);

    /**
     * Change project listing type for user
     * @param $user
     * @param $type
     * @return mixed
     */
    public function changeProjectsListingType($user, $type);

    /**
     * Get user recent data for project create
     * @param $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecentDatas($user);

    /**
     * Disable autocomplete for project
     * @param $data
     * @return mixed
     */
    public function disableAutocompleteData($user, $data);

    /**
     * Get user active subscription and plan details
     * @param $user
     * @return array
     */
    public function getActiveSubscription($user);
}