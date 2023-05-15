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

use Psr\Http\Message\ServerRequestInterface;
use Teknoo\East\Common\Contracts\Loader\LoaderInterface;
use Teknoo\East\Common\Contracts\Recipe\Step\ListObjectsAccessControlInterface;
use Teknoo\East\Common\Contracts\Recipe\Step\SearchFormLoaderInterface;
use Teknoo\East\Common\Recipe\Step\ExtractOrder;
use Teknoo\East\Common\Recipe\Step\ExtractPage;
use Teknoo\East\Common\Recipe\Step\LoadListObjects;
use Teknoo\East\Common\Recipe\Step\LoadObject;
use Teknoo\East\Common\Recipe\Step\RenderError;
use Teknoo\East\Common\Recipe\Step\RenderList;
use Teknoo\East\Foundation\Recipe\CookbookInterface;
use Teknoo\Recipe\Bowl\Bowl;
use Teknoo\Recipe\Cookbook\BaseCookbookTrait;
use Teknoo\Recipe\Ingredient\Ingredient;
use Teknoo\Recipe\RecipeInterface;
use Teknoo\Space\Recipe\Step\Job\PrepareCriteria as JobPrepareCriteria;

/**
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class JobList implements CookbookInterface
{
    use BaseCookbookTrait;

    /**
     * @param array<string, string> $loadListObjectsWiths
     */
    public function __construct(
        RecipeInterface $recipe,
        private LoadObject $loadObject,
        private ExtractPage $extractPage,
        private ExtractOrder $extractOrder,
        private JobPrepareCriteria $jobPrepareCriteria,
        private LoadListObjects $loadListObjects,
        private RenderList $renderList,
        private RenderError $renderError,
        private SearchFormLoaderInterface $searchFormLoader,
        private ListObjectsAccessControlInterface $listObjectsAccessControl,
        private string $defaultErrorTemplate,
        private array $loadListObjectsWiths = [],
    ) {
        $this->fill($recipe);
    }

    protected function populateRecipe(RecipeInterface $recipe): RecipeInterface
    {
        $recipe = $recipe->require(new Ingredient(ServerRequestInterface::class, 'request'));
        $recipe = $recipe->require(new Ingredient(LoaderInterface::class, 'loader'));
        $recipe = $recipe->require(new Ingredient('string', 'defaultOrderDirection'));
        $recipe = $recipe->require(new Ingredient('int', 'itemsPerPage'));
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
            00
        );

        $recipe = $recipe->cook($this->extractPage, ExtractPage::class, [], 00);

        $recipe = $recipe->cook($this->extractOrder, ExtractOrder::class, [], 10);

        $recipe = $recipe->cook($this->searchFormLoader, SearchFormLoaderInterface::class, [], 20);

        $recipe = $recipe->cook($this->jobPrepareCriteria, JobPrepareCriteria::class, [], 25);

        $recipe = $recipe->cook($this->loadListObjects, LoadListObjects::class, $this->loadListObjectsWiths, 30);

        $recipe = $recipe->cook(
            $this->listObjectsAccessControl,
            ListObjectsAccessControlInterface::class,
            [],
            40
        );

        $recipe = $recipe->cook($this->renderList, RenderList::class, [], 50);

        $recipe = $recipe->onError(new Bowl($this->renderError, []));

        $this->addToWorkplan('errorTemplate', $this->defaultErrorTemplate);

        return $recipe;
    }
}
