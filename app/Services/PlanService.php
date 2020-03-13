<?php

namespace App\Services;


use App\Contracts\IPlanService;
use App\Plan;

class PlanService implements IPlanService
{
    protected $model;

    public function __construct(Plan $model)
    {
        $this->model = $model;
    }

    /**
     * Get all plans
     * @return mixed
     */
    public function list()
    {
        return $this->model->orderBy('id', 'desc')->get();
    }

    /**
     * Get plan details
     * @param $id
     * @return mixed
     */
    public function getDetails($id)
    {
        return $this->model->where('id', $id)->first();
    }

    /**
     * Create new plan
     * @param $data
     * @param $stripePlan
     * @return mixed
     */
    public function store($data, $stripePlan)
    {
        $data['stripe_plan_id'] = $stripePlan['id'];
        $data['stripe_product_id'] = $stripePlan['product'];

        return $this->model->create($data);
    }

    /**
     * Delete plan
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        return $this->model->where('id', $id)->delete();
    }
}