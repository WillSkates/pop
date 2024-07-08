<?php

declare(strict_types=1);

namespace Pop\Docker;

interface Command
{
    public function cmd(): array;
}
