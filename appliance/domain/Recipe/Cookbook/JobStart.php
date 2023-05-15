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

namespace Teknoo\Space\Recipe\Cookbook;

use Teknoo\East\Common\Contracts\Recipe\Step\FormHandlingInterface;
use Teknoo\East\Common\Contracts\Recipe\Step\FormProcessingInterface;
use Teknoo\East\Common\Contracts\Recipe\Step\ObjectAccessControlInterface;
use Teknoo\East\Common\Contracts\Recipe\Step\RedirectClientInterface;
use Teknoo\East\Common\Contracts\Recipe\Step\RenderFormInterface;
use Teknoo\East\Common\Recipe\Step\CreateObject;
use Teknoo\East\Common\Recipe\Step\LoadObject;
use Teknoo\East\Common\Recipe\Step\RenderError;
use Teknoo\East\Foundation\Recipe\CookbookInterface;
use Teknoo\East\Paas\Object\Project;
use Teknoo\Recipe\Bowl\Bowl;
use Teknoo\Recipe\Cookbook\BaseCookbookTrait;
use Teknoo\Recipe\Ingredient\Ingredient;
use Teknoo\Recipe\RecipeInterface;
use Teknoo\Space\Contracts\Recipe\Step\Job\CallNewJobInterface;
use Teknoo\Space\Contracts\Recipe\Step\Job\NewJobNotifierInterface;
use Teknoo\Space\Recipe\Step\Job\PrepareNewJobForm;
use Teknoo\Space\Recipe\Step\PersistedVariable\LoadPersistedVariablesForJob;

/**
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class JobStart implements CookbookInterface
{
    use BaseCookbookTrait;

    public function __construct(
        RecipeInterface $recipe,
        private LoadObject $loadObject,
        private ObjectAccessControlInterface $objectAccessControl,
        private CreateObject $createObject,
        private PrepareNewJobForm $prepareNewJobForm,
        private LoadPersistedVariablesForJob $loadPersistedVariablesForJob,
        private FormHandlingInterface $formHandling,
        private FormProcessingInterface $formProcessing,
        private NewJobNotifierInterface $newJobNotifier,
        private CallNewJobInterface $callNewJob,
        private RedirectClientInterface $redirectClient,
        private RenderFormInterface $renderForm,
        private RenderError $renderError,
        private string $defaultErrorTemplate,
    ) {
        $this->fill($recipe);
    }

    protected function populateRecipe(RecipeInterface $recipe): RecipeInterface
    {
        $recipe = $recipe->require(new Ingredient('string', 'route'));
        $recipe = $recipe->require(new Ingredient('string', 'objectClass'));
        $recipe = $recipe->require(new Ingredient('string', 'formClass'));
        $recipe = $recipe->require(new Ingredient('array', 'formOptions'));
        $recipe = $recipe->require(new Ingredient('string', 'template'));
        $recipe = $recipe->require(new Ingredient('string', 'projectId'));

        $recipe = $recipe->cook(
            $this->loadObject,
            LoadObject::class . ':Project',
            [
                'loader' => 'projectLoader',
                'id' => 'projectId',
                'workPlanKey' => 'projectKey'
            ],
            05
        );

        $recipe = $recipe->cook(
            $this->objectAccessControl,
            ObjectAccessControlInterface::class,
            [
                'object' => Project::class
            ],
            06
        );

        $recipe = $recipe->cook($this->createObject, CreateObject::class, [], 10);

        $recipe = $recipe->cook($this->loadPersistedVariablesForJob, LoadPersistedVariablesForJob::class, [], 20);

        $recipe = $recipe->cook($this->prepareNewJobForm, PrepareNewJobForm::class, [], 30);

        $recipe = $recipe->cook($this->formHandling, FormHandlingInterface::class, [], 40);

        $recipe = $recipe->cook($this->formProcessing, FormProcessingInterface::class, [], 50);

        $recipe = $recipe->cook($this->newJobNotifier, NewJobNotifierInterface::class, [], 70);

        $recipe = $recipe->cook($this->callNewJob, CallNewJobInterface::class, [], 80);

        $recipe = $recipe->cook(
            $this->redirectClient,
            RedirectClientInterface::class,
            [
                'parameters' => 'routeParameters'
            ],
            90
        );

        $recipe = $recipe->cook($this->renderForm, RenderFormInterface::class, [], 100);

        $recipe = $recipe->onError(new Bowl($this->renderError, []));

        $this->addToWorkplan('nextStep', RenderFormInterface::class);

        $this->addToWorkplan('errorTemplate', $this->defaultErrorTemplate);

        return $recipe;
    }
}
