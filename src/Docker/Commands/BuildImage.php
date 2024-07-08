<?php

declare(strict_types=1);

namespace Pop\Docker\Commands;

use Pop\Docker\Command;
use Pop\Docker\Image;

/**
 * Takes an Image object and provides
 * all of the arguments required for
 * podman/docker to build it.
 */
class BuildImage implements Command
{
    public function __construct(
        public Image $image
    ) {
        //^^
    }

    public function cmd(): array
    {
        return [
            'build',
            '-t',
            $this->image->fullPath(),
            '-f',
            $this->image->buildSource(),
            $this->image->buildWorkingDirectory()
        ];
    }
}
