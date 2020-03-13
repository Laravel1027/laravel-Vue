<?php

namespace App\Contracts\Admin;


interface IUserService
{
    /**
     * @return mixed
     */
    public function list();

    /**
     * @param $id
     * @return mixed
     */
    public function getById($id);

    /**
     * @param $query
     * @return mixed
     */
    public function search($query);

    /**
     * @param $id
     * @return mixed
     */
    public function deleteUser($id);
}