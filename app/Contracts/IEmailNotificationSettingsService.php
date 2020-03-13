<?php

namespace App\Contracts;

interface IEmailNotificationSettingsService
{
    /**
     * @param $user
     * @param $settings
     * @return mixed
     */
    public function store($user, $settings);

    /**
     * @param $user
     * @param $settings
     * @return mixed
     */
    public function update($user, $settings);
}