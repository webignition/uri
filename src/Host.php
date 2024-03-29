<?php

declare(strict_types=1);

namespace webignition\Uri;

use IpUtils\Address\IPv4;
use IpUtils\Exception\InvalidExpressionException;
use IpUtils\Expression\Subnet;
use IpUtils\Factory as IpUtilsFactory;

class Host
{
    public const HOST_PART_SEPARATOR = '.';

    public const UNROUTABLE_THIS_NETWORK_RANGE = '0.0.0.0/8';
    public const UNROUTABLE_PRIVATE_USE_NETWORKS_10_RANGE = '10.0.0.0/8';
    public const UNROUTABLE_LOOPBACK_RANGE = '127.0.0.0/8';
    public const UNROUTABLE_LINK_LOCAL_RANGE = '169.254.0.0/16';
    public const UNROUTABLE_PRIVATE_USE_NETWORKS_172_RANGE = '172.16.0.0/12';
    public const UNROUTABLE_IETF_PROTOCOL_ASSIGNMENTS_RANGE = '192.0.0.0/24';
    public const UNROUTABLE_TEST_NET_1_RANGE = '192.0.2.0/24';
    public const UNROUTABLE_6_TO_4_ANYCAST_RANGE = '192.88.99.0/24';
    public const UNROUTABLE_PRIVATE_USE_NETWORKS_192_RANGE = '192.168.0.0/16';
    public const UNROUTABLE_BENCHMARK_TESTING_RANGE = '198.18.0.0/15';
    public const UNROUTABLE_TEST_NET_2_RANGE = '198.51.100.0/24';
    public const UNROUTABLE_TEST_NET_3_RANGE = '203.0.113.0/24';
    public const UNROUTABLE_MULTICAST_RANGE = '224.0.0.0/4';
    public const UNROUTABLE_FUTURE_USE_RANGE = '240.0.0.0/4';
    public const UNROUTABLE_LIMITED_BROADCAST_RANGE = '255.255.255.255/32';

    /**
     * @var string[]
     */
    private array $unrouteableRanges = [
        self::UNROUTABLE_THIS_NETWORK_RANGE,
        self::UNROUTABLE_PRIVATE_USE_NETWORKS_10_RANGE,
        self::UNROUTABLE_LOOPBACK_RANGE,
        self::UNROUTABLE_LINK_LOCAL_RANGE,
        self::UNROUTABLE_PRIVATE_USE_NETWORKS_172_RANGE,
        self::UNROUTABLE_IETF_PROTOCOL_ASSIGNMENTS_RANGE,
        self::UNROUTABLE_TEST_NET_1_RANGE,
        self::UNROUTABLE_6_TO_4_ANYCAST_RANGE,
        self::UNROUTABLE_PRIVATE_USE_NETWORKS_192_RANGE,
        self::UNROUTABLE_BENCHMARK_TESTING_RANGE,
        self::UNROUTABLE_TEST_NET_2_RANGE,
        self::UNROUTABLE_TEST_NET_3_RANGE,
        self::UNROUTABLE_MULTICAST_RANGE,
        self::UNROUTABLE_FUTURE_USE_RANGE,
        self::UNROUTABLE_LIMITED_BROADCAST_RANGE
    ];

    private string $host;

    /**
     * @var string[]
     */
    private array $parts;

    public function __construct(string $host)
    {
        $this->host = $host;
        $this->parts = explode(self::HOST_PART_SEPARATOR, $host);
    }

    public function __toString(): string
    {
        return $this->host;
    }

    /**
     * @return string[]
     */
    public function getParts(): array
    {
        return $this->parts;
    }

    public function equals(Host $comparator): bool
    {
        return (string) $this === (string) $comparator;
    }

    /**
     * @param string[] $excludedParts
     */
    public function isEquivalentTo(Host $comparator, array $excludedParts = []): bool
    {
        $thisHost = new Host(PunycodeEncoder::encode((string) $this));
        $comparatorHost = new Host(PunycodeEncoder::encode((string) $comparator));

        if (empty($excludedParts)) {
            return $thisHost->equals($comparatorHost);
        }

        $thisParts = $this->excludeParts($thisHost->getParts(), $excludedParts);
        $comparatorParts = $this->excludeParts($comparatorHost->getParts(), $excludedParts);

        return $thisParts === $comparatorParts;
    }

    /**
     * @throws InvalidExpressionException
     */
    public function isPubliclyRoutable(): bool
    {
        try {
            $ip = IpUtilsFactory::getAddress($this->host);

            if ($ip->isPrivate()) {
                return false;
            }

            if ($ip->isLoopback()) {
                return false;
            }

            if ($ip instanceof IPv4 && $this->isIpv4InUnroutableRange($ip)) {
                return false;
            }

            return true;
        } catch (\UnexpectedValueException $unexpectedValueException) {
            return true;
        }
    }

    /**
     * @param string[] $parts
     * @param string[] $exclusions
     *
     * @return string[]
     */
    private function excludeParts(array $parts, array $exclusions): array
    {
        $filteredParts = [];

        foreach ($parts as $index => $part) {
            if (!isset($exclusions[$index]) || $exclusions[$index] !== $part) {
                $filteredParts[] = $part;
            }
        }

        return $filteredParts;
    }

    /**
     * @throws InvalidExpressionException
     */
    private function isIpv4InUnroutableRange(IPv4 $ip): bool
    {
        foreach ($this->unrouteableRanges as $ipRange) {
            if ($ip->matches(new Subnet($ipRange))) {
                return true;
            }
        }

        return false;
    }
}
