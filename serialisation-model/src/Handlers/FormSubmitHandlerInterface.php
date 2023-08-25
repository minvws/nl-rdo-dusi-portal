<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Handlers;

use MinVWS\DUSi\Shared\Serialisation\Models\Application\FormSubmit;

interface FormSubmitHandlerInterface
{
    public function handle(FormSubmit $formSubmit): void;
}
