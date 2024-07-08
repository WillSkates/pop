<?php

declare(strict_types=1);

namespace Pop\Commands;

use InvalidArgumentException;
use Pop\Docker\PopDockerImage;
use Pop\Docker\Commands\BuildImage;
use Pop\CLICommand;

/**
 * Set which container image we should
 * use for a given piece of software.
 * 
 * e.g. For php we should use the "8.3"
 * container image.
 */
class Version implements ConsoleCommand
{
    public function run(string ...$args): int
    {
        if (count($args) !== 2) {
            throw new InvalidArgumentException(
                'Version needs 2 arguments, one for the thing and one for the version.'
            );
        }

        $thing = $args[0];
        $version = $args[1];

        $image = new PopDockerImage(
            $thing,
            $version
        );

        pop_config($thing . '.image', $image->toArray());

        return 0;
    }
}
