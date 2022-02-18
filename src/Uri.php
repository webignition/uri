<?php

declare(strict_types=1);

namespace webignition\Uri;

use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    private string $scheme = '';
    private string $userInfo = '';
    private string $host = '';
    private ?int $port = null;
    private string $path = '';
    private string $query = '';
    private string $fragment = '';

    public function __construct(string $url)
    {
        $components = Parser::parse($url);

        $scheme = (string) ($components[Parser::COMPONENT_SCHEME] ?? '');
        $host = (string) ($components[Parser::COMPONENT_HOST] ?? '');
        $port = $components[Parser::COMPONENT_PORT] ?? null;
        $port = is_int($port) ? $port : null;
        $path = (string) ($components[Parser::COMPONENT_PATH] ?? '');
        $query = (string) ($components[Parser::COMPONENT_QUERY] ?? '');
        $fragment = (string) ($components[Parser::COMPONENT_FRAGMENT] ?? '');
        $user = (string) ($components[Parser::COMPONENT_USER] ?? '');
        $pass = (string) ($components[Parser::COMPONENT_PASS] ?? '');

        $userInfo = new UserInfo($user, $pass);

        self::applyComponents($this, $scheme, (string) $userInfo, $host, $port, $path, $query, $fragment);
    }

    public function __toString(): string
    {
        $uri = '';

        if ('' !== $this->scheme) {
            $uri .= $this->scheme . ':';
        }

        $authority = $this->getAuthority();

        if ('' !== $authority || 'file' === $this->scheme) {
            $uri .= '//' . $authority;
        }

        $path = $this->path;

        if ($authority && $path && '/' !== $path[0]) {
            $path = '/' . $path;
        }

        if ('' === $authority && preg_match('/^\/\//', $path)) {
            $path = '/' . ltrim($path, '/');
        }

        $uri .= $path;

        if ('' !== $this->query) {
            $uri .= '?' . $this->query;
        }

        if ('' !== $this->fragment) {
            $uri .= '#' . $this->fragment;
        }

        return $uri;
    }

    public static function compose(
        string $scheme,
        string $authority,
        string $path,
        string $query,
        string $fragment
    ): self {
        $uriString = '';

        if (!empty($scheme)) {
            $uriString .= $scheme . ':';
        }

        if (!empty($authority)) {
            $uriString .= '//' . $authority;
        }

        if ('' !== $uriString && strlen($path) && '/' !== $path[0]) {
            $path = '/' . $path;
        }

        $uriString .= $path . '?' . $query . '#' . $fragment;

        return new Uri($uriString);
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getAuthority(): string
    {
        $authority = $this->host;
        if ('' !== $this->userInfo) {
            $authority = $this->userInfo . '@' . $authority;
        }

        if (null !== $this->port) {
            $authority .= ':' . $this->port;
        }

        return $authority;
    }

    public function getUserInfo(): string
    {
        return $this->userInfo;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getFragment(): string
    {
        return $this->fragment;
    }

    public function withScheme($scheme): self
    {
        $scheme = trim(strtolower($scheme));

        if ($this->scheme === $scheme) {
            return $this;
        }

        return self::applyComponents(
            clone $this,
            $scheme,
            $this->userInfo,
            $this->host,
            $this->port,
            $this->path,
            $this->query,
            $this->fragment
        );
    }

    public function withUserInfo($user, $password = null): self
    {
        $userInfo = (string) (new UserInfo($user, $password));

        if ($this->userInfo === $userInfo) {
            return $this;
        }

        return self::applyComponents(
            clone $this,
            $this->scheme,
            $userInfo,
            $this->host,
            $this->port,
            $this->path,
            $this->query,
            $this->fragment
        );
    }

    public function withHost($host): self
    {
        $host = trim(strtolower($host));

        if ($this->host === $host) {
            return $this;
        }

        return self::applyComponents(
            clone $this,
            $this->scheme,
            $this->userInfo,
            $host,
            $this->port,
            $this->path,
            $this->query,
            $this->fragment
        );
    }

    public function withPort($port): self
    {
        if (null !== $port) {
            $port = (int) $port;
        }

        if ($this->port === $port) {
            return $this;
        }

        return self::applyComponents(
            clone $this,
            $this->scheme,
            $this->userInfo,
            $this->host,
            $port,
            $this->path,
            $this->query,
            $this->fragment
        );
    }

    public function withPath($path): self
    {
        $path = Filter::filterPath($path);

        if ($this->path === $path) {
            return $this;
        }

        return self::applyComponents(
            clone $this,
            $this->scheme,
            $this->userInfo,
            $this->host,
            $this->port,
            $path,
            $this->query,
            $this->fragment
        );
    }

    public function withQuery($query): self
    {
        $query = Filter::filterQueryOrFragment($query);

        if ($this->query === $query) {
            return $this;
        }

        return self::applyComponents(
            clone $this,
            $this->scheme,
            $this->userInfo,
            $this->host,
            $this->port,
            $this->path,
            $query,
            $this->fragment
        );
    }

    public function withFragment($fragment): self
    {
        $fragment = Filter::filterQueryOrFragment($fragment);

        if ($this->fragment === $fragment) {
            return $this;
        }

        return self::applyComponents(
            clone $this,
            $this->scheme,
            $this->userInfo,
            $this->host,
            $this->port,
            $this->path,
            $this->query,
            $fragment
        );
    }

    private static function applyComponents(
        Uri $url,
        string $scheme,
        string $userInfo,
        string $host,
        ?int $port,
        string $path,
        string $query,
        string $fragment
    ): self {
        $url->scheme = strtolower($scheme);
        $url->userInfo = $userInfo;
        $url->host = strtolower($host);
        $url->path = Filter::filterPath($path);
        $url->query = Filter::filterQueryOrFragment($query);
        $url->fragment = Filter::filterQueryOrFragment($fragment);
        $url->port = Filter::filterPort($port, $url->getScheme());

        return $url;
    }
}
