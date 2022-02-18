<?php

declare(strict_types=1);

namespace webignition\Uri;

class DefaultPortIdentifier
{
    /**
     * @var array<string, int>
     */
    private static array $schemeToPortMap = [
        'http' => 80,
        'https' => 443,
        'ftp' => 21,
        'gopher' => 70,
        'nntp' => 119,
        'news' => 119,
        'telnet' => 23,
        'tn3270' => 23,
        'imap' => 143,
        'pop' => 110,
        'ldap' => 389,
    ];

    public static function isDefaultPort(?string $scheme, ?int $port): bool
    {
        $knownPort = self::$schemeToPortMap[$scheme] ?? null;

        return null === $port || $knownPort === $port;
    }
}
