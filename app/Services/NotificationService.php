<?php

namespace App\Services;

use App\Contracts\INotificationService;
use Ramsey\Uuid\Uuid;
use Laravel\Spark\Notification;
use Laravel\Spark\Events\NotificationCreated;

class NotificationService implements INotificationService
{
    protected $model;

    /**
     * NotificationService constructor.
     * @param Notification $model
     */
    public function __construct(Notification $model)
    {
        $this->model = $model;
    }

    /**
     * Get Notifications
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|Notification[]
     */
    public function recent($user)
    {
        // Retrieve all unread notifications for the user...
        $unreadNotifications = $this->model->with('creator')
            ->where('user_id', $user->id)
            ->where('read', 0)
            ->orderBy('created_at', 'desc')
            ->get();

        $notifications = $unreadNotifications->sortByDesc('created_at');

        if (count($notifications) > 0) {
            $this->model->whereNotIn('id', $notifications->pluck('id'))
                ->where('user_id', $user->id)
                ->where('created_at', '<', $notifications->first()->created_at)
                ->delete();
        }

        return $notifications->values();
    }

    /**
     * Create New Notification
     * @param mixed $user
     * @param array $data
     * @return Notification
     */
    public function create($user, array $data)
    {
        $creator = array_get($data, 'from');

        $notification = $this->model->create([
            'id' => Uuid::uuid4(),
            'user_id' => $user->id,
            'created_by' => $creator ? $creator->id : null,
            'type' => $data['type'],
            'icon' => $data['icon'],
            'body' => $data['body'],
            'action_text' => array_get($data, 'action_text'),
            'action_url' => array_get($data, 'action_url'),
            'company' => $data['company'],
            'project' => $data['project'],
            'proofer' => $data['proofer'],
        ]);

        event(new NotificationCreated($notification));

        return $notification;
    }

    /**
     * Create Personal Notification
     * @param mixed $user
     * @param mixed $from
     * @param array $data
     * @return Notification
     */
    public function personal($user, $from, array $data)
    {
        return $this->create($user, array_merge($data, ['from' => $from]));
    }
}