<?php

namespace App\Services\API\V1;


use App\APIToken;
use App\Contracts\API\V1\IAPIProjectService;
use App\Project;

class APIProjectProjectService implements IAPIProjectService
{
    protected $apiModel;
    protected $projectModel;

    /**
     * APIProjectProjectService constructor.
     * @param APIToken $apiModel
     * @param Project $projectModel
     */
    public function __construct(APIToken $apiModel, Project $projectModel)
    {
        $this->apiModel = $apiModel;
        $this->projectModel = $projectModel;
    }

    /**
     * Get user projects
     * @param $user
     * @return mixed
     */
    public function getProjects($user)
    {
        return $user->projects()->has('revisions')
            ->where('status', '!=', 'approved')
            ->where('status', '!=', 'completed')
            ->get();
    }

    /**
     * Get project by ID
     * @param $projectId
     * @return mixed
     */
    public function getProjectById($projectId)
    {
        return $this->projectModel->findOrFail($projectId);
    }

    /**
     * Get Project by hash
     * @param $projectHash
     * @return mixed
     */
    public function getProjectByHash($projectHash)
    {
        return $this->projectModel->findOrFail($projectHash);
    }
}