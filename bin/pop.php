<?php

require __DIR__ . '/../vendor/autoload.php';

use Pop\Commands\Build;
use Pop\Commands\Run;
use Pop\Commands\Version;

$commands = [
    'build'   => Build::class,
    'run'     => Run::class,
    'version' => Version::class
];

function runCommand(string $key, int $strip = 2): void {
    global $argv, $commands;

    /**
     * @var int
     */
    $exitCode = call_user_func_array(
        [
            (new $commands[$key]),
            'run'
        ],
        array_slice($argv, $strip)
    );

    exit($exitCode);
}

$list = array_keys($commands);

if (in_array($argv[1], $list)) {
    runCommand($argv[1]);
}

runCommand('run', 1);
