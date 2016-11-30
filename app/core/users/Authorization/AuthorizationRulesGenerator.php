<?php

namespace Users\Authorization;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Kdyby\Doctrine\EntityManager;
use Nette\SmartObject;

/**
 * @package Users\Authorization
 */
class AuthorizationRulesGenerator
{
    use SmartObject;

    /** @var AbstractFixture */
    private $fixture;
    
    /** @var EntityManager */
    private $em;

    /** @var \Users\Authorization\Resource */
    private $resource;

    
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }


    /**
     * @param AbstractFixture $fixture
     */
    public function setFixture(AbstractFixture $fixture)
    {
        $this->fixture = $fixture;
    }


    /**
     * @param \Users\Authorization\Resource $resource
     * @param string|null $fixtureObjectReferenceName
     * @return $this
     */
    public function addResource(\Users\Authorization\Resource $resource, $fixtureObjectReferenceName = null)
    {
        $this->em->persist($resource);
        $this->resource = $resource;

        if ($fixtureObjectReferenceName !== null and $this->fixture !== null) {
            $this->fixture->addReference($fixtureObjectReferenceName, $resource);
        }

        return $this;
    }


    /**
     * @param \Users\Authorization\Resource $resource
     * @return $this
     */
    public function updateResource(\Users\Authorization\Resource $resource)
    {
       $this->resource = $resource;
        return $this;
    }


    /**
     * @param Privilege $privilege
     * @param Role $role
     * @return $this
     */
    public function addDefinition(Privilege $privilege, Role $role)
    {
        $accessDefinition = new AccessDefinition($this->resource, $privilege);
        $this->em->persist($accessDefinition);

        $permission = new Permission($role, $this->resource, $privilege);
        $this->em->persist($permission);

        return $this;
    }
}