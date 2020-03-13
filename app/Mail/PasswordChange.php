<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PasswordChange extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $name;
    public $project;

    /**
     * Create a new message instance.
     *
     * @param $name
     * @param $project
     * @param $token
     */
    public function __construct($name, $project, $token)
    {
        $this->token = $token;
        $this->name = $name;
        $this->project = $project;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("New Project | Please Login to Review")->view('emails.customer_invited');
    }
}
