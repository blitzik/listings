<?php declare(strict_types=1);

namespace Accounts\DI;

use Nette\Application\IPresenterFactory;
use Accounts\Fixtures\AccountsFixture;
use Kdyby\Doctrine\DI\IEntityProvider;
use Nette\DI\CompilerExtension;
use Fixtures\IFixtureProvider;
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
        $config = $this->getConfig();

        $cb->getDefinition($cb->getByType(IPresenterFactory::class))
           ->addSetup('setMapping', [['Accounts' => 'Accounts\\*Module\\Presenters\\*Presenter']]);

        $forgottenPassword = $cb->getDefinition($this->prefix('forgottenPasswordFormControlFactory'));
        $forgottenPassword->addSetup('setApplicationUrl', [$config['applicationUrl']])
                          ->addSetup('setAdminEmail', [$config['adminEmail']])
                          ->addSetup('setAdminFullName', [$config['adminFullName']]);
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