<?php declare(strict_types=1);

namespace Shopware\Tests\Integration\Core\Framework\App;

use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Util\Host;

/**
 * @internal
 */
#[Package('framework')]
class PrivateHost extends Host
{
    public function isPublicUrl(string $url): bool
    {
        return false;
    }
}
