<?php

namespace Korko\SecretSanta\Libs;

use Exception;

class Randomizer
{
    public static function randomize(array $participants) : array
    {
        $combinations = Combinator::all(array_keys($participants), function ($santa, $target) use ($participants) {
            return $santa !== $target && !in_array($target, $participants[$santa]['exclusions']);
        });

        if ($combinations === []) {
            throw new Exception('Cannot resolve '.json_encode($participants));
        }

        $rnd = array_rand($combinations);
        $combination = $combinations[$rnd];

        foreach ($combination as $idx => $idx2) {
            $combination[$idx] = $participants[$idx2]['name'];
        }

        return $combination;
    }
}
