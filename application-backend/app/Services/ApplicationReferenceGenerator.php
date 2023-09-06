<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

class ApplicationReferenceGenerator
{
    public function generateRandomNumberByElevenRule(): int
    {

        do {
            $randomNumber = rand(11, 99999999);
        } while ($randomNumber % 11 !== 0);

        return $randomNumber;
    }
}
