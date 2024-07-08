<?php

declare(strict_types=1);

namespace Pop\Commands;

use InvalidArgumentException;
use Exception;
use Pop\Docker\Image;
use Pop\CLICommand;

/**
 * Looks for the container image
 * associated with a piece of software
 * e.g. php.
 *
 * It then runs the arguments passed to
 * it within the container that 
 * we have for that software.
 */
class Run implements ConsoleCommand
{
    public function run(string ...$args): int
    {
        if (count($args) < 2) {
            throw new InvalidArgumentException(
                'Run needs atleast 2 arguments, one for the thing you want to run and then the command.'
            );
        }

        $cwd = getcwd();

        if ($cwd === false) {
            throw new Exception(
                'Can\'t run \'Run\', we can\'t determine what the current working directory is.'
            );
        }

        $thing = $args[0];

        if (pop_config_has($thing . '.image') === false) {
            //Eventually this should have defaults.
            throw new Exception(
                sprintf(
                    'Cannot find config for "%s" for this build. Exiting.',
                    $thing
                )
            );
        }

        $details = pop_config($thing . '.image');

        $image = Image::fromArray($details);

        $runCmd = new \Pop\Docker\Commands\Run(
            $image,
            [
                'CI' => 'true'
            ],
            $args,
            sha1($cwd) . '-' . $image->name,
            [
                $cwd => '/work'
            ],
            true,
            false,
            true
        );

        return (new CLICommand('podman',$runCmd->cmd(), getcwd()))->go();
    }
}
