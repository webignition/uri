<?php

declare(strict_types=1);

namespace webignition\Uri\Tests;

use Psr\Http\Message\UriInterface;
use webignition\Uri\ScopeComparer;
use webignition\Uri\Uri;

class ScopeComparerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider isInScopeDataProvider
     *
     * @param array<string[]> $equivalentSchemeSets
     * @param array<string[]> $equivalentHostSets
     */
    public function testIsInScope(
        UriInterface $sourceUrl,
        UriInterface $comparatorUrl,
        array $equivalentSchemeSets,
        array $equivalentHostSets,
        bool $expectedIsInScope
    ): void {
        $scopeComparer = new ScopeComparer();

        if (!empty($equivalentSchemeSets)) {
            foreach ($equivalentSchemeSets as $equivalentSchemeSet) {
                $scopeComparer->addEquivalentSchemes($equivalentSchemeSet);
            }
        }

        if (!empty($equivalentHostSets)) {
            foreach ($equivalentHostSets as $equivalentHostSet) {
                $scopeComparer->addEquivalentHosts($equivalentHostSet);
            }
        }

        $this->assertEquals($expectedIsInScope, $scopeComparer->isInScope($sourceUrl, $comparatorUrl));
    }

    /**
     * @return array<mixed>
     */
    public function isInScopeDataProvider(): array
    {
        return [
            'two empty urls are in scope' => [
                'sourceUrl' => new Uri(''),
                'comparatorUrl' => new Uri(''),
                'equivalentSchemeSets' => [],
                'equivalentHostSets' => [],
                'expectedIsInScope' => true,
            ],
            'different schemes, no equivalent schemes, not in scope' => [
                'sourceUrl' => new Uri('http://example.com/'),
                'comparatorUrl' => new Uri('https://example.com/'),
                'equivalentSchemeSets' => [],
                'equivalentHostSets' => [],
                'expectedIsInScope' => false,
            ],
            'different schemes, has equivalent schemes, is in scope' => [
                'sourceUrl' => new Uri('http://example.com/'),
                'comparatorUrl' => new Uri('https://example.com/'),
                'equivalentSchemeSets' => [
                    [
                        'http',
                        'https',
                    ],
                ],
                'equivalentHostSets' => [],
                'expectedIsInScope' => true,
            ],
            'comparator as substring of source, is not in scope' => [
                'sourceUrl' => new Uri('http://example.com/foo'),
                'comparatorUrl' => new Uri('http://example.com/'),
                'equivalentSchemeSets' => [],
                'equivalentHostSets' => [],
                'expectedIsInScope' => false,
            ],
            'source as substring of comparator, is in scope' => [
                'sourceUrl' => new Uri('http://example.com/'),
                'comparatorUrl' => new Uri('http://example.com/foo'),
                'equivalentSchemeSets' => [],
                'equivalentHostSets' => [],
                'expectedIsInScope' => true,
            ],
            'different hosts, no equivalent hosts, not in scope' => [
                'sourceUrl' => new Uri('http://example.com/'),
                'comparatorUrl' => new Uri('https://example.com/'),
                'equivalentSchemeSets' => [],
                'equivalentHostSets' => [],
                'expectedIsInScope' => false,
            ],
            'different hosts, has equivalent hosts, is in scope' => [
                'sourceUrl' => new Uri('http://www.example.com/'),
                'comparatorUrl' => new Uri('http://example.com/'),
                'equivalentSchemeSets' => [],
                'equivalentHostSets' => [
                    [
                        'www.example.com',
                        'example.com',
                    ],
                ],
                'expectedIsInScope' => true,
            ],
            'equivalent schemes, equivalent hosts, identical path, is in scope' => [
                'sourceUrl' => new Uri('https://www.example.com/'),
                'comparatorUrl' => new Uri('http://example.com/'),
                'equivalentSchemeSets' => [
                    [
                        'http',
                        'https',
                    ],
                ],
                'equivalentHostSets' => [
                    [
                        'www.example.com',
                        'example.com',
                    ],
                ],
                'expectedIsInScope' => true,
            ],
            'equivalent schemes, non-equivalent hosts, identical path, not in scope' => [
                'sourceUrl' => new Uri('https://www.example.com/'),
                'comparatorUrl' => new Uri('http://example.com/'),
                'equivalentSchemeSets' => [
                    [
                        'http',
                        'https',
                    ],
                ],
                'equivalentHostSets' => [],
                'expectedIsInScope' => false,
            ],
            'equivalent schemes, equivalent hosts, source has no path, is in scope' => [
                'sourceUrl' => new Uri('https://www.example.com'),
                'comparatorUrl' => new Uri('http://example.com/foo'),
                'equivalentSchemeSets' => [
                    [
                        'http',
                        'https',
                    ],
                ],
                'equivalentHostSets' => [
                    [
                        'www.example.com',
                        'example.com',
                    ],
                ],
                'expectedIsInScope' => true,
            ],
            'equivalent schemes, equivalent hosts, source path substring of comparator path, is in scope' => [
                'sourceUrl' => new Uri('https://www.example.com/foo'),
                'comparatorUrl' => new Uri('http://example.com/foo/bar'),
                'equivalentSchemeSets' => [
                    [
                        'http',
                        'https',
                    ],
                ],
                'equivalentHostSets' => [
                    [
                        'www.example.com',
                        'example.com',
                    ],
                ],
                'expectedIsInScope' => true,
            ],
            'different ports; port difference is ignored' => [
                'sourceUrl' => new Uri('http://example.com/'),
                'comparatorUrl' => new Uri('http://example.com:8080/'),
                'equivalentSchemeSets' => [],
                'equivalentHostSets' => [],
                'expectedIsInScope' => true,
            ],
            'different users; user difference is ignored' => [
                'sourceUrl' => new Uri('http://foo:password@example.com/'),
                'comparatorUrl' => new Uri('http://bar:password@example.com/'),
                'equivalentSchemeSets' => [],
                'equivalentHostSets' => [],
                'expectedIsInScope' => true,
            ],
            'different passwords; password difference is ignored' => [
                'sourceUrl' => new Uri('http://user:foo@example.com/'),
                'comparatorUrl' => new Uri('http://user:bar@example.com/'),
                'equivalentSchemeSets' => [],
                'equivalentHostSets' => [],
                'expectedIsInScope' => true,
            ],
            'different queries; query difference is ignored' => [
                'sourceUrl' => new Uri('http://example.com/?foo=bar'),
                'comparatorUrl' => new Uri('http://example.com/?bar=foo'),
                'equivalentSchemeSets' => [],
                'equivalentHostSets' => [],
                'expectedIsInScope' => true,
            ],
            'different fragments; fragment difference is ignored' => [
                'sourceUrl' => new Uri('http://example.com/#foo'),
                'comparatorUrl' => new Uri('http://example.com/#bar'),
                'equivalentSchemeSets' => [],
                'equivalentHostSets' => [],
                'expectedIsInScope' => true,
            ],
        ];
    }
}
