<?php

/*
 * Teknoo Space.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * it is available in LICENSE file at the root of this package
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 *
 * @link        http://teknoo.space Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\Space\Tests\Unit\Object\Config;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Teknoo\Space\Object\Config\SubscriptionPlan;
use Teknoo\Space\Object\Config\SubscriptionPlanCatalog;

use function iterator_to_array;

/**
 * Class SearchTest.
 *
 * @copyright Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @author Richard Déloge <richard@teknoo.software>
 *
 */
#[CoversClass(SubscriptionPlanCatalog::class)]
class SubscriptionPlanCatalogTest extends TestCase
{
    private SubscriptionPlanCatalog $catalog;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->catalog = new SubscriptionPlanCatalog(
            ['Foo' => $this->createMock(SubscriptionPlan::class)],
        );
    }

    public function testConstruct(): void
    {
        self::assertInstanceOf(
            SubscriptionPlan::class,
            iterator_to_array($this->catalog)['Foo'],
        );
    }
}
