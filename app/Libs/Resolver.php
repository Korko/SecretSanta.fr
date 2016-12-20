<?php

namespace Korko\SecretSanta\Libs;

use Exception;

class Resolver
{
    public static function resolve(array $participants) : array
    {
        $combination = Combinator::one(array_keys($participants), function ($santa, $target) use ($participants) {
            return $santa !== $target && !in_array($target, $participants[$santa]['exclusions']);
        });

        if ($combination === []) {
            throw new Exception('Cannot resolve '.json_encode($participants));
        }

        foreach ($combination as $idx => $idx2) {
            $combination[$idx] = $participants[$idx2]['name'];
        }

        return $combination;
    }
}
