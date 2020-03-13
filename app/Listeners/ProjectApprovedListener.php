<?php

namespace App\Listeners;

use App\Events\ProjectApproved;
use App\Mail\ApproveProject;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class ProjectApprovedListener
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
     * @param  ProjectApproved  $event
     * @return void
     */
    public function handle(ProjectApproved $event)
    {
        // Email notifications
        if (!$event->freelancer->emailNotifications) {
            $event->freelancer->emailNotificatios()->create();

            Mail::to($event->freelancer->email)->queue(new ApproveProject($event->freelancer->name, $event->project, $event->revision));
        } else if ($event->freelancer->emailNotifications->approved_project) {
            Mail::to($event->freelancer->email)->queue(new ApproveProject($event->freelancer->name, $event->project, $event->revision));
        }
    }
}
