<?php
declare(strict_types=1);

namespace App\Models;

readonly class FormData
{
    public function __construct(public string $id, public string $json)
    {
    }
}
