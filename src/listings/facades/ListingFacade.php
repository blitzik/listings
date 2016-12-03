<?php

namespace Listings\Facades;

use Kdyby\Doctrine\EntityManager;
use Nette\SmartObject;

class ListingFacade
{
    use SmartObject;


    /** @var EntityManager */
    private $em;


    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }



}