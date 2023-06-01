<?php
declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Cache\Repository as CacheRepository;

readonly class FormCacheRepository
{
    private const TAGS = ['form'];

    private CacheRepository $cacheRepository;

    public function __construct(CacheRepository $cacheRepository, private ?int $ttl)
    {
        if ($cacheRepository->supportsTags()) {
            $this->cacheRepository = $cacheRepository->tags(self::TAGS);
        } else {
            $this->cacheRepository = $cacheRepository;
        }
    }

    public function exists(string $key): bool
    {
        return $this->cacheRepository->has($key);
    }

    public function get(string $key): ?string
    {
        return $this->cacheRepository->get($key);
    }

    public function store(string $key, string $data): bool
    {
        return $this->cacheRepository->put($key, $data, $this->ttl);
    }

    public function purge(string $key): bool
    {
        return $this->cacheRepository->forget($key);
    }
}
