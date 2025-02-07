<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Hsm;

interface HsmDecryptableData
{
    public function getKeyLabel(): string;
    public function getData(): string;
}
