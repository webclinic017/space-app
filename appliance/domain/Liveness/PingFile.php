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

namespace Teknoo\Space\Liveness;

use DateTimeInterface;
use Teknoo\East\Foundation\Time\DatesService;

use function file_put_contents;

/**
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 *
 */
class PingFile
{
    public function __construct(
        private DatesService $datesService,
        private string $pingFilePath,
    ) {
    }

    public function __invoke(): self
    {
        $this->datesService->passMeTheDate(
            setter: function (DateTimeInterface $dateTime): void {
                file_put_contents(
                    $this->pingFilePath,
                    $dateTime->getTimestamp(),
                );
            },
            preferRealDate: true,
        );

        return $this;
    }
}
