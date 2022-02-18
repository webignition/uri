<?php

declare(strict_types=1);

namespace webignition\Uri\Tests;

use webignition\Uri\UserInfo;

class UserInfoTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider toStringDataProvider
     */
    public function testToString(string $user, ?string $password, string $expectedString): void
    {
        $userInfo = new UserInfo($user, $password);

        $this->assertSame($expectedString, (string) $userInfo);
    }

    /**
     * @return array<mixed>
     */
    public function toStringDataProvider(): array
    {
        return [
            'user empty, password null' => [
                'user' => '',
                'password' => null,
                'expectedString' => '',
            ],
            'user empty, password empty' => [
                'user' => '',
                'password' => '',
                'expectedString' => '',
            ],
            'user only' => [
                'user' => 'user',
                'password' => null,
                'expectedString' => 'user',
            ],
            'user and password' => [
                'user' => 'user',
                'password' => 'password',
                'expectedString' => 'user:password',
            ],
        ];
    }

    /**
     * @dataProvider fromStringDataProvider
     */
    public function testFromString(string $userInfoString, string $expectedUser, ?string $expectedPassword): void
    {
        $userInfo = UserInfo::fromString($userInfoString);

        $this->assertSame($expectedUser, $userInfo->getUser());
        $this->assertSame($expectedPassword, $userInfo->getPassword());
    }

    /**
     * @return array<mixed>
     */
    public function fromStringDataProvider(): array
    {
        return [
            'empty' => [
                'userInfoString' => '',
                'expectedUser' => '',
                'expectedPassword' => null,
            ],
            'user only' => [
                'userInfoString' => 'user',
                'expectedUser' => 'user',
                'expectedPassword' => null,
            ],
            'user and empty password' => [
                'userInfoString' => 'user:',
                'expectedUser' => 'user',
                'expectedPassword' => null,
            ],
            'user and password' => [
                'userInfoString' => 'user:password',
                'expectedUser' => 'user',
                'expectedPassword' => 'password',
            ],
        ];
    }
}
