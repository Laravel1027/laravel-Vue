<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class IssuePosted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;

    public $project;

    public $issue;

    /**
     * Create a new event instance.
     *
     * @param $user
     * @param $project
     * @param $issue
     */
    public function __construct($user, $project, $issue)
    {
        //
        $this->user = $user;
        $this->project = $project;
        $this->issue = $issue;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
