<?php

namespace App\Jobs;

use App\Contracts\IFileService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DeleteProofFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $files;

    /**
     * Create a new job instance.
     *
     * @param $files
     */
    public function __construct($files)
    {
        $this->files = $files;
    }

    /**
     * Execute the job.
     *
     * @param IFileService $fileService
     * @return void
     */
    public function handle(IFileService $fileService)
    {
        foreach ($this->files as $file) {
            $fileService->deleteFile($file);
        }
    }
}
