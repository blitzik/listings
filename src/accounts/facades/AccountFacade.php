<?php

namespace Accounts\Facades;

use Kdyby\Doctrine\EntityManager;
use Nette\SmartObject;

class AccountFacade
{
    use SmartObject;


    /** @var EntityManager */
    private $em;


    public function __construct(
        EntityManager $entityManager
    ) {
        $this->em = $entityManager;
    }





}