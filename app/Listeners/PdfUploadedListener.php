<?php

namespace App\Listeners;

use App\Events\PdfUploaded;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PdfUploadedListener
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
     * @param  PdfUploaded  $event
     * @return void
     */
    public function handle(PdfUploaded $event)
    {
        //
    }
}
