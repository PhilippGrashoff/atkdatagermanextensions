<?php declare(strict_types=1);

namespace atkdatagermanextenions;

use Atk4\Data\Field;

class GermanHelpers {


    /**
     * makes german formatted strings from date, time and datetime fields
     */
    public static function castDateTimeToGermanString(Field $field, bool $shortenTime = false): string
    {
        //no DateTimeInterFace passed? Just return given value
        if ($field->get() instanceof \DateTimeInterface) {
            //TODO: When ATK Fields are fully refactored, refactor this to $field instanceOf Field\DateTime etc
            if ($field->type === 'datetime') {
                if ($shortenTime) {
                    return $field->get()->format('d.m.Y H:i');
                } else {
                    return $field->get()->format('d.m.Y H:i:s');
                }
            }
            if ($field->type === 'date') {
                return $field->get()->format('d.m.Y');
            }
            if ($field->type === 'time') {
                if ($shortenTime) {
                    return $field->get()->format('H:i');
                } else {
                    return $field->get()->format('H:i:s');
                }
            }
        }

        //no DateTime field? return unchanged value
        return (string)$field->get();
    }

    public static function arrayToGermanCommaList(array $a): string
    {
        $return = '';
        $counter = 0;
        foreach ($a as $item) {
            if (empty($item)) {
                continue;
            }

            $counter++;
            if ($counter === 1) {
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