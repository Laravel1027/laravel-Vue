<?php

namespace App\Contracts\API\V1;


interface IAPIAuthService
{
    /**
     * Get user by ID
     * @param $id
     * @return mixed
     */
    public function getUserById($id);

    /**
     * Create new token for user
     * @param $user
     * @return mixed
     */
    public function createToken($user);

    /**
     * Get user by token
     * @param $token
     * @return mixed|null
     */
    public function getUserByToken($token);
}