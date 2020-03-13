<?php

namespace App\Listeners;

use App\Events\NewRevisionCreated;
use App\Mail\NewRevision;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class NewRevisionCreatedListener
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
     * @param  NewRevisionCreated  $event
     * @return void
     */
    public function handle(NewRevisionCreated $event)
    {
        // Email notifications
        if (!$event->user->emailNotifications) {
            $event->user->emailNotificatios()->create();

            Mail::to($event->user->email)->queue(new NewRevision($event->user->name, $event->project, $event->revision));
        } else if ($event->user->emailNotifications->new_revision) {
            Mail::to($event->user->email)->queue(new NewRevision($event->user->name, $event->project, $event->revision));
        }
    }
}
