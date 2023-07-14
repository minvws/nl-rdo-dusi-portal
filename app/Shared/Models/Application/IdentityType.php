<?php
declare(strict_types=1);

namespace App\Shared\Models\Application;

enum IdentityType: string
{
    case EncryptedCitizenServiceNumber = 'encryptedCitizenServiceNumber';
}
