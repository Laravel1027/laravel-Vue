<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CustomerInvited
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $customer;

    public $project;

    public $token;

    /**
     * Create a new event instance.
     *
     * @param $customer
     * @param $project
     * @param $token
     */
    public function __construct($customer, $project, $token)
    {
        $this->customer = $customer;
        $this->project = $project;
        $this->token = $token;
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
