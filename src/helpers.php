<?php

declare(strict_types=1);

use Pop\Config\Config;

/**
 * Some helper functions to make
 * configuration a bit nicer to use.
 */
if (!function_exists('pop_config')) {
    function pop_config(string $key, mixed $value = null): mixed
    {
        $cfg = Config::withDefaults();

        if ($value === null) {
            return $cfg->get($key);
        }

        $cfg->set($key, $value);
        return $value;
    }
}

if (!function_exists('pop_config_has')) {
    function pop_config_has(string $key): bool
    {
        return Config::withDefaults()->has($key);
    }
}
