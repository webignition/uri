<?php

declare(strict_types=1);

namespace webignition\Uri\Tests;

use webignition\Uri\Path;

class PathTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(string $path, string $expectedPath): void
    {
        $path = new Path($path);

        $this->assertEquals($expectedPath, (string) $path);
    }

    /**
     * @return array<mixed>
     */
    public function createDataProvider(): array
    {
        return [
            'empty' => [
                'pathString' => '',
                'expectedPath' => '',
            ],
            'non-empty' => [
                'pathString' => '/foo',
                'expectedPath' => '/foo',
            ],
            'percent-encode unicode characters' => [
                'path' => '/Nattō',
                'expectedPath' => '/Natt%C5%8D',
            ],
        ];
    }

    /**
     * @dataProvider isRelativeDataProvider
     */
    public function testIsRelative(string $pathString, bool $expectedIsRelative): void
    {
        $path = new Path($pathString);

        $this->assertEquals($expectedIsRelative, $path->isRelative());
    }

    /**
     * @return array<mixed>
     */
    public function isRelativeDataProvider(): array
    {
        return [
            'empty path is relative' => [
                'pathString' => '',
                'expectedIsRelative' => true,
            ],
            'foo is relative' => [
                'pathString' => 'foo',
                'expectedIsRelative' => true,
            ],
            '/foo is not relative' => [
                'pathString' => '/foo',
                'expectedIsRelative' => false,
            ],
        ];
    }

    /**
     * @dataProvider isAbsoluteDataProvider
     */
    public function testIsAbsolute(string $pathString, bool $expectedIsAbsolute): void
    {
        $path = new Path($pathString);

        $this->assertEquals($expectedIsAbsolute, $path->isAbsolute());
    }

    /**
     * @return array<mixed>
     */
    public function isAbsoluteDataProvider(): array
    {
        return [
            'empty path is not absolute' => [
                'pathString' => '',
                'expectedIsAbsolute' => false,
            ],
            'foo is not absolute' => [
                'pathString' => 'foo',
                'expectedIsAbsolute' => false,
            ],
            '/foo is absolute' => [
                'pathString' => '/foo',
                'expectedIsAbsolute' => true,
            ],
        ];
    }

    /**
     * @dataProvider filenameAndDirectoryPropertiesDataProvider
     */
    public function testFilenameAndDirectoryProperties(
        string $pathString,
        bool $expectedHasFilename,
        string $expectedFilename,
        string $expectedDirectory,
        bool $expectedHasTrailingSlash
    ): void {
        $path = new Path($pathString);

        $this->assertEquals($expectedHasFilename, $path->hasFilename());
        $this->assertEquals($expectedFilename, $path->getFilename());
        $this->assertEquals($expectedDirectory, $path->getDirectory());
        $this->assertEquals($expectedHasTrailingSlash, $path->hasTrailingSlash());
    }

    /**
     * @return array<mixed>
     */
    public function filenameAndDirectoryPropertiesDataProvider(): array
    {
        return [
            '/example/' => [
                'pathString' => '/example/',
                'expectedHasFilename' => false,
                'expectedFilename' => '',
                'expectedDirectory' => '/example/',
                'expectedHasTrailingSlash' => true,
            ],
            '/file.txt' => [
                'pathString' => '/file.txt',
                'expectedHasFilename' => true,
                'expectedFilename' => 'file.txt',
                'expectedDirectory' => '/',
                'expectedHasTrailingSlash' => false,
            ],
            '/example/file.txt' => [
                'pathString' => '/example/file.txt',
                'expectedHasFilename' => true,
                'expectedFilename' => 'file.txt',
                'expectedDirectory' => '/example',
                'expectedHasTrailingSlash' => false,
            ],
            '/example/file.txt/e' => [
                'pathString' => '/example/file.txt/',
                'expectedHasFilename' => false,
                'expectedFilename' => '',
                'expectedDirectory' => '/example/file.txt/',
                'expectedHasTrailingSlash' => true,
            ],
        ];
    }

    /**
     * @dataProvider hasTrailingSlashDataProvider
     */
    public function testHasTrailingSlash(string $pathString, bool $expectedHasTrailingSlash): void
    {
        $path = new Path($pathString);

        $this->assertSame($expectedHasTrailingSlash, $path->hasTrailingSlash());
    }

    /**
     * @return array<mixed>
     */
    public function hasTrailingSlashDataProvider(): array
    {
        return [
            'empty path does not have trailing slash' => [
                'pathString' => '',
                'expectedHasTrailingSlash' => false,
            ],
            'does not have trailing slash' => [
                'pathString' => '/path',
                'expectedHasTrailingSlash' => false,
            ],
            'has trailing slash' => [
                'pathString' => '/path/',
                'expectedHasTrailingSlash' => true,
            ],
        ];
    }
}
