<?php

declare(strict_types=1);

namespace Pop\Docker;

use Pop\Docker\Image;

/**
 * References an OCI image
 * that comes _with_ pop.
 */
class PopDockerImage extends Image
{
    public function __construct(
        public string $thing,
        public string $version
    ) {
        parent::__construct(
            'localhost/' . $this->thing,
            $this->thing,
            $this->version,
            sprintf(
                '%s/%s%s.Dockerfile',
                __DIR__ . '/../../Dockerfiles',
                $this->thing,
                $this->version
            ),
            __DIR__ . '/../../Dockerfiles'
        );
    }
}
