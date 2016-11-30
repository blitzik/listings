<?php

namespace App\DI;

use App\Commands\LoadBasicDataCommand;
use App\Extensions\CompilerExtension;
use App\Fixtures\IFixtureProvider;
use App\Fixtures\BaseFixture;

class CoreExtension extends CompilerExtension implements IFixtureProvider
{
    public function loadConfiguration()
    {
        $cb = $this->getContainerBuilder();

        $loadBasicDataCommand = $cb->addDefinition($this->prefix('loadBasicDataCommand'));
        $loadBasicDataCommand->setClass(LoadBasicDataCommand::class);
        $loadBasicDataCommand->addTag('kdyby.console.command');
    }


    public function beforeCompile()
    {
        $cb = $this->getContainerBuilder();

        $loadBasicDataCommand = $cb->getDefinition($this->prefix('loadBasicDataCommand'));

        //-- fixtures
        foreach ($this->compiler->getExtensions() as $extension) {
            if (!$extension instanceof IFixtureProvider) {
                continue;
            }

            foreach ($extension->getDataFixtures() as $directory => $fixturesClassNames) {
                foreach ($fixturesClassNames as $fixtureClassName) {
                    $loadBasicDataCommand->addSetup('addFixture', [$fixtureClassName]);
                }
            }
        }
    }


    public function getDataFixtures()
    {
        return [
            __DIR__ . '/../fixtures' => [
                BaseFixture::class
            ]
        ];
    }


}