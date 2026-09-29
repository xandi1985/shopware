<?php declare(strict_types=1);

namespace Shopware\Tests\Unit\Core\Framework\Util;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Util\Host;

/**
 * @internal
 */
#[Package('framework')]
#[CoversClass(Host::class)]
class HostTest extends TestCase
{
    /**
     * @param list<string> $resolvedIps
     */
    #[DataProvider('urlProvider')]
    public function testIsPublicUrl(string $url, array $resolvedIps, bool $expected): void
    {
        $host = new Host(static fn (): array => $resolvedIps);

        static::assertSame($expected, $host->isPublicUrl($url));
    }

    public static function urlProvider(): \Generator
    {
        yield 'host with public addresses' => ['https://registry.example.com/register', ['93.184.215.14', '93.184.215.15'], true];
        yield 'http url with public host' => ['http://registry.example.com/register', ['93.184.215.14'], true];
        yield 'public ip' => ['https://93.184.215.14/register', [], true];
        yield 'loopback' => ['https://app.localhost/register', ['127.0.0.1'], false];
        yield 'docker network' => ['https://app-server/register', ['172.18.0.5'], false];
        yield 'lan' => ['https://app.internal/register', ['192.168.1.10'], false];
        yield 'public and private addresses' => ['https://app.example.com/register', ['93.184.215.14', '10.0.0.1'], false];
        yield 'private ip' => ['https://10.0.0.1/register', [], false];
        yield 'bracketed ipv6 loopback' => ['https://[::1]/register', [], false];
        yield 'unresolvable host' => ['https://unknown.example.com/register', [], false];
        yield 'url without host' => ['https:///register', [], false];
    }
}
