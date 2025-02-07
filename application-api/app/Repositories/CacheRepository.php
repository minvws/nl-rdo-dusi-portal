<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Repositories;

use Illuminate\Cache\Repository as LaravelCacheRepository;

class CacheRepository
{
    private const TAGS = ['form'];

    private LaravelCacheRepository $cacheRepository;

    public function __construct(LaravelCacheRepository $cacheRepository, private ?int $ttl)
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
