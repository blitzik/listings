<?php

namespace App\Templates;

use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Users\Authorization\Authorizator;
use Nette\Application\UI;
use Nette;

class TemplateFactory extends \Nette\Bridges\ApplicationLatte\TemplateFactory
{
    /** @var Authorizator */
    private $authorizator;


    public function __construct(
        ILatteFactory $latteFactory,
        Nette\Http\IRequest $httpRequest,
        Nette\Security\User $user,
        Nette\Caching\IStorage $cacheStorage,
        Authorizator $authorizator
    ) {
        parent::__construct($latteFactory, $httpRequest, $user, $cacheStorage);

        $this->authorizator = $authorizator;
    }


    /**
     * @return Template
     */
    public function createTemplate(UI\Control $control = null)
    {
        $template =  parent::createTemplate($control);

        $template->authorizator = $this->authorizator;

        return $template;
    }

}