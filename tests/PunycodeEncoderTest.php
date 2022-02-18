<?php

namespace webignition\Uri\Tests;

use webignition\Uri\PunycodeEncoder;

class PunycodeEncoderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider encodeDataProvider
     *
     * @param string $value
     * @param string $expectedValue
     */
    public function testEncode(string $value, string $expectedValue)
    {
        $this->assertSame($expectedValue, PunycodeEncoder::encode($value));
    }

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
     *
     * @param string $value
     * @param string $expectedValue
     */
    public function testDecode(string $value, string $expectedValue)
    {
        $this->assertSame($expectedValue, PunycodeEncoder::decode($value));
    }

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
