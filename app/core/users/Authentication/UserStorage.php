<?php

namespace Users\Authentication;

use Kdyby\Doctrine\EntityManager;
use Nette\Security\IIdentity;
use Nette\Caching\IStorage;
use Nette\Caching\Cache;
use Nette\Http\Session;

class UserStorage extends \Nette\Http\UserStorage
{
    /** @var EntityManager */
    private $entityManager;

    /** @var Cache */
    private $cache;


    public function  __construct(
        Session $sessionHandler,
        EntityManager $entityManager,
        IStorage $storage
    ) {
        parent::__construct($sessionHandler);

        $this->entityManager = $entityManager;
        $this->cache = new Cache($storage, UserAuthenticator::CACHE_NAMESPACE);
    }

    
    /**
     * Sets the user identity.
     * @return UserStorage Provides a fluent interface
     */
    public function setIdentity(IIdentity $identity = null)
    {
        if ($identity !== NULL) {
            $class = get_class($identity);
            // we want to convert identity entities into fake identity
            // so only the identifier fields are stored,
            // but we are only interested in identities which are correctly
            // mapped as doctrine entities
            if ($this->entityManager->getMetadataFactory()->hasMetadataFor($class)) {
                $cm = $this->entityManager->getClassMetadata($class);
                $identifier = $cm->getIdentifierValues($identity);
                $identity = new FakeIdentity($identifier, $class);
            }
        }
        return parent::setIdentity($identity);
    }
    

    /**
     * Returns current user identity, if any.
     * @return IIdentity|NULL
     */
    public function getIdentity()
    {
        $identity = parent::getIdentity();
        // if we have our fake identity, we now want to
        // convert it back into the real entity
        if ($identity instanceof FakeIdentity) {
            return $this->cache->load(sprintf('user-%s', $identity->getId()), function () use ($identity) {
                return $this->entityManager->createQuery(
                    'SELECT u, role FROM ' . $identity->getClass() . ' u
                     JOIN u.role role
                     WHERE u.id = :id'
                )->setParameter('id', hex2bin($identity->getId()))
                 ->getOneOrNullResult();
            });

            //return $this->entityManager->getReference($identity->getClass(), $identity->getId());
        }
        return $identity;
    }
}