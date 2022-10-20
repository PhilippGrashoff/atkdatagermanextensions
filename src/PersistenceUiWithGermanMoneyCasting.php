<?php declare(strict_types=1);

namespace atkdatagermanextensions;

use Atk4\Ui\Exception;
use Atk4\Ui\Persistence\Ui;
use NumberFormatter;

class PersistenceUiWithGermanMoneyCasting extends Ui
{

    /**
     * This method contains the logic of casting generic values into user-friendly format.
     *
     * @return string|string[]
     */
    public function _typecastSaveField(\Atk4\Data\Field $f, $value)
    {
        // serialize if we explicitly want that
        if ($f->serialize) {
            $value = $this->serializeSaveField($f, $value);
        }

        // always normalize string EOL
        if (is_string($value) && !$f->serialize) {
            $value = preg_replace('~\r?\n|\r~', "\n", $value);
        }

        // work only on copied value not real one !!!
        $value = is_object($value) ? clone $value : $value;

        switch ($f->type) {
            case 'boolean':
                $value = $value ? $this->yes : $this->no;

                break;
            case 'money':
                $numberFormatter = new NumberFormatter('de_DE', NumberFormatter::DECIMAL);
                $res = $numberFormatter->format($value);
                if ($res === false) {
                    throw new Exception($numberFormatter->getErrorMessage() . ' ' . $numberFormatter->getErrorCode());
                }
                $value = $res;

                break;
            case 'date':
            case 'datetime':
            case 'time':
                $dt_class = $f->dateTimeClass ?? \DateTime::class;
                $tz_class = $f->dateTimeZoneClass ?? \DateTimeZone::class;

                if ($value instanceof $dt_class || $value instanceof \DateTimeInterface) {
                    $formats = [
                        'date' => $this->date_format,
                        'datetime' => $this->datetime_format,
                        'time' => $this->time_format
                    ];
                    $format = $f->persist_format ?: $formats[$f->type];

                    // datetime only - set to persisting timezone
                    if ($f->type === 'datetime' && isset($f->persist_timezone)) {
                        $value = new $dt_class($value->format('Y-m-d H:i:s.u'), $value->getTimezone());
                        $value->setTimezone(new $tz_class($f->persist_timezone));
                    }
                    $value = $value->format($format);
                }

                break;
            case 'array':
            case 'object':
                // don't encode if we already use some kind of serialization
                $value = $f->serialize ? $value : json_encode($value, JSON_THROW_ON_ERROR);

                break;
        }

        return is_array($value)
            ? array_map(function ($v) {
                return (string)$v;
            }, $value)
            : (string)$value;
    }

    /**
     * Interpret user-defined input for various types.
     */
    public function _typecastLoadField(\Atk4\Data\Field $f, $value)
    {
        // serialize if we explicitly want that
        if ($f->serialize && $value) {
            try {
                $new_value = $this->serializeLoadField($f, $value);
            } catch (\Exception $e) {
                throw (new Exception('Unable to serialize field value on load'))
                    ->addMoreInfo('serializator', $f->serialize)
                    ->addMoreInfo('value', $value)
                    ->addMoreInfo('field', $f);
            }
            $value = $new_value;
        }

        // always normalize string EOL
        if (is_string($value) && !$f->serialize) {
            $value = preg_replace('~\r?\n|\r~', "\n", $value);
        }

        switch ($f->type) {
            case 'string':
            case 'text':
                break;
            case 'boolean':
                $value = (bool)$value;

                break;
            case 'money':
                $numberFormatter = new NumberFormatter('de_DE', NumberFormatter::DECIMAL);
                $res = $numberFormatter->parse((string)$value, \NumberFormatter::TYPE_DOUBLE);
                if ($res === false) {
                    throw new Exception($numberFormatter->getErrorMessage() . ' ' . $numberFormatter->getErrorCode());
                }
                $value = $res;

                break;
            case 'date':
            case 'datetime':
            case 'time':
                $dt_class = $f->dateTimeClass ?? \DateTime::class;
                $tz_class = $f->dateTimeZoneClass ?? \DateTimeZone::class;

                // ! symbol in date format is essential here to remove time part of DateTime - don't remove, this is not a bug
                $formats = [
                    'date' => '!+' . $this->date_format,
                    'datetime' => '!+' . $this->datetime_format,
                    'time' => '!+' . $this->time_format
                ];
                $format = $f->persist_format ?: $formats[$f->type];

                // datetime only - set from persisting timezone
                $valueStr = $value;
                if ($f->type === 'datetime' && isset($f->persist_timezone)) {
                    $value = $dt_class::createFromFormat($format, $value, new $tz_class($f->persist_timezone));
                    if ($value === false) {
                        throw (new Exception('Incorrectly formatted datetime'))
                            ->addMoreInfo('format', $format)
                            ->addMoreInfo('value', $valueStr)
                            ->addMoreInfo('field', $f);
                    }
                    $value->setTimeZone(new $tz_class(date_default_timezone_get()));
                } else {
                    $value = $dt_class::createFromFormat($format, $value);
                    if ($value === false) {
                        throw (new Exception('Incorrectly formatted date/time'))
                            ->addMoreInfo('format', $format)
                            ->addMoreInfo('value', $valueStr)
                            ->addMoreInfo('field', $f);
                    }
                }

                break;
        }

        if (isset($f->reference)) {
            if (empty($value)) {
                $value = null;
            }
        }

        return $value;
    }
}