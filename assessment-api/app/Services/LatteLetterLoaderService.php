<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Services;

use Latte\Loader;
use Latte\RuntimeException;
use Latte\Strict;

class LatteLetterLoaderService implements Loader
{
    use Strict;

    protected string $baseDir;

    /** @var string[]  [name => content] */
    protected ?array $templates;


    public function __construct(string $baseDir = null, ?array $templates = [])
    {
        $this->baseDir = self::normalizePath("$baseDir/");
        $this->templates = $templates;
    }

    public function addTemplate(string $key, string $content): void
    {
        $this->templates[$key] = $content;
    }

    /**
     * Returns template source code.
     */
    public function getContent(string $name): string
    {
        if (isset($this->templates[$name])) {
            return $this->templates[$name];
        }

        $file = $this->baseDir . $name;
        if ($this->baseDir && !str_starts_with(self::normalizePath($file), $this->baseDir)) {
            throw new RuntimeException("Template '$file' is not within the allowed path '$this->baseDir'.");
        }

        if (!is_file($file)) {
            throw new RuntimeException("Missing template file '$file'.");
        }

        if (touch($file) === false || $this->isExpired($name, time())) {
            trigger_error(
                "File's modification time is in the future. Cannot update it",
                E_USER_WARNING
            );
        }

        $contents = file_get_contents($file);

        if (!$contents) {
            throw new RuntimeException("Could not get contents of '$file'.");
        }

        return $contents;
    }

    public function isExpired(string $name, int $time): bool
    {
        $mtime = filemtime($this->baseDir . $name); // @ - stat may fail
        return !$mtime || $mtime > $time;
    }

    /**
     * Returns referred template name.
     */
    public function getReferredName(string $name, string $referringName): string
    {
        if ($this->baseDir || !preg_match('#/|\\\\|[a-z][a-z0-9+.-]*:#iA', $name)) {
            $name = self::normalizePath($referringName . '/../' . $name);
        }

        return $name;
    }

    /**
     * Returns unique identifier for caching.
     */
    public function getUniqueId(string $name): string
    {
        return $this->baseDir . str_replace('/', DIRECTORY_SEPARATOR, $name);
    }

    protected static function normalizePath(string $path): string
    {
        $res = [];
        foreach (explode('/', str_replace('\\', '/', $path)) as $part) {
            if ($part === '..' && $res && end($res) !== '..') {
                array_pop($res);
            } elseif ($part !== '.') {
                $res[] = $part;
            }
        }

        return implode(DIRECTORY_SEPARATOR, $res);
    }
}
