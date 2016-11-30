<?php

namespace Url\DI;

use Url\Services\UrlParametersConverter;
use Kdyby\Doctrine\DI\IEntityProvider;
use App\Extensions\CompilerExtension;
use App\Fixtures\IFixtureProvider;
use Url\Fixtures\UrlsFixture;
use Url\IUrlParametersMapper;
use Nette\DI\Statement;
use Nette\DI\Compiler;
use Url\RequestPanel;
use Tracy\Bar;

class UrlExtension extends CompilerExtension implements IEntityProvider, IFixtureProvider
{
    private $defaults = [];


    /**
     * Processes configuration data. Intended to be overridden by descendant.
     * @return void
     */
    public function loadConfiguration()
    {
        $config = $this->getConfig() + $this->defaults;
        $this->setConfig($config);

        $cb = $this->getContainerBuilder();

        $cb->removeDefinition('routing.router');
        Compiler::loadDefinitions($cb, $this->loadFromFile(__DIR__ . '/services.neon'), $this->name);
    }


    /**
     * Adjusts DI container before is compiled to PHP class. Intended to be overridden by descendant.
     * @return void
     */
    public function beforeCompile()
    {
        $cb = $this->getContainerBuilder();

        $bar = $cb->getDefinition($cb->getByType(Bar::class));
        $bar->addSetup('addPanel', ['@'.$cb->getByType(RequestPanel::class)]);

        $urlParametersConverter = $cb->addDefinition($this->prefix('urlParametersConverter'));
        $urlParametersConverter->setClass(UrlParametersConverter::class);
        foreach ($cb->findByType(IUrlParametersMapper::class) as $definitionName => $urlParametersMapperDefinition) {
            $urlParametersConverter->addSetup(
                'addMapping',
                [
                    'presenter' => new Statement([sprintf('@%s', $definitionName), 'getPresenter']),
                    'urlParametersMapping' => new Statement([sprintf('@%s', $definitionName), 'getUrlMappings'])
                ]
            );
        }

    }


    /**
     * Returns associative array of Namespace => mapping definition
     *
     * @return array
     */
    public function getEntityMappings()
    {
        return ['Url' => __DIR__ . '/..'];
    }


    /**
     * @return array
     */
    public function getDataFixtures()
    {
        return [
            __DIR__ . '/../fixtures' => [
                UrlsFixture::class
            ]
        ];
    }

}