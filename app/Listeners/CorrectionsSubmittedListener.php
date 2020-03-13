<?php

namespace App\Listeners;

use App\Events\CorrectionsSubmitted;
use App\Mail\SubmitCorrections;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class CorrectionsSubmittedListener
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
     * @param  CorrectionsSubmitted  $event
     * @return void
     */
    public function handle(CorrectionsSubmitted $event)
    {
        // Email notifications
        if (!$event->user->emailNotifications) {
            $event->user->emailNotificatios()->create();

            Mail::to($event->user->email)->queue(new SubmitCorrections($event->user->name, $event->project, $event->revision));
        } else if ($event->user->emailNotifications->new_correction) {
            Mail::to($event->user->email)->queue(new SubmitCorrections($event->user->name, $event->project, $event->revision));
        }
    }
}
