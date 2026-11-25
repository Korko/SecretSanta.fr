<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
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
        $source = base_path().Config::string('vue-i18n-generator.langPath');
        $target = base_path().self::FILTERED_PATH;

        File::deleteDirectory($target);
        File::copyDirectory($source, $target);

        /** @var array<string, list<string>> $langKeys */
        $langKeys = Config::array('vue-i18n-generator.langKeys', []);
        foreach ($langKeys as $file => $keys) {
            /** @var list<string> $paths */
            $paths = File::glob($target.'/*/'.$file.'.php');
            foreach ($paths as $path) {
                /** @var array<string, mixed> $translations */
                $translations = require $path;
                $filtered = Arr::only($translations, $keys);
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
