<?php declare(strict_types=1);

namespace traitsforatkdata;

use Atk4\Data\Exception;
use Atk4\Data\Field;
use NumberFormatter;

trait GermanMoneyFormatFieldTrait
{

    protected function germanPriceForMoneyField(Field $field)
    {
        $field->typecast = [
            function ($value, $field, $persistence) {
                $numberFormatter = new NumberFormatter('de_DE', NumberFormatter::DECIMAL);
                $res = $numberFormatter->format($value);
                if ($res === false) {
                    throw new Exception($numberFormatter->getErrorMessage() . ' ' . $numberFormatter->getErrorCode());
                }
                return $res;
            },
            function ($value, $field, $persistence) {
                $numberFormatter = new NumberFormatter('de_DE', NumberFormatter::DECIMAL);
                $res = $numberFormatter->parse((string)$value, \NumberFormatter::TYPE_DOUBLE);
                if ($res === false) {
                    throw new Exception($numberFormatter->getErrorMessage() . ' ' . $numberFormatter->getErrorCode());
                }
                return $res;
            },
        ];
    }
}