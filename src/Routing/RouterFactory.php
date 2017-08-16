<?php declare(strict_types = 1);

namespace Routing;

use Nette\Application\Routers\RouteList;
use Nette\Application\IRouter;
use Nette\StaticClass;

class RouterFactory
{
    use StaticClass;


    /**
     * @param IRouter $customRouter
     * @return IRouter
     */
    public static function createRouter(IRouter $customRouter): IRouter
    {
        $router = new RouteList();

        $router[] = $customRouter;

        return $router;
    }

}