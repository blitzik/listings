<?php

namespace Url\Panel;

use Nette\Application\Request;
use Url\Services\Router;
use Nette\Http\IRequest;
use Nette\SmartObject;
use Tracy\IBarPanel;
use Tracy\Dumper;

class RequestPanel implements IBarPanel
{
    use SmartObject;


    /** @var  Request */
    private $appRequest;

    /** @var \HttpRequest  */
    private $httpRequest;

    /** @var Router  */
    private $router;

    public function __construct(IRequest $httpRequest, Router $router)
    {
        $this->httpRequest = $httpRequest;
        $this->router = $router;
        $this->appRequest = $router->match($httpRequest);
    }

    /**
     * Renders HTML code for custom tab.
     * @return string
     */
    function getTab()
    {
        ob_start();
        echo 'APP_REQUEST';
        return ob_get_clean();
    }

    /**
     * Renders HTML code for custom panel.
     * @return string
     */
    function getPanel()
    {
        ob_start();
        echo Dumper::toHtml($this->appRequest);
        return ob_get_clean();
    }

}