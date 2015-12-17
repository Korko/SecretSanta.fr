<?php

namespace Korko\SecretSanta\Libs;

use Exception;

class Randomizer
{
    public static function randomize(array $santas, array $targets = null)
    {
        if ($targets === null) {
            $targets = array_column($santas, 'name');
        }

        for ($i = 0; $i < 1000; $i++) {
            $hat = $targets;
            $matches = [];

            foreach ($santas as $key => $santa) {
                $target = self::getRandomTargetName($santa, $hat);
                if ($target === null) {
                    continue 2; // Nobody found, reroll
                }
                $matches[$key] = $target;
                $hat = array_diff($hat, [$target]);
            }

            return $matches;
        }

        throw new Exception('Cannot resolve '.json_encode($santas).' with '.json_encode($targets));
    }

    private static function getRandomTargetName($santa, array $hat)
    {
        $santasHat = array_diff($hat, [$santa['name'], $santa['partner']]);
        if ($santasHat === []) {
            return;
        }

        return $santasHat[array_rand($santasHat)];
    }
}
