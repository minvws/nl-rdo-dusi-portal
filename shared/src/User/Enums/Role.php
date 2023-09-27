<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\User\Enums;

enum Role: string
{
    case UserAdmin = 'userAdmin';
    case Assessor = 'assessor';
    case ImplementationCoordinator = 'implementationCoordinator';
    case InternalAuditor = 'internalAuditor';
}
