<?php declare(strict_types=1);

namespace Common\DI;

use Nette\Application\IPresenterFactory;
use Common\Templates\TemplateFactory;
use Nette\DI\CompilerExtension;
use Nette\DI\Compiler;

class CommonExtension extends CompilerExtension
{
    private $defaults = [
        'imagesPath' => '%wwwDir%/images'
    ];


    public function loadConfiguration(): void
    {
        $config = $this->getConfig() + $this->defaults;
        $this->setConfig($config);

        $cb = $this->getContainerBuilder();
        Compiler::loadDefinitions($cb, $this->loadFromFile(__DIR__ . '/services.neon'), $this->name);

        $templateFactory = $cb->addDefinition($this->prefix('templateFactory'));
        $templateFactory->setClass(TemplateFactory::class)
                        ->setArguments(['imagesPath' => $config['imagesPath']]);
    }


    public function beforeCompile(): void
    {
        $cb = $this->getContainerBuilder();

        $cb->getDefinition($cb->getByType(IPresenterFactory::class))
           ->addSetup('setMapping', [['Common' => 'Common\\*Module\\Presenters\\*Presenter']]);

        $cb->removeDefinition('latte.templateFactory');
    }

}