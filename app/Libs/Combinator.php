<?php

namespace Korko\SecretSanta\Libs;

use Closure;
use Traversable;

class Combinator
{
    public static function all(array $elements, Closure $validator = null) : array
    {
        $combinations = [];

        foreach (self::getGenerator($elements) as $combination) {
            $combination = array_combine($elements, $combination);

            if (isset($validator) && !self::isCombinationValid($combination, $validator)) {
                continue;
            }

            $combinations[] = $combination;
        }

        return $combinations;
    }

    // With php7.1, return should be "?array" to return null in case of error
    public static function one(array $elements, Closure $validator = null) : array
    {
        $rndElements = $elements;
        shuffle($rndElements);

        foreach (self::getGenerator($rndElements) as $combination) {
            $combination = array_combine($elements, $combination);

            if (isset($validator) && !self::isCombinationValid($combination, $validator)) {
                continue;
            }

            return $combination;
        }

        return [];
    }

    protected static function getGenerator(array $elements) : Traversable
    {
        if ($elements === []) {
            return;
        }

        foreach (self::getGenerator_internal($elements) as $combination) {
            yield $combination;
        }
    }

    private static function getGenerator_internal(array $elements, $combination = []) : Traversable
    {
        if ($elements === []) {
            yield $combination;
        } else {
            foreach ($elements as $idx => $element) {
                // Take the first element

                // Add it to the combination
                $tmpCombination = $combination;
                $tmpCombination[] = $element;

                // And remove it from possibilities
                $tmpElements = $elements;
                unset($tmpElements[$idx]);

                yield from self::getGenerator_internal($tmpElements, $tmpCombination);
            }
        }
    }

    private static function isCombinationValid(array $combination, Closure $validator) : bool
    {
        foreach ($combination as $elementA => $elementB) {
            if (!$validator($elementA, $elementB)) {
                return false;
            }
        }

        return true;
    }
}
