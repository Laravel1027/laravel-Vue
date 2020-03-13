<?php

namespace App\Listeners;

use App\Events\CommentPosted;
use App\Mail\NewComment;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class CommentPostedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  CommentPosted  $event
     * @return void
     */
    public function handle(CommentPosted $event)
    {
        // Email notifications
        if (!$event->user->emailNotifications) {
            $event->user->emailNotifications()->create();

            Mail::to($event->user->email)->queue(new NewComment($event->user->name, $event->project, $event->comment, $event->revisionId));
        } else if ($event->user->emailNotifications->new_comment) {
            Mail::to($event->user->email)->queue(new NewComment($event->user->name, $event->project, $event->comment, $event->revisionId));
        }
    }
}
