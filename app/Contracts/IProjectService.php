<?php

namespace App\Contracts;

interface IProjectService
{
    /**
     * @param $user
     * @return mixed
     */
    public function getProjects($user);
    /**
     * @param array $data
     * @return mixed
     */
    public function createProject(array $data);

    /**
     * Save Creative Brief
     * @param array $data
     * @return mixed
     */
    public function saveCreativeBrief(array $data);

    /**
     * Get Creative Brief
     * @param $id
     * @return mixed
     */
    public function getCreativeBrief($id);

    /**
     * @param array $data
     * @return mixed
     */
    public function createSendProject(array $data);

    /**
     * @param $project_id
     * @return mixed
     */
    public function getActiveRevision($project_id);

    /**
     * @param $project_id
     * @return mixed
     */
    public function getRevisionVersions($project_id);

    /**
     * @param $project_id
     * @param $user_type
     * @return mixed
     */
    public function sendProject($project_id, $user_type);

    /**
     * @param array $data
     * @return mixed
     */
    public function approveProject(array $data);

    /**
     * @param $project_id
     * @return mixed
     */
    public function submitCorrections($project_id);

    /**
     * @param $project_id
     * @return mixed
     */
    public function deleteProject($project_id);

    /**
     * @param $data
     * @return mixed
     */
    public function sendfinalFiles($data);

    /**
     * @param $projectId
     * @return mixed
     */
    public function getDetails($projectId);

    /**
     * @param $projectId
     * @param $data
     * @return mixed
     */
    public function updateProject($projectId, $data);

    /**
     * @param $projectId
     * @param $status
     * @return mixed
     */
    public function changeStatus($projectId, $status);

    /**
     * @param $lastRevision
     * @return mixed
     */
    public function getIssues($lastRevision);

    /**
     * @param $project
     * @param $collaborators
     * @return mixed
     */
    public function registerCollaborators($project, $collaborators);

    /**
     * @param array $data
     * @return mixed
     */
    public function registerUser(array $data);

    /**
     * @param $project_id
     * @param $revison_id
     * @return mixed
     */
    public function loadInitialRevision($project_id, $revision_id);

    /**
     * @param $data
     * @return mixed
     */
    public function addTeamMember($data);

    /**
     * @param $projectId
     * @param $memberId
     * @return mixed
     */
    public function deleteTeamMember($projectId, $memberId);

    /**
     * Get project final files
     * @param $projectId
     * @return array
     */
    public function getFinalFiles($projectId);
}