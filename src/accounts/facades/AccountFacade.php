<?php

declare(strict_types = 1);

namespace Accounts\Facades;

use Accounts\Exceptions\Runtime\EmailIsInUseException;
use Accounts\Services\Persisters\UserPersister;
use Kdyby\Doctrine\EntityManager;
use Nette\SmartObject;
use Users\User;

class AccountFacade
{
    use SmartObject;


    /** @var UserPersister */
    private $userPersister;

    /** @var EntityManager */
    private $em;


    public function __construct(
        EntityManager $entityManager,
        UserPersister $userPersister
    ) {
        $this->em = $entityManager;
        $this->userPersister = $userPersister;
    }


    /**
     * @param array $values
     * @return \Users\User
     * @throws EmailIsInUseException
     * @throws \Exception
     */
    public function createAccount(array $values): User
    {
        return $this->userPersister->save($values);
    }


}