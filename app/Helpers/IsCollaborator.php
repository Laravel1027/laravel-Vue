<?php

/**
 * Check if user has projects as collaborator
 * @param $user
 * @return bool
 */
function isCollaborator($user)
{
    $collaboratorProjects = $user->projects()->where('role', 'collaborator')->count();

    if ($collaboratorProjects) {
        return true;
    }

    return false;
}