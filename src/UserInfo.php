<?php

declare(strict_types=1);

namespace webignition\Uri;

class UserInfo
{
    public const USER_PASS_DELIMITER = ':';

    private string $user;
    private ?string $password;

    public function __construct(string $user, ?string $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    public function __toString(): string
    {
        $userInfo = '';

        if (!empty($this->user)) {
            $userInfo .= $this->user;
        }

        if (!empty($this->password)) {
            $userInfo .= self::USER_PASS_DELIMITER . $this->password;
        }

        return $userInfo;
    }

    public static function fromString(string $userInfo): UserInfo
    {
        $parts = explode(self::USER_PASS_DELIMITER, $userInfo, 2);
        $partCount = count($parts);

        $user = '';
        $password = null;

        if (0 !== $partCount) {
            $user = $parts[0];

            if ($partCount > 1) {
                $password = $parts[1];
                $password = empty($password) ? null : $password;
            }
        }

        return new self($user, $password);
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }
}
