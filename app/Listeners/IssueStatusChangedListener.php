<?php

namespace App\Listeners;

use App\Events\IssueStatusChanged;
use App\Mail\ChangeIssueStatus;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class IssueStatusChangedListener
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
     * @param  IssueStatusChanged  $event
     * @return void
     */
    public function handle(IssueStatusChanged $event)
    {
        // Email notifications
        if (!$event->user->emailNotifications) {
            $event->user->emailNotificatios()->create();

            Mail::to($event->user->email)->queue(new ChangeIssueStatus($event->user->name, $event->project, $event->issue));
        } else if ($event->user->emailNotifications->issue_status) {
            Mail::to($event->user->email)->queue(new ChangeIssueStatus($event->user->name, $event->project, $event->issue));
        }
    }
}
