<?php

namespace Listings\ACL;

use Users\Authorization\IAuthorizationAssertion;
use Users\Authorization\IResource;
use Users\Authorization\Privilege;
use Nette\Security\Permission;
use Users\Authorization\Role;
use Nette\Security\IRole;
use Listings\Listing;

final class ListingOwnerAuthorizationAssertion implements IAuthorizationAssertion
{
    /**
     * @return bool
     */
    public function isForAllowed()
    {
        return true;
    }


    /**
     * @return mixed
     */
    public function getResourceName()
    {
        return Listing::class;
    }


    /**
     * @return string
     */
    public function getPrivilegeNames()
    {
        return [Privilege::EDIT, Privilege::REMOVE, Privilege::VIEW];
    }


    /**
     * @param Permission $acl
     * @param $role
     * @param $resource
     * @param $privilege
     * @return bool
     * @throws \Exception
     */
    public function assert(Permission $acl, $role, $resource, $privilege)
    {
        if (!$acl->getQueriedRole() instanceof IRole)  {
            throw new \Exception('The Role\'s owner has to implement IRole');
        }

        if ($acl->getQueriedRole()->getRoleId() === Role::ADMIN) {
            return true;
        }

        if ($acl->getQueriedResource() instanceof IResource) {
            return $acl->getQueriedResource()->getOwnerId() === $acl->getQueriedRole()->getId();
        }

        return true;
    }

}