<?php

declare(strict_types=1);

namespace Pop;

interface Serializable
{
    public function toArray(): array;
    public static function fromArray(array $bits): self;
}
