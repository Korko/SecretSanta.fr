<?php

declare(strict_types=1);

use NunoMaduro\Essentials\Configurables\ShouldBeStrict;

return [
    // Strict models catch lazy-loading/missing attributes in dev & tests, but must not 500 in prod.
    ShouldBeStrict::class => env('APP_ENV') !== 'production',
];
