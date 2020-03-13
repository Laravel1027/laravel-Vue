<?php

namespace App\Services;


use App\Contracts\IUserService;
use App\User;

class UserService implements IUserService
{
    protected $model;

    /**
     * UserService constructor.
     * @param User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * @param $id
     * @param $activationCode
     * @return mixed
     */
    public function verifyUser($id, $activationCode)
    {
        $user = $this->model->where('id', $id)
            ->where('activation_code', $activationCode)
            ->first();

        if ($user) {
            return $user->update([
                'verified' => 1,
                'activation_code' => null
            ]);
        }

        return null;
    }
}