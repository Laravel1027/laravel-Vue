<?php

namespace App\Contracts\Admin;


interface IProjectService
{
    /**
     * @param $user
     * @return mixed
     */
    public function deleteUserProjects($user);
}