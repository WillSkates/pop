<?php

declare(strict_types=1);

namespace Pop\Config;

use Exception;

/**
 * A small Key -> Value store using a JSON file.
 */
class Config
{
    private string $realPath;
    private array $config;

    public function __construct(
        public string $dirPath,
        public string $fileName
    ) {
        $this->realPath = sprintf('%s/%s/%s', getcwd(), $this->dirPath, $this->fileName);

        if (!file_exists(getcwd() . '/' . $this->dirPath)) {
            mkdir(getcwd() . '/' . $this->dirPath, 0700);
        }

        $this->config = [];

        if (!file_exists($this->realPath)) {
            file_put_contents($this->realPath, '{}');
        } else {
            $this->load();
        }

        foreach (['.gitignore', '.dockerignore', '.podmanignore'] as $file) {
            $path = getcwd() . '/' . $file;
            $value = $this->dirPath;

            if (file_exists($path)) {
                $contents = file_get_contents($path);

                if (preg_match('#^' . $value . '#', $contents)) {
                    continue;
                }
            }

            file_put_contents($path, $value . PHP_EOL, FILE_APPEND);
        }
    }

    public function load(): void
    {
        $this->config = json_decode(file_get_contents($this->realPath), (bool)JSON_OBJECT_AS_ARRAY);
    }

    public function save(): void
    {
        file_put_contents($this->realPath, json_encode($this->config, JSON_PRETTY_PRINT));
    }

    public function set(string $key, mixed $value): self
    {
        $this->config[$key] = $value;
        $this->save();
        return $this;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->config);
    }

    public function get(string $key): mixed
    {
        if ($this->has($key) === false) {
            throw new Exception(
                sprintf(
                    'We do not have a value for key "%s".',
                    $key
                )
            );
        }

        return $this->config[$key];
    }

    public static function withDefaults(): self
    {
        return new self(
            '.pop',
            'config.json'
        );
    }
}
