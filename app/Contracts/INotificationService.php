<?php

namespace App\Contracts;

interface INotificationService
{
    public function recent($user);
    public function create($user, array $data);
    public function personal($user, $from, array $data);
}