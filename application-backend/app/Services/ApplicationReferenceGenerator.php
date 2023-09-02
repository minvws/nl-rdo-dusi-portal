<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

class ApplicationReferenceGenerator
{
    public static function generateRandomNumberByElevenRule(): int
    {
        do {
            $randomNumber = rand(10000000, 99999999);
        } while ($randomNumber % 11 !== 0);

        return $randomNumber;
    }
}
