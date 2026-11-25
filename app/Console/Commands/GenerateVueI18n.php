<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use MartinLindhe\VueInternationalizationGenerator\Commands\GenerateInclude;

/**
 * vue-i18n:generate, restricted to the keys listed in vue-i18n-generator.langKeys:
 * the package can only pick whole files, so they are filtered into a temporary
 * lang directory that the package then reads.
 */
final class GenerateVueI18n extends GenerateInclude
{
    private const string FILTERED_PATH = '/storage/framework/vue-i18n-lang';

    public function handle()
    {
        $source = base_path().config('vue-i18n-generator.langPath');
        $target = base_path().self::FILTERED_PATH;

        File::deleteDirectory($target);
        File::copyDirectory($source, $target);

        foreach ((array) config('vue-i18n-generator.langKeys', []) as $file => $keys) {
            foreach (File::glob($target.'/*/'.$file.'.php') as $path) {
                $filtered = Arr::only(require $path, $keys);
                // JSON in a single-quoted literal: var_export is a debug function (arch php preset)
                $json = addcslashes(json_encode($filtered, JSON_THROW_ON_ERROR), "'\\");
                File::put($path, "<?php return json_decode('".$json."', true);".PHP_EOL);
            }
        }

        config(['vue-i18n-generator.langPath' => self::FILTERED_PATH]);

        try {
            return parent::handle();
        } finally {
            File::deleteDirectory($target);
        }
    }
}
