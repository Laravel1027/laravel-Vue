<?php

namespace App\Contracts;


interface IPlanService
{
    /**
     * Get all plans
     * @return mixed
     */
    public function list();

    /**
     * Get plan details
     * @param $id
     * @return mixed
     */
    public function getDetails($id);

    /**
     * @param $data
     * @param $stripePlan
     * @return mixed
     */
    public function store($data, $stripePlan);

    /**
     * Delete plan
     * @param $id
     * @return mixed
     */
    public function delete($id);

}