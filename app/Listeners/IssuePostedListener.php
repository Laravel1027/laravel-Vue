<?php

namespace App\Listeners;

use App\Events\IssuePosted;
use App\Mail\NewIssue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class IssuePostedListener
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
     * @param  IssuePosted  $event
     * @return void
     */
    public function handle(IssuePosted $event)
    {
        // Email notifications
        if (!$event->user->emailNotifications) {
            $event->user->emailNotificatios()->create();

            Mail::to($event->user->email)->queue(new NewIssue($event->user->name, $event->project, $event->issue));
        } else if ($event->user->emailNotifications->new_issue) {
            Mail::to($event->user->email)->queue(new NewIssue($event->user->name, $event->project, $event->issue));
        }
    }
}
