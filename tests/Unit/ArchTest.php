<?php

use App\Providers\ComposerServiceProvider;

arch()->preset()->php();
arch()->preset()->security()->ignoring(['assert', ComposerServiceProvider::class]); // fixed git commands for the footer version

// ponytail: strict() and laravel() presets from the starter kit are not enabled yet:
// they need strict_types everywhere and resource-style controllers (see pint.json disabled rules).
