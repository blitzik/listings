<?php

namespace Listings\DI;

use Kdyby\Doctrine\DI\IEntityProvider;
use App\Extensions\CompilerExtension;
use Listings\Fixtures\ListingsFixture;
use App\Fixtures\IFixtureProvider;
use Nette\DI\Compiler;

final class ListingsExtension extends CompilerExtension implements IEntityProvider, IFixtureProvider
{
    public function loadConfiguration()
    {
        $cb = $this->getContainerBuilder();
        Compiler::loadDefinitions($cb, $this->loadFromFile(__DIR__ . '/services.neon'), $this->name);
    }


    public function beforeCompile()
    {
        $cb = $this->getContainerBuilder();
        $this->setPresenterMapping($cb, ['Listings' => 'Listings\\*Module\\Presenters\\*Presenter']);

        $latteFactory = $cb->getDefinition('nette.latteFactory');
        $latteFactory->addSetup('addFilter', ['invoiceTimeHoursAndMinutes', $this->prefix('@invoiceTimeFilter')])
                     ->addSetup('addFilter', ['invoiceTimeWithComma', $this->prefix('@invoiceTimeWithCommaFilter')]);
    }


    function getEntityMappings()
    {
        return ['Listings' => __DIR__ . '/..'];
    }


    /**
     * @return array
     */
    public function getDataFixtures()
    {
        return [
            __DIR__ . '/../fixtures' => [
                ListingsFixture::class
            ]
        ];
    }

}