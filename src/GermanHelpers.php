<?php declare(strict_types=1);

namespace atkdatagermanextensions;

class GermanHelpers
{

    public static function arrayToGermanCommaList(array $a): string
    {
        $return = '';
        $counter = 0;
        foreach ($a as $item) {
            $counter++;
            if (empty($item)) {
                continue;
            }

            if (strlen($return) === 0) {
                $return .= $item;
            } elseif ($counter === count($a)) {
                $return .= ' und ' . $item;
            } else {
                $return .= ', ' . $item;
            }
        }

        return $return;
    }
}