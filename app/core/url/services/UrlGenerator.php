<?php

namespace Url\Generators;

use Doctrine\Common\Persistence\ObjectManager;
use Nette\SmartObject;
use Url\Url;

class UrlGenerator
{
    use SmartObject;
    
    
    /** @var ObjectManager */
    private $em;

    /** @var string */
    private $presenter;


    public function __construct($presenter, ObjectManager $manager)
    {
        $this->presenter = $presenter;
        $this->em = $manager;
    }


    /**
     * @param string $presenter
     * @return $this
     */
    public function addPresenter($presenter)
    {
        $this->presenter = $presenter;

        return $this;
    }


    /**
     * @param string $url
     * @param string|null $presenterAction
     * @param null $internal_id
     * @return $this
     */
    public function addUrl($url, $presenterAction = null, $internal_id = null)
    {
        $url = self::create($url, $this->presenter, $presenterAction, $internal_id);
        $this->em->persist($url);

        return $this;
    }


    /**
     * @param $urlPath
     * @param $presenter
     * @param $action
     * @param null $internal_id
     * @return Url
     */
    public static function create($urlPath, $presenter, $action, $internal_id = null)
    {
        $url = new Url();
        $url->setUrlPath($urlPath);
        $url->setDestination($presenter, $action);
        $url->setInternalId($internal_id);

        return $url;
    }



}