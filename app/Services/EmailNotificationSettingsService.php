<?php

namespace App\Services;

use App\Contracts\IEmailNotificationSettingsService;
use App\EmailNotificationSettings;

class EmailNotificationSettingsService implements IEmailNotificationSettingsService
{
    protected $model;

    /**
     * EmailNotificationSettingsService constructor.
     * @param EmailNotificationSettings $model
     */
    public function __construct(EmailNotificationSettings $model)
    {
        $this->model = $model;
    }

    /**
     * Create Email Notifications Settings
     * @param $user
     * @param $settings
     * @return mixed
     */
    public function store($user, $settings)
    {
        return $user->emailNotifications()->create($settings);
    }

    /**
     * Update Email Notifications Settings
     * @param $user
     * @param $settings
     * @return mixed
     */
    public function update($user, $settings)
    {
        if (!$user->emailNotifications) {
            return $this->store($user, $settings);
        }
        return $user->emailNotifications()->update($settings);
    }
}