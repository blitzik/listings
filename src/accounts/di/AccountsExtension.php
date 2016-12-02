<?php

namespace Accounts;

use Accounts\Fixtures\AccountsFixture;
use Kdyby\Doctrine\DI\IEntityProvider;
use App\Extensions\CompilerExtension;
use App\Fixtures\IFixtureProvider;
use Nette\DI\Compiler;

class AccountsExtension extends CompilerExtension implements IEntityProvider, IFixtureProvider
{
    public function loadConfiguration()
    {
        $cb = $this->getContainerBuilder();
        Compiler::loadDefinitions($cb, $this->loadFromFile(__DIR__ . '/services.neon'), $this->name);
    }


    public function beforeCompile()
    {
        $cb = $this->getContainerBuilder();

        $this->setPresenterMapping($cb, ['Accounts' => 'Accounts\\*Module\\Presenters\\*Presenter']);
    }


    function getEntityMappings()
    {
        return ['Accounts' => __DIR__ . '/..'];
    }


    /**
     * @return array
     */
    public function getDataFixtures()
    {
        return [
            __DIR__ . '/../fixtures' => [
                AccountsFixture::class
            ]
        ];
    }


}