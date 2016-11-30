<?php

namespace Users\Authorization;
use Nette\Security\Permission;


/**
 * Class that implements this interface just adds
 * specific Roles, Resources and privileges to ACL
 * generated from database
 *
 * @package Users\Authorization
 */
interface IAuthorizationAssertion
{
    /**
     * Is this definition meant to be used in allowed privilege?
     *
     * @return bool
     */
    public function isForAllowed();


    /**
     * @return string
     */
    public function getResourceName();


    /**
     * @return string
     */
    public function getPrivilegeName();


    /**
     * @param Permission $acl
     * @param $role
     * @param $resource
     * @param $privilege
     * @return mixed
     */
    public function assert(Permission $acl, $role, $resource, $privilege);
}