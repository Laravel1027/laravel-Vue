<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewComment extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $project;
    public $comment;
    public $revision_id;

    /**
     * Create a new message instance.
     * @param $name
     * @param $project
     * @param $comment
     * @param $revision_id
     */
    public function __construct($name, $project, $comment, $revision_id)
    {
        $this->name = $name;
        $this->project = $project;
        $this->comment = $comment;
        $this->revision_id = $revision_id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Comment | Please Login to Review')->view('emails.projects.new_comment');
    }
}