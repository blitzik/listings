<?php declare(strict_types=1);

namespace Common\Templates;

use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\Application\UI\ITemplate;
use Nette\Application\UI\Control;
use Nette\Security\IAuthorizator;
use Nette;

class TemplateFactory extends \Nette\Bridges\ApplicationLatte\TemplateFactory
{
    /** @var IAuthorizator */
    private $authorizator;

    /** @var Nette\Localization\ITranslator */
    private $translator;

    /** @var string */
    private $imagesPath;


    public function __construct(
        string $imagesPath,
        ILatteFactory $latteFactory,
        Nette\Http\IRequest $httpRequest,
        Nette\Security\User $user,
        Nette\Caching\IStorage $cacheStorage,
        IAuthorizator $authorizator,
        Nette\Localization\ITranslator $translator
    ) {
        parent::__construct($latteFactory, $httpRequest, $user, $cacheStorage);

        $this->authorizator = $authorizator;
        $this->translator = $translator;
        $this->imagesPath = $imagesPath;
    }


    public function createTemplate(Control $control = null): ITemplate
    {
        $template =  parent::createTemplate($control);

        $template->authorizator = $this->authorizator;
        $template->setTranslator($this->translator);

        $template->imagesPath = $template->basePath . '/' . $this->imagesPath;

        return $template;
    }

}