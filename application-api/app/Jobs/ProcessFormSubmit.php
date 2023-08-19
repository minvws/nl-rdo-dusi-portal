<?php

declare(strict_types=1);

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MinVWS\DUSi\Shared\Application\Shared\Models\Application\FormSubmit;

class ProcessFormSubmit implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public readonly FormSubmit $formSubmit)
    {
    }

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        throw new Exception("Job should not be processed by this service!");
    }
}
