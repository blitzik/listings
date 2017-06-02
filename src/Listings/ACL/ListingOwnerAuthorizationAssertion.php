<?php declare(strict_types=1);

namespace Listings\ACL;

use blitzik\Authorization\Authorizator\IAuthorizationAssertion;
use blitzik\Authorization\Authorizator\IResource;
use blitzik\Authorization\Privilege;
use blitzik\Authorization\Role;
use Nette\Security\Permission;
use Nette\Security\IRole;
use Listings\Listing;

final class ListingOwnerAuthorizationAssertion implements IAuthorizationAssertion
{
    public function isForAllowed(): bool
    {
        return true;
    }


    public function getResourceName(): string
    {
        return Listing::class;
    }


    public function getPrivilegeNames(): array
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
    public function assert(Permission $acl, $role, $resource, $privilege): bool
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