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

namespace Teknoo\Space\Recipe\Step\Account;

use RuntimeException;
use Teknoo\East\Foundation\Manager\ManagerInterface;
use Teknoo\Space\Object\Config\SubscriptionPlanCatalog;
use Teknoo\Space\Object\DTO\SpaceAccount;

/**
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class SetQuota
{
    public function __construct(
        private SubscriptionPlanCatalog $catalog
    ) {
    }

    public function __invoke(
        ManagerInterface $manager,
        SpaceAccount $spaceAccount,
        ?string $subscriptionPlanId = null,
    ): self {
        $accountData = $spaceAccount->accountData;

        if (null === $accountData) {
            throw new RuntimeException('Missing Space Account data');
        }

        if (!empty($subscriptionPlanId)) {
            $accountData->setSubscriptionPlan($subscriptionPlanId);
        }

        $accountData->visit(
            'subscriptionPlan',
            function (string $planId) use ($spaceAccount): void {
                if (empty($planId)) {
                    return;
                }

                $plan = $this->catalog->getSubscriptionPlan($planId);
                $spaceAccount->account->setQuotas($plan->getQuotas());
            }
        );

        return $this;
    }
}
