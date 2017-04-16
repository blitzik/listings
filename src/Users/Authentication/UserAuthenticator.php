<?php

namespace Users\Authentication;

use Users\Services\RoleRestricters\IRoleRestricter;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Kdyby\Doctrine\EntityManager;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;
use Nette\Http\IRequest;
use Nette\SmartObject;
use Users\User;

class UserAuthenticator implements IAuthenticator
{
    use SmartObject;
    
    
    const CACHE_NAMESPACE = 'users.authentication';

    /** @var IRequest */
    private $httpRequest;

    /** @var EntityManager  */
    private $entityManager;

    /** @var IRoleRestricter */
    private $roleRestricter;


    public function __construct(
        EntityManager $entityManager,
        IRequest $httpRequest
    ) {
        $this->httpRequest = $httpRequest;
        $this->entityManager = $entityManager;
    }


    /**
     * @param IRoleRestricter|null $restricter
     */
    public function setRoleRestricter(IRoleRestricter $restricter = null)
    {
        $this->roleRestricter = $restricter;
    }

    
    /**
     * Performs an authentication against e.g. database.
     * and returns IIdentity on success or throws AuthenticationException
     * @return IIdentity
     * @throws AuthenticationException
     */
    public function authenticate(array $credentials)
    {
        list($email, $password) = $credentials;

        $user = $this->getUser($email);

        if ($user === null) {
            throw new AuthenticationException('Wrong E-mail');
        }

        if (!Passwords::verify($password, $user->getPassword())) {
            throw new AuthenticationException('Wrong password');

        } elseif (Passwords::needsRehash($user->getPassword())) {
            $user->setPassword($password);
        }
        
        if (isset($this->roleRestricter)) {
            foreach ($user->getRoles() as $role) {
                if (!$this->roleRestricter->checkRole($role)) {
                    throw new AuthenticationException('Insufficient permissions');
                }
            }
            $this->roleRestricter = null; // intentionally
        }

        return new FakeIdentity($user->getId(), get_class($user));
    }


    /**
     * @param $email
     * @return User
     */
    private function getUser($email)
    {
        return $this->entityManager->createQuery(
            'SELECT u, role FROM ' . User::class . ' u
             JOIN u.role role
             WHERE u.email = :email'
        )->setParameter('email', $email)
         ->getOneOrNullResult();
    }
}