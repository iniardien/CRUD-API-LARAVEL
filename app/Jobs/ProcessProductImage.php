<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;



class ProcessProductImage implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    protected $filePath;

    /**
     * Create a new job instance.
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;

    }

    /**
     * Execute the job.
     */
    public function handle()
    {
         $image = Storage::get($this->filePath);
    }

}
