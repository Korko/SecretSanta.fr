<?php

declare(strict_types=1);

arch()->preset()->php();
arch()->preset()->security()->ignoring(['assert', App\Providers\ComposerServiceProvider::class]); // fixed git commands for the footer version

// ponytail: kit presets not enabled: strict() forbids protected methods in final classes (Eloquent accessors),
// laravel() needs resource-style controllers.
