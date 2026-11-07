<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command as BaseCommand;

abstract class Command extends BaseCommand
{
    protected function stringArgument(string $key): ?string
    {
        $value = $this->argument($key);

        return is_string($value) && $value !== '' ? $value : null;
    }
}
