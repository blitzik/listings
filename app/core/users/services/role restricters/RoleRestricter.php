<?php

namespace Users\Services;

use Users\Services\RoleRestricters\IRoleRestricter;
use Users\Authorization\Authorizator;
use Users\Authorization\IRole;
use Nette\SmartObject;
use Users\User;

class RoleRestricter implements IRoleRestricter
{
    use SmartObject;
    
    
    /** @var Authorizator */
    private $authorizator;


    public function __construct(Authorizator $authorizator)
    {
        $this->authorizator = $authorizator;
    }


    /**
     * @param User|\Nette\Security\User|IRole|string $role
     * @return bool
     */
    public function checkRole($role)
    {
        return $this->authorizator->isAllowed($role, 'operation_module', 'view') or
               $this->authorizator->isAllowed($role, 'operation_manager_module', 'view');
    }

}