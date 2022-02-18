<?php

namespace webignition\Uri\Tests;

use webignition\Uri\Filter;

class FilterTest extends \PHPUnit\Framework\TestCase
{
    public const UNRESERVED_CHARACTERS = 'a-zA-Z0-9.-_~!$&\'()*+,;=:@';

    /**
     * @dataProvider filterPathDataProvider
     */
    public function testFilterPath(string $path, string $expectedPath): void
    {
        $this->assertSame($expectedPath, Filter::filterPath($path));
    }

    /**
     * @return array<mixed>
     */
    public function filterPathDataProvider(): array
    {
        return [
            'relative path' => [
                'path' => 'path',
                'expectedPath' => 'path',
            ],
            'absolute path' => [
                'path' => '/path',
                'expectedPath' => '/path',
            ],
            'percent-encode spaces' => [
                'path' => '/pa th',
                'expectedPath' => '/pa%20th',
            ],
            'percent-encode multi-byte characters' => [
                'path' => '/€',
                'expectedPath' => '/%E2%82%AC',
            ],
            'do not double encode' => [
                'path' => '/pa%20th',
                'expectedPath' => '/pa%20th',
            ],
            'percent-encode invalid percent encodings' => [
                'path' => '/pa%2-th',
                'expectedPath' => '/pa%252-th',
            ],
            'do not encode path separators' => [
                'path' => '/pa/th//two',
                'expectedPath' => '/pa/th//two',
            ],
            'do not encode unreserved characters' => [
                'path' => '/' . self::UNRESERVED_CHARACTERS,
                'expectedPath' => '/' . self::UNRESERVED_CHARACTERS,
            ],
            'encoded unreserved characters are not decoded' => [
                'path' => '/p%61th',
                'expectedPath' => '/p%61th',
            ],
            'encode reserved characters' => [
                'path' => '/?#[]',
                'expectedPath' => '/%3F%23%5B%5D',
            ],
        ];
    }

    /**
     * @dataProvider filterQueryOrFragmentDataProvider
     */
    public function testFilterQueryOrFragment(string $queryOrFragment, string $expectedQuery): void
    {
        $this->assertSame($expectedQuery, Filter::filterQueryOrFragment($queryOrFragment));
    }

    /**
     * @return array<mixed>
     */
    public function filterQueryOrFragmentDataProvider(): array
    {
        return [
            'percent-encode spaces' => [
                'queryOrFragment' => 'f o=b r',
                'expectedQuery' => 'f%20o=b%20r',
            ],
            'do not encode plus' => [
                'queryOrFragment' => 'f+o=b+r',
                'expectedQuery' => 'f+o=b+r',
            ],
            'percent-encode multi-byte characters' => [
                'queryOrFragment' => '€=€',
                'expectedQuery' => '%E2%82%AC=%E2%82%AC',
            ],
            'do not double encode' => [
                'queryOrFragment' => 'f%20o=b%20r',
                'expectedQuery' => 'f%20o=b%20r',
            ],
            'percent-encode invalid percent encodings' => [
                'queryOrFragment' => 'f%2o=b%2r',
                'expectedQuery' => 'f%252o=b%252r',
            ],
            'do not encode path separators' => [
                'queryOrFragment' => 'q=va/lue',
                'expectedQuery' => 'q=va/lue',
            ],
            'do not encode unreserved characters' => [
                'queryOrFragment' => self::UNRESERVED_CHARACTERS,
                'expectedQuery' => self::UNRESERVED_CHARACTERS,
            ],
            'encoded unreserved characters are not decoded' => [
                'queryOrFragment' => 'f%61r=b%61r',
                'expectedQuery' => 'f%61r=b%61r',
            ],
        ];
    }

    /**
     * @dataProvider filterPortInvalidPortDataProvider
     */
    public function testFilterPortInvalidPort(int $port): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Filter::filterPort($port);
    }

    /**
     * @return array<mixed>
     */
    public function filterPortInvalidPortDataProvider(): array
    {
        return [
            'less than min' => [
                'port' => Filter::MIN_PORT - 1,
            ],
            'greater than max' => [
                'port' => Filter::MAX_PORT + 1,
            ],
        ];
    }

    /**
     * @dataProvider filterPortSuccessDataProvider
     */
    public function testFilterPortSuccess(?int $port, ?int $expectedPort): void
    {
        $this->assertSame($expectedPort, Filter::filterPort($port));
    }

    /**
     * @return array<mixed>
     */
    public function filterPortSuccessDataProvider(): array
    {
        return [
            'null' => [
                'port' => null,
                'expectedPort' => null,
            ],
            'int in range' => [
                'port' => 8080,
                'expectedPort' => 8080,
            ],
        ];
    }
}
