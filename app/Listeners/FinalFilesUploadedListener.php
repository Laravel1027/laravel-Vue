<?php

namespace App\Listeners;

use App\Events\FinalFilesUploaded;
use App\Mail\FinalFiles;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class FinalFilesUploadedListener
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
     * @param  FinalFilesUploaded  $event
     * @return void
     */
    public function handle(FinalFilesUploaded $event)
    {
        //Create email notification for client
        if (!$event->client->emailNotifications) {
            $event->client->emailNotifications()->create();

            Mail::to($event->client->email)->queue(new FinalFiles($event->client->name, $event->project, $event->links, $event->revision));
        } else if ($event->client->emailNotifications->completed_project) {
            Mail::to($event->client->email)->queue(new FinalFiles($event->client->name, $event->project, $event->links, $event->revision));
        }
    }
}
