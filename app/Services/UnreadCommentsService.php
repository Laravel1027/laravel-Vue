<?php

namespace App\Services;


use App\Contracts\IUnreadCommentsService;
use App\UnreadComment;

class UnreadCommentsService implements IUnreadCommentsService
{
    protected $model;

    public function __construct(UnreadComment $model)
    {
        $this->model = $model;
    }

    /**
     * @param $user
     * @param array $data
     * @return mixed
     */
    public function store($user, array $data)
    {
        $data['user_id'] = $user->id;
        return $this->model->create($data);
    }

    /**
     * @param $condition
     * @return mixed
     */
    public function delete($condition)
    {
        return $this->model->where($condition)->delete();
    }
}