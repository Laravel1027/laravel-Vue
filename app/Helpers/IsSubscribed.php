<?php

/**
 * Check user subscription status
 * @param $user
 * @return bool
 */
function isSubscribed($user)
{
    if ($user->subscribed('default') || $user->onGenericTrial()) {
        return true;
    }

    return false;
}