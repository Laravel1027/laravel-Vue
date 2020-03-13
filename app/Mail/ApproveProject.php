<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ApproveProject extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $project;
    public $revision;

    /**
     * Create a new message instance.
     * @param $name
     * @param $project
     * @param $revision
     */
    public function __construct($name, $project, $revision)
    {
        $this->name = $name;
        $this->project = $project;
        $this->revision = $revision;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Project Approved | Please Login to Review')->view('emails.projects.approved');
    }
}