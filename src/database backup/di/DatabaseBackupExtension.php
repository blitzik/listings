<?php

namespace DatabaseBackup;

use DatabaseBackup\Fixtures\DefaultFixture;
use App\Extensions\CompilerExtension;
use App\Fixtures\IFixtureProvider;
use Nette\DI\Compiler;

class DatabaseBackupExtension extends CompilerExtension implements IFixtureProvider
{
    public function loadConfiguration()
    {
        $cb = $this->getContainerBuilder();
        Compiler::loadDefinitions($cb, $this->loadFromFile(__DIR__ . '/services.neon'), $this->name);
    }


    public function beforeCompile()
    {
        $config = $this->getConfig();
        $cb = $this->getContainerBuilder();

        $this->setPresenterMapping($cb, ['DatabaseBackup' => 'DatabaseBackup\\*Module\\Presenters\\*Presenter']);

        $ftpBackupFileHandler = $cb->getDefinition($this->prefix('ftpBackupFileHandler'));
        $ftpBackupFileHandler->setArguments([$config['ftps']]);

        $databaseBackup = $cb->getDefinition($this->prefix('databaseBackup'));
        $databaseBackup->setArguments([$config['databaseCredentials'], $config['backupTempPath']])
                       ->addSetup('addHandler', ['@'.$this->prefix('ftpBackupFileHandler')]);

        $backupSubscriber = $cb->getDefinition($this->prefix('databaseBackupSubscriber'));
        $backupSubscriber->setArguments([$config['sender'], $config['receivers']]);


        $cronBackupPresenter = $cb->getDefinition($this->prefix('cronBackupPresenter'));
        $cronBackupPresenter->setArguments([$config['urlPassword']]);
    }

    
    /**
     * @return array
     */
    public function getDataFixtures()
    {
        return [
            __DIR__ . '/../fixtures' => [
                DefaultFixture::class
            ]
        ];
    }

}