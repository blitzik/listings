<?php

namespace Users\Authorization;

use Nette\Security\IAuthorizator;
use Kdyby\Doctrine\EntityManager;
use Nette\InvalidStateException;
use Nette\Security\Permission;
use Nette\Security\IResource;
use Nette\Caching\IStorage;
use Nette\Utils\Validators;
use Nette\Caching\Cache;
use Nette\SmartObject;
use Users\User;

class Authorizator implements IAuthorizator
{
    use SmartObject;
    
    
    const CACHE_NAMESPACE = 'users.authorization';

    /** @var Cache */
    private $cache;

    /** @var Permission */
    private $acl;

    /** @var EntityManager */
    private $em;

    /** @var AuthorizationAssertionsCollection|null */
    private $assertionsCollection;


    public function __construct(
        AuthorizationAssertionsCollection $assertionsCollection = null,
        EntityManager $entityManager,
        IStorage $storage
    ) {
        $this->assertionsCollection = $assertionsCollection;
        $this->em = $entityManager;
        $this->cache = new Cache($storage, self::CACHE_NAMESPACE);
        $this->acl = $this->loadACL();
    }
    

    /**
     * @param \Nette\Security\User|\Users\User|IRole|string $role
     * @param IResource|string $resource
     * @param string $privilege
     * @return bool
     */
    public function isAllowed($role, $resource, $privilege)
    {
        $roles = $this->resolveRoles($role);

        try {
            foreach ($roles as $userRole) {
                if ($this->acl->isAllowed($userRole, $resource, $privilege) === true) {
                    return true;
                }
            }

            return false;

        } catch (InvalidStateException $e) {
            return false; // role does not exists
        }
    }


    /**
     * @param User|\Nette\Security\User|IRole|string $role
     * @return array
     */
    private function resolveRoles($role)
    {
        $roles = [];
        if (Validators::is($role, 'unicode')) {
            $roles[] = $role;

        } elseif ($role instanceof IRole) {
            $roles[] = $role->getName();

        } else {
            $userRoles = [];
            if ($role instanceof User) {
                $userRoles = $role->getRoles();

            } elseif ($role instanceof \Nette\Security\User) {
                $userIdentity = $role->getIdentity();
                if ($userIdentity !== null) {
                    $userRoles = $role->getIdentity()->getRoles();
                }

            } else {
                throw new \InvalidArgumentException;
            }

            foreach ($userRoles as $userRole) {
                if (Validators::is($userRole, 'unicode')) {
                    $roles[] = $userRole;
                } else {
                    $roles[] = $userRole->getName();
                }
            }
        }

        return $roles;
    }


    private function loadACL()
    {
        return $this->cache->load('acl', function () {
            return $this->createACL();
        });
    }


    private function createACL()
    {
        $acl = new Permission();

        $this->loadRoles($acl);
        $this->loadResources($acl);
        $this->loadPermissions($acl);

        return $acl;
    }


    private function loadRoles(Permission $acl)
    {
        $roles = $this->em->createQuery(
            'SELECT r, parent FROM ' . Role::class . ' r
             LEFT JOIN r.parent parent
             ORDER BY r.parent ASC'
        )->execute();

        /** @var Role $role */
        foreach ($roles as $role) {
            $acl->addRole($role->getName(), $role->hasParent() ? $role->getParent()->getName() : null);
        }

        $acl->addRole(Role::GOD);
    }


    private function loadResources(Permission $acl)
    {
        $resources = $this->em->createQuery(
            'SELECT r FROM ' . Resource::class . ' r'
        )->execute();

        /** @var Resource $resource */
        foreach ($resources as $resource) {
            $acl->addResource($resource->getName());
        }
    }


    private function loadPermissions(Permission $acl)
    {
        $permissions = $this->em->createQuery(
            'SELECT p, pr FROM ' . \Users\Authorization\Permission::class . ' p
             LEFT JOIN p.privilege pr'
        )->execute();

        /** @var \Users\Authorization\Permission $permission */
        foreach ($permissions as $permission) {
            if ($permission->isAllowed() === true) {
                $assertion = $this->assertionsCollection->getAssertionsForAllowed($permission->getResourceName(), $permission->getPrivilegeName());
                $acl->allow($permission->getRoleName(), $permission->getResourceName(), $permission->getPrivilegeName(), ($assertion !== null ? [$assertion, 'assert'] : null));
            } else {
                $assertion = $this->assertionsCollection->getAssertionsForDenied($permission->getResourceName(), $permission->getPrivilegeName());
                $acl->deny($permission->getRoleName(), $permission->getResourceName(), $permission->getPrivilegeName(), ($assertion !== null ? [$assertion, 'assert'] : null));
            }
        }

        $acl->allow(Role::GOD, IAuthorizator::ALL, IAuthorizator::ALL);
    }

}