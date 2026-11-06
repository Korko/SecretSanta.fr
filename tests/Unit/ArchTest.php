<?php

declare(strict_types=1);

use App\Providers\ComposerServiceProvider;

arch()->preset()->php();
arch()->preset()->security()->ignoring(['assert', ComposerServiceProvider::class]); // fixed git commands for the footer version

// kit presets not enabled: strict() forbids protected methods in final classes (Eloquent accessors),
// laravel() needs resource-style controllers.
