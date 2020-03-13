<?php

namespace App\Contracts;


interface IUnreadCommentsService
{
    /**
     * @param $data
     * @return mixed
     */
    public function store($user, array $data);

    /**
     * @param $condition
     * @return mixed
     */
    public function delete($condition);
}