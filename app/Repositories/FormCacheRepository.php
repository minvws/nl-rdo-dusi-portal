<?php
declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemManager;

readonly class FormCacheRepository
{
    private const DISK = 'forms';

    private Filesystem $storage;

    public function __construct(FilesystemManager $manager)
    {
        $this->storage = $manager->disk(self::DISK);
    }

    private function pathForKey(string $key): string
    {
        return "$key.json";
    }

    private function keyForPath(string $path): string
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    public function getKeys(): array
    {
        return array_map(fn ($path) => $this->keyForPath($path), $this->storage->allFiles());
    }

    public function exists(string $key): bool
    {
        return $this->storage->exists($this->pathForKey($key));
    }

    public function get(string $key): ?string
    {
        return $this->storage->get($this->pathForKey($key));
    }

    public function store(string $key, string $data): bool
    {
        return $this->storage->put($this->pathForKey($key), $data);
    }

    public function purge(string $key): bool
    {
        return $this->storage->delete($this->pathForKey($key));
    }
}
