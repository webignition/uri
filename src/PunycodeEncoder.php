<?php

namespace webignition\Uri;

use Algo26\IdnaConvert\IdnaConvert;

class PunycodeEncoder
{
    public static function encode(string $value): string
    {
        try {
            return (new IdnaConvert())->encode($value);
        } catch (\InvalidArgumentException $invalidArgumentException) {
        }

        return $value;
    }

    public static function decode(string $value): string
    {
        return (new IdnaConvert())->decode($value);
    }
}
