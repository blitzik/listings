<?php

namespace Users\Services\RoleRestricters;

use Users\Authorization\IRole;
use Users\User;

interface IRoleRestricter
{
    /**
     * @param User|\Nette\Security\User|IRole|string $role
     * @return bool
     */
    public function checkRole($role);
}