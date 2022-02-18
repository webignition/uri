<?php

declare(strict_types=1);

namespace webignition\Uri\Tests;

use Psr\Http\Message\UriInterface;
use webignition\Uri\Inspector;
use webignition\Uri\Uri;

class InspectorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider isNotPubliclyRoutableDataProvider
     */
    public function testIsNotPubliclyRoutable(UriInterface $url, bool $expectedIsPubliclyRoutable): void
    {
        $this->assertEquals($expectedIsPubliclyRoutable, Inspector::isNotPubliclyRoutable($url));
    }

    /**
     * @return array<mixed>
     */
    public function isNotPubliclyRoutableDataProvider(): array
    {
        return [
            'no host' => [
                'url' => new Uri('example'),
                'expectedIsPubliclyRoutable' => true,
            ],
            'host not publicly routable' => [
                'url' => new Uri('http://127.0.0.1'),
                'expectedIsPubliclyRoutable' => true,
            ],
            'host lacks dots' => [
                'url' => new Uri('http://example'),
                'expectedIsPubliclyRoutable' => true,
            ],
            'host starts with dot' => [
                'url' => new Uri('http://.example'),
                'expectedIsPubliclyRoutable' => true,
            ],
            'host ends with dot' => [
                'url' => new Uri('http://example.'),
                'expectedIsPubliclyRoutable' => true,
            ],
            'valid' => [
                'url' => new Uri('http://example.com'),
                'expectedIsPubliclyRoutable' => false,
            ],
        ];
    }

    /**
     * @dataProvider isProtocolRelativeDataProvider
     */
    public function testIsProtocolRelative(UriInterface $uri, bool $expectedIsProtocolRelative): void
    {
        $this->assertSame($expectedIsProtocolRelative, Inspector::isProtocolRelative($uri));
    }

    /**
     * @return array<mixed>
     */
    public function isProtocolRelativeDataProvider(): array
    {
        return [
            'no scheme, no host is not protocol relative' => [
                'uri' => new Uri('/path'),
                'expectedIsProtocolRelative' => false,
            ],
            'has scheme is not protocol relative' => [
                'uri' => new Uri('http://example'),
                'expectedIsProtocolRelative' => false,
            ],
            'no scheme has host is protocol relative' => [
                'uri' => new Uri('//example.com'),
                'expectedIsProtocolRelative' => true,
            ],
        ];
    }
}
