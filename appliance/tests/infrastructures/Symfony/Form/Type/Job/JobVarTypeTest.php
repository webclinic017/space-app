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

namespace Teknoo\Space\Tests\Unit\Infrastructures\Symfony\Form\Type\Job;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Teknoo\Space\Infrastructures\Symfony\Form\Type\Job\JobVarType;

/**
 * Class JobVarTypeTest.
 *
 * @copyright Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @author Richard Déloge <richard@teknoo.software>
 *
 * @covers \Teknoo\Space\Infrastructures\Symfony\Form\Type\Job\JobVarType
 */
class JobVarTypeTest extends TestCase
{
    private JobVarType $jobVarType;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        
        $this->jobVarType = new JobVarType();
    }

    public function testGetBlockPrefix(): void
    {
        self::assertIsString($this->jobVarType->getBlockPrefix());
    }

    public function testBuildForm(): void
    {
        self::assertInstanceOf(
            JobVarType::class,
            $this->jobVarType->buildForm(
                $this->createMock(FormBuilderInterface::class),
                ['foo' => 'bar'],
            ),
        );
    }

    public function testConfigureOptions(): void
    {
        self::assertInstanceOf(
            JobVarType::class,
            $this->jobVarType->configureOptions(
                $this->createMock(OptionsResolver::class),
            ),
        );
    }
}
