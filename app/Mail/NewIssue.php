<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewIssue extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $project;
    public $issue;

    /**
     * Create a new message instance.
     * @param $name
     * @param $project
     * @param $issue
     */
    public function __construct($name, $project, $issue)
    {
        $this->name = $name;
        $this->project = $project;
        $this->issue = $issue;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Issue | Please Login to Review')->view('emails.projects.new_issue');
    }
}