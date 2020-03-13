<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ChangeIssueStatus extends Mailable
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
        
        if ($this->issue->status == 'todo') {
            return $this->subject('New Comment | Please Login to Review')->view('emails.projects.approved_issue');    
        } else if ($this->issue->status == 'review') {
            return $this->subject('Issue is in review | Please Login to Review')->view('emails.projects.approved_issue');    
        } else {
            return $this->subject('Issue is approved | Please Login to Review')->view('emails.projects.approved_issue');
        }
    }
}