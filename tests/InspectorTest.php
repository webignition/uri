<?php

namespace webignition\Uri\Tests;

use IpUtils\Exception\InvalidExpressionException;
use Psr\Http\Message\UriInterface;
use webignition\Uri\Inspector;
use webignition\Uri\Uri;

class InspectorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider isNotPubliclyRoutableDataProvider
     *
     * @param UriInterface $url
     * @param bool $expectedIsPubliclyRoutable
     *
     * @throws InvalidExpressionException
     */
    public function testIsNotPubliclyRoutable(UriInterface $url, bool $expectedIsPubliclyRoutable)
    {
        $this->assertEquals($expectedIsPubliclyRoutable, Inspector::isNotPubliclyRoutable($url));
    }

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
     *
     * @param UriInterface $uri
     * @param bool $expectedIsProtocolRelative
     */
    public function testIsProtocolRelative(UriInterface $uri, bool $expectedIsProtocolRelative)
    {
        $this->assertSame($expectedIsProtocolRelative, Inspector::isProtocolRelative($uri));
    }

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
