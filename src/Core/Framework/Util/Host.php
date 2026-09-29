<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Util;

use Shopware\Core\Framework\Log\Package;
use Symfony\Component\HttpFoundation\IpUtils;

/**
 * @internal
 */
#[Package('framework')]
class Host
{
    /**
     * @var \Closure(string): list<string>
     */
    private readonly \Closure $resolveHost;

    /**
     * @param (\Closure(string): list<string>)|null $resolveHost
     */
    public function __construct(?\Closure $resolveHost = null)
    {
        $this->resolveHost = $resolveHost ?? static fn (string $host): array => gethostbynamel($host) ?: [];
    }

    public function isPublicUrl(string $url): bool
    {
        $host = parse_url($url, \PHP_URL_HOST);

        return \is_string($host) && $this->isPublic($host);
    }

    private function isPublic(string $host): bool
    {
        $host = trim($host, '[]');

        if ($host === '') {
            return false;
        }

        $ips = filter_var($host, \FILTER_VALIDATE_IP) !== false ? [$host] : ($this->resolveHost)($host);

        if ($ips === []) {
            return false;
        }

        foreach ($ips as $ip) {
            if (filter_var($ip, \FILTER_VALIDATE_IP) === false || IpUtils::isPrivateIp($ip)) {
                return false;
            }
        }

        return true;
    }
}
