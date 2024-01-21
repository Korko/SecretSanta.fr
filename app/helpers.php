<?php

if (! function_exists('hashedRoute')) {
    function hashedRoute(string $name, mixed $parameters = [], DateTimeInterface|DateInterval|int|null $expiration = null, bool $absolute = true): string
    {
        return app('url')->hashedRoute($name, $parameters, $expiration, $absolute);
    }
}

if (! function_exists('signedRoute')) {
    function signedRoute(string $name, mixed $parameters = [], DateTimeInterface|DateInterval|int|null $expiration = null, bool $absolute = true): string
    {
        return app('url')->signedRoute($name, $parameters, $expiration, $absolute);
    }
}

if (! function_exists('base64url_encode')) {
    function base64url_encode($s)
    {
        return str_replace(['+', '/'], ['-', '_'], base64_encode($s));
    }
}

if (! function_exists('base64url_decode')) {
    function base64url_decode($s)
    {
        return base64_decode(str_replace(['-', '_'], ['+', '/'], $s));
    }
}

if (! function_exists('convert')) {
    function convert($size)
    {
        $unit = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];

        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2).' '.$unit[$i];
    }
}

if (! function_exists('translations')) {
    /**
     * Returns the translations array.
     * These locales will be sent to Vue via the Inertia's share method.
     *
     * @param $locale string - The locale whose translations you want to find
     * @return array
     */
    function translations(?string $locale = null): array
    {
        $storage = Storage::build([
            'driver' => 'local',
            'root' => app()->langPath(),
        ]);

        /*
         * ['en' => ['validation' => ['accepted' => '...']]]
        */
        return collect($storage->allFiles($locale))
            ->map(fn ($file) => [
                'path' => explode(
                    DIRECTORY_SEPARATOR,
                    pathinfo($file, \PATHINFO_DIRNAME).DIRECTORY_SEPARATOR.pathinfo($file, \PATHINFO_FILENAME)
                ),
                'translations' => require(app()->langPath($locale.DIRECTORY_SEPARATOR.$file)),
            ])
            ->reduce(fn ($tree, $file) => array_merge_recursive(
                $tree,
                collect($file['path'])
                    ->reverse()
                    ->reduce(fn ($subtree, $file) => [$file => $subtree], $file['translations'])
            ), []);
    }
}
