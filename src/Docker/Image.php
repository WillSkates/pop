<?php

declare(strict_types=1);

namespace Pop\Docker;

use Exception;
use InvalidArgumentException;
use Pop\Serializable;
use SplFileInfo;

/**
 * Encapsulates details about an OCI image
 * e.g. What it's called and the arguments
 * required to build it.
 *
 * Also provides toArray and fromArray
 * methods to help with serializing and
 * unserializing that data.
 */
class Image implements Serializable
{
    public const string REPOSITORY_PATH_KEY = 'repository_path';
    public const string NAME_KEY = 'name';
    public const string TAG_KEY = 'tag';
    public const string BUILD_SOURCE_KEY = 'build_source';
    public const string BUILD_WORK_DIR_KEY = 'build_work_dir';

    public function __construct(
        public string $repositoryPath,
        public string $name,
        public string $tag = 'latest',
        protected string $buildSource = '',
        protected string $buildWorkingDirectory = ''
    ) {
        $cwd = getcwd();

        if ($cwd === false) {
            throw new Exception(
                'Can\'t run \'Run\', we can\'t determine what the current working directory is.'
            );
        }

        if (trim($this->buildSource) === '') {
            $this->buildSource = $cwd . '/Dockerfile';
        }

        if (trim($this->buildWorkingDirectory) === '') {
            $this->buildWorkingDirectory = $cwd;
        }

        $errors = [];

        foreach (
            [
                self::BUILD_SOURCE_KEY => $this->buildSource,
                self::BUILD_WORK_DIR_KEY => $this->buildWorkingDirectory
            ] as $key => $value
        ) {
            if (!file_exists($value)) {
                $errors[] = sprintf(
                    '[%s](%s) does not exist.',
                    $key,
                    $value
                );
                continue;
            }

            if (!is_readable($value)) {
                $errors[] = sprintf(
                    '[%s](%s) is not readable.',
                    $key,
                    $value
                );
            }
        }

        if (!is_file($this->buildSource)) {
            $errors[] = sprintf(
                '[%s](%s) is not a file.',
                self::BUILD_SOURCE_KEY,
                $this->buildSource
            );
        }

        foreach (
            [
                'is_dir' => 'is not a directory',
                'is_writable' => 'is not writable'
            ] as $check => $failMessage
        ) {
            if (!call_user_func('\\' . $check, $this->buildWorkingDirectory)) {
                $errors[] = sprintf(
                    '[%s](%s) %s.',
                    self::BUILD_WORK_DIR_KEY,
                    $this->buildWorkingDirectory,
                    $failMessage
                );
            }
        }

        if (count($errors) !== 0) {
            throw new InvalidArgumentException(
                implode(', ', $errors)
            );
        }
    }

    public function fullPath(): string
    {
        return sprintf(
            '%s/%s:%s',
            $this->repositoryPath,
            $this->name,
            $this->tag
        );
    }

    public function buildSource(): string
    {
        return $this->buildSource;
    }

    public function buildWorkingDirectory(): string
    {
        return $this->buildWorkingDirectory;
    }

    public function toArray(): array
    {
        $buildSource = realpath($this->buildSource);
        $buildWorkingDirectory = realpath($this->buildWorkingDirectory);

        return [
            self::REPOSITORY_PATH_KEY => $this->repositoryPath,
            self::NAME_KEY => $this->name,
            self::TAG_KEY => $this->tag,
            self::BUILD_SOURCE_KEY => $buildSource,
            self::BUILD_WORK_DIR_KEY => $buildWorkingDirectory
        ];
    }

    public static function fromArray(array $bits): self
    {
        $missing = [];

        foreach (
            [
                self::REPOSITORY_PATH_KEY,
                self::NAME_KEY,
                self::TAG_KEY
            ] as $key
        ) {
            if (!array_key_exists($key, $bits)) {
                $missing[] = $key;
            }
        }

        if (count($missing)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Cannot import %s from array. These keys are missing: %s.',
                    __CLASS__,
                    implode(', ', $missing)
                )
            );
        }

        $wrongType = [];

        foreach ($bits as $key => $value) {
            if (!is_string($value)) {
                $wrongType[] = $key . ':' . gettype($value);
            }
        }

        if (count($wrongType)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Cannot import %s from array. All values must be strings but: %s.',
                    __CLASS__,
                    implode(', ', $wrongType)
                )
            );
        }

        foreach (
            [
                self::BUILD_SOURCE_KEY,
                self::BUILD_WORK_DIR_KEY
            ] as $key
        ) {
            if (!array_key_exists($key, $bits)) {
                $bits[$key] = null;
            }
        }

        /**
         * @psalm-suppress MixedArgument
         */
        return new self(
            $bits[self::REPOSITORY_PATH_KEY],
            $bits[self::NAME_KEY],
            $bits[self::TAG_KEY],
            $bits[self::BUILD_SOURCE_KEY],
            $bits[self::BUILD_WORK_DIR_KEY]
        );
    }
}
