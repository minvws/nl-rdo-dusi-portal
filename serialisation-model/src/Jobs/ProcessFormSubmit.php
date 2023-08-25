<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MinVWS\DUSi\Shared\Serialisation\Handlers\FormSubmitHandlerInterface;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\FormSubmit;
use Throwable;

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
     * @throws Throwable
     */
    public function handle(FormSubmitHandlerInterface $uploadHandler): void
    {
        $uploadHandler->handle($this->formSubmit);
    }
}
