<?php

declare(strict_types=1);

namespace Pop\Docker\Commands;

use Pop\Docker\Command;
use Pop\Docker\Image;

/**
 * Takes an Image object and some options
 * and provides all of the arguments required
 * for odman/docker to run a container for
 * that image as we've configured it.
 */
class Run implements Command
{
    public function __construct(
        public Image $image,
        public array $env,
        public array $cmd,
        public string $containerName,
        public array $volumes,
        public bool $pseudoTTY,
        public bool $interactive,
        public bool $removeAfter,
        public array $options = []
    ) {
        // ^^
    }

    public function cmd(): array
    {
        $args = ['run', '--name=' . $this->containerName];

        if ($this->pseudoTTY) {
            $args[] = '-t';
        }

        if ($this->interactive) {
            $args[] = '-i';
        }

        if ($this->removeAfter) {
            $args[] = '--rm';
        }

        foreach ($this->options as $key => $value) {
            $args[] = $key;
            $args[] = $value;
        }

        foreach ($this->env as $key => $value) {
            $args[] = '--env';
            $args[] = sprintf(
                '%s=%s',
                $key,
                $value
            );
        }

        foreach ($this->volumes as $source => $target) {
            $args[] = '-v';
            $args[] = sprintf(
                '%s:%s',
                $source,
                $target
            );
        }

        $args[] = $this->image->fullPath();

        foreach ($this->cmd as $arg) {
            $args[] = $arg;
        }

        return $args;
    }
}
