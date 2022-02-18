<?php

declare(strict_types=1);

namespace webignition\Uri;

use Algo26\IdnaConvert\Exception\AlreadyPunycodeException;
use Algo26\IdnaConvert\Exception\InvalidCharacterException;
use Algo26\IdnaConvert\Punycode\FromPunycode;
use Algo26\IdnaConvert\Punycode\ToPunycode;
use Algo26\IdnaConvert\TranscodeUnicode\TranscodeUnicode;

class PunycodeEncoder
{
    private const DOT = '.';

    public static function encode(string $value): string
    {
        $dotSeparatedParts = explode(self::DOT, $value);

        $encoded = [];
        foreach ($dotSeparatedParts as $part) {
            $encoded[] = self::encodePart($part);
        }

        return implode(self::DOT, $encoded);
    }

    public static function decode(string $value): string
    {
        $dotSeparatedParts = explode(self::DOT, $value);

        $decoded = [];
        foreach ($dotSeparatedParts as $part) {
            $decoded[] = self::decodePart($part);
        }

        return implode(self::DOT, $decoded);
    }

    private static function encodePart(string $value): string
    {
        $unicodeTranscoder = new TranscodeUnicode();

        $asUcs4Array = $unicodeTranscoder->convert(
            $value,
            TranscodeUnicode::FORMAT_UTF8,
            TranscodeUnicode::FORMAT_UCS4_ARRAY
        );

        try {
            $encoded = (new ToPunycode())->convert($asUcs4Array);
        } catch (AlreadyPunycodeException | InvalidCharacterException $e) {
            return $value;
        }

        return '' === $encoded ? $value : $encoded;
    }

    private static function decodePart(string $value): string
    {
        $decoded = (new FromPunycode())->convert($value);

        return is_string($decoded) ? $decoded : $value;
    }
}
