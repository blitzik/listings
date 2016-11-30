<?php

namespace App\Extensions;

use Nette\Application\IPresenterFactory;
use Nette\DI\ContainerBuilder;

class CompilerExtension extends \Nette\DI\CompilerExtension
{
    /**
     * @param ContainerBuilder $builder
     * @param array $mapping
     */
    public function setPresenterMapping(ContainerBuilder $builder, array $mapping)
    {
        $builder->getDefinition($builder->getByType(IPresenterFactory::class))
                ->addSetup('setMapping', [$mapping]);
    }
}