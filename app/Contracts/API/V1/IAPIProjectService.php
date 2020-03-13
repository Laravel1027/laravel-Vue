<?php

namespace App\Contracts\API\V1;

/**
 * Interface IAPIService
 * @package App\Contracts\API\V1
 */
interface IAPIProjectService
{
    /**
     * Get user projects
     * @param $user
     * @return mixed
     */
    public function getProjects($user);

    /**
     * Get project by ID
     * @param $projectId
     * @return mixed
     */
    public function getProjectById($projectId);

    /**
     * Get project by hash
     * @param $projectHash
     * @return mixed
     */
    public function getProjectByHash($projectHash);

}