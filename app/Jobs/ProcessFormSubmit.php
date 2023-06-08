<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessFormSubmit implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $id, public string $data)
    {
    }

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        echo "Received ProcessFormSubmit job for form \"{$this->id}\" with data length " . strlen($this->data) . "\n";
    }
}
