<?php declare(strict_types=1);

namespace Fixtures\DI;

use Fixtures\Commands\LoadBasicDataCommand;
use Nette\DI\CompilerExtension;
use Fixtures\IFixtureProvider;

class FixturesExtension extends CompilerExtension
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

}