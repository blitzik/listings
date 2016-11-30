<?php

namespace Users\Authorization;

interface IRole extends \Nette\Security\IRole
{
    const MEMBER = 1;
    const ADMIN = 2;

    /**
     * Role owner ID
     * 
     * @return int
     */
    public function getOwnerId();


    /**
     * @return string
     */
    public function getName();
}