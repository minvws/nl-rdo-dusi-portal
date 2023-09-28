<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO;

use MinVWS\Codable\Decoding\Decodable;
use MinVWS\Codable\Decoding\DecodableSupport;
use MinVWS\Codable\JSON\JSONDecoder;

readonly class CheckOrganisationsAccountResponse implements Decodable
{
    use DecodableSupport;

    public function __construct(public AccountInfo $account)
    {
    }

    public static function fromJson(string $jsonResponse): CheckOrganisationsAccountResponse
    {
        return (new JSONDecoder())->decode($jsonResponse)->decodeObject(self::class);
    }
}
