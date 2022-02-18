<?php

declare(strict_types=1);

namespace webignition\Uri\Tests;

use webignition\Uri\PunycodeEncoder;

class PunycodeEncoderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider encodeDataProvider
     */
    public function testEncode(string $value, string $expectedValue): void
    {
        $this->assertSame($expectedValue, PunycodeEncoder::encode($value));
    }

    /**
     * @return array<mixed>
     */
    public function encodeDataProvider(): array
    {
        return [
            'ascii' => [
                'value' => 'foo',
                'expectedValue' => 'foo',
            ],
            'unicode' => [
                'value' => '♥',
                'expectedValue' => 'xn--g6h',
            ],
            'unicode with dots' => [
                'value' => '♥.♥.♥',
                'expectedValue' => 'xn--g6h.xn--g6h.xn--g6h',
            ],
            'punycode' => [
                'value' => 'xn--g6h',
                'expectedValue' => 'xn--g6h',
            ],
            'punycode with dots' => [
                'value' => 'xn--g6h.xn--g6h.xn--g6h',
                'expectedValue' => 'xn--g6h.xn--g6h.xn--g6h',
            ],
        ];
    }

    /**
     * @dataProvider decodeDataProvider
     */
    public function testDecode(string $value, string $expectedValue): void
    {
        $this->assertSame($expectedValue, PunycodeEncoder::decode($value));
    }

    /**
     * @return array<mixed>
     */
    public function decodeDataProvider(): array
    {
        return [
            'ascii' => [
                'value' => 'foo',
                'expectedValue' => 'foo',
            ],
            'unicode' => [
                'value' => '♥',
                'expectedValue' => '♥',
            ],
            'unicode with dots' => [
                'value' => '♥.♥.♥',
                'expectedValue' => '♥.♥.♥',
            ],
            'punycode' => [
                'value' => 'xn--g6h',
                'expectedValue' => '♥',
            ],
            'punycode with dots' => [
                'value' => 'xn--g6h.xn--g6h.xn--g6h',
                'expectedValue' => '♥.♥.♥',
            ],
        ];
    }
}
