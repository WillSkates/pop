<?php

declare(strict_types=1);

namespace Pop\Commands;

use InvalidArgumentException;
use Pop\Docker\PopDockerImage;
use Pop\Docker\Commands\BuildImage;
use Pop\CLICommand;

/**
 * Runs a CLI command that builds
 * an OCI image.
 */
class Build implements ConsoleCommand
{
    public function run(string ...$args): int
    {
        if (count($args) !== 2) {
            throw new InvalidArgumentException(
                'Build needs 2 arguments, one for the thing and one for the version.'
            );
        }

        $thing = $args[0];
        $version = $args[1];

        $image = new PopDockerImage(
            $thing,
            $version
        );

        return (
            new CLICommand(
                'podman',
                (new BuildImage($image))->cmd(),
                __DIR__ . '/../../Dockerfiles'
            )
        )->go();
    }
}
