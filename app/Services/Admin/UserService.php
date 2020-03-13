<?php

namespace App\Services\Admin;


use App\Contracts\Admin\IUserService;
use App\User;
use Illuminate\Support\Facades\Auth;

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
     * Get users list
     * @return mixed
     */
    public function list()
    {
        $users = $this->model->with('teams')->where('id', '<>', Auth::user()->id)->get();

        foreach ($users as $user) {
            $user->activePlan = $user->currentPlan();
        }

        return $users;
    }

    /**
     * Get user by Id
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Search for users
     * @param $query
     * @return mixed
     */
    public function search($query)
    {
        return $this->model
            ->where('name', 'like', '%'.$query.'%')
            ->orWhere('email', 'like', '%'.$query.'%')->get();
    }

    /**
     * Delete user from system
     * @param $id
     * @return mixed
     */
    public function deleteUser($id)
    {
        return $this->model->where('id', $id)->delete();
    }
}