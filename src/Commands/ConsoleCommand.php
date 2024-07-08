<?php

declare(strict_types=1);

namespace Pop\Commands;

interface ConsoleCommand
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function run(string ...$args): int;
}
