<?php

namespace App\Listeners;

use App\Events\PdfPageConverted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PdfPageConvertedListener
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
     * @param  PdfPageConverted  $event
     * @return void
     */
    public function handle(PdfPageConverted $event)
    {

    }
}
