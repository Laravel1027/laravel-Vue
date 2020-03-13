<?php

/**
 * Check if user has projects as freelancer
 * @param $user
 * @return bool
 */
function isFreelancer($user)
{
    $userProjects = $user->projects()->count();

    if ($userProjects == 0) {
        return true;
    } else {
        $freelancerProjects = $user->projects()->where('role', 'freelancer')->count();

        if ($freelancerProjects) {
            return true;
        }

        return false;
    }
}