<?php

namespace Localization\DI;

use Localization\Fixtures\LocalizationFixture;
use Kdyby\Doctrine\DI\IEntityProvider;
use App\Extensions\CompilerExtension;
use App\Fixtures\IFixtureProvider;
use Nette\DI\Compiler;

class LocalizationExtension extends CompilerExtension implements IEntityProvider, IFixtureProvider
{
    public function loadConfiguration()
    {
        $cb = $this->getContainerBuilder();
        Compiler::loadDefinitions($cb, $this->loadFromFile(__DIR__ . '/services.neon'), $this->name);
    }


    public function beforeCompile()
    {
        $cb = $this->getContainerBuilder();
    }


    function getEntityMappings()
    {
        return ['Localization' => __DIR__ . '/..'];
    }


    /**
     * @return array
     */
    public function getDataFixtures()
    {
        return [
            __DIR__ . '/../fixtures/basic' => [
                LocalizationFixture::class
            ]
        ];
    }


}