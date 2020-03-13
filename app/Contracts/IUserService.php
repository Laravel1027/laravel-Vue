<?php

namespace App\Contracts;


interface IUserService
{
    /**
     * @param $id
     * @param $activationCode
     * @return mixed
     */
    public function verifyUser($id, $activationCode);
}