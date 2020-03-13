<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TeamInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $view;
    public $invitation;

    public function __construct($view, $invitation)
    {
        $this->view = $view;
        $this->invitation = $invitation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("New Invitation!")->view($this->view);
    }
}
