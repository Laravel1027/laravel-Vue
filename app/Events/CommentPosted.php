<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CommentPosted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;

    public $project;

    public $comment;

    public $revisionId;

    /**
     * Create a new event instance.
     *
     * @param $user
     * @param $project
     * @param $comment
     * @param $revisionId
     */
    public function __construct($user, $project, $comment, $revisionId)
    {
        $this->user = $user;
        $this->project = $project;
        $this->comment = $comment;
        $this->revisionId = $revisionId;
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
