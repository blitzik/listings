<?php declare(strict_types=1);

namespace Listings\DI;

use Nette\Application\IPresenterFactory;
use Kdyby\Doctrine\DI\IEntityProvider;
use Listings\Fixtures\ListingsFixture;
use Nette\DI\CompilerExtension;
use Fixtures\IFixtureProvider;
use Nette\DI\Compiler;

final class ListingsExtension extends CompilerExtension implements IEntityProvider, IFixtureProvider
{
    public function loadConfiguration(): void
    {
        $cb = $this->getContainerBuilder();
        Compiler::loadDefinitions($cb, $this->loadFromFile(__DIR__ . '/services.neon'), $this->name);
    }


    public function beforeCompile(): void
    {
        $cb = $this->getContainerBuilder();

        $cb->getDefinition($cb->getByType(IPresenterFactory::class))
           ->addSetup('setMapping', [['Listings' => 'Listings\\*Module\\Presenters\\*Presenter']]);

        $latteFactory = $cb->getDefinition('nette.latteFactory');
        $latteFactory->addSetup('addFilter', ['invoiceTimeHoursAndMinutes', $this->prefix('@invoiceTimeFilter')])
                     ->addSetup('addFilter', ['invoiceTimeWithComma', $this->prefix('@invoiceTimeWithCommaFilter')]);
    }


    function getEntityMappings(): array
    {
        return ['Listings' => __DIR__ . '/..'];
    }


    public function getDataFixtures(): array
    {
        return [
            __DIR__ . '/../fixtures' => [
                ListingsFixture::class
            ]
        ];
    }

}