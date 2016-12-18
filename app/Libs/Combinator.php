<?php

namespace Korko\SecretSanta\Libs;

use Closure;
use Exception;
use Traversable;

class Combinator
{
    public static function all(array $elements, Closure $validator = null) : array
    {
        $combinations = [];

        foreach(self::getGenerator($elements) as $combination) {
            if(isset($validator) && !self::isCombinationValid($combination, $validator)) {
                continue;
            }

            $combinations[] = $combination;
        }

        return $combinations;
    }

    public static function getGenerator(array $elements) : Traversable
    {
        if($elements === array()) {
            yield;
        }

        $rndElements = $elements;
        shuffle($rndElements);

        foreach(self::getGenerator_internal($rndElements) as $combination) {
            yield array_combine($elements, $combination);
        }
    }

    private static function getGenerator_internal(array $elements, $combination = array()) : Traversable
    {
        if($elements === array()) {
            yield $combination;
        } else {
            foreach($elements as $idx => $element) {
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
        foreach($combination as $elementA => $elementB) {
            if(!$validator($elementA, $elementB)) {
                return false;
            }
        }
        return true;
    }
}
