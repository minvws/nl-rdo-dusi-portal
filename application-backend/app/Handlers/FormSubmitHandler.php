<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Handlers;

use MinVWS\DUSi\Application\Backend\Services\ApplicationService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\FormSubmit;
use MinVWS\DUSi\Shared\Serialisation\Handlers\FormSubmitHandlerInterface;

class FormSubmitHandler implements FormSubmitHandlerInterface
{
    public function __construct(private ApplicationService $applicationService)
    {
    }

    /**
     * @throws \Throwable
     */
    public function handle(FormSubmit $formSubmit): void
    {
        $this->applicationService->processFormSubmit($formSubmit);
    }
}
