<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FinalFilesUploaded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $client;

    public $project;

    public $links;

    public $revision;

    /**
     * Create a new event instance.
     *
     * @param $client
     * @param $project
     * @param $links
     * @param $revision
     */
    public function __construct($client, $project, $links, $revision)
    {
        $this->client = $client;
        $this->project = $project;
        $this->links = $links;
        $this->revision = $revision;
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
