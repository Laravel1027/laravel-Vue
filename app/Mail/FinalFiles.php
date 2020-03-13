<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class FinalFiles extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $project;
    public $revision;
    public $final_files;

    /**
     * Create a new message instance.
     * @param $name
     * @param $project
     * @param $final_files
     * @param $revision
     */
    public function __construct($name, $project, $final_files, $revision)
    {
        $this->name = $name;
        $this->project = $project;
        $this->revision = $revision;
        $this->final_files = $final_files;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Project Final Files | Please Login to Review')->view('emails.projects.final_files');
    }
}