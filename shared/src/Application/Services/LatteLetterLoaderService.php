<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services;

use Latte\Loaders\FileLoader;
use Latte\Strict;

class LatteLetterLoaderService extends FileLoader
{
    use Strict;

    /** @var string[]  [name => content] */
    protected ?array $templates;


    public function __construct(string $baseDir = null, ?array $templates = [])
    {
        $this->baseDir = self::normalizePath("$baseDir/");
        $this->templates = $templates;

        parent::__construct($baseDir);
    }

    public function addTemplate(string $key, string $content): void
    {
        $this->templates[$key] = $content;
    }

    /**
     * Returns template source code.
     * @psalm-suppress ParamNameMismatch
     */
    public function getContent(string $fileName): string
    {
        return $this->templates[$fileName] ?? parent::getContent($fileName);
    }
}
