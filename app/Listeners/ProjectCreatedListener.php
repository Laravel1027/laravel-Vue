<?php

namespace App\Listeners;

use App\Events\ProjectCreated;
use App\Mail\NewProject;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class ProjectCreatedListener
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
     * @param  ProjectCreated  $event
     * @return void
     */
    public function handle(ProjectCreated $event)
    {
        if (!$event->user->emailNotifications) {
            $event->user->emailNotifications()->create();

            Mail::to($event->user)->queue(new NewProject($event->user->name, $event->project, $event->revision));
        } else if ($event->user->emailNotifications->new_project) {
            Mail::to($event->user)->queue(new NewProject($event->user->name, $event->project, $event->revision));
        }
    }
}
