<?php

namespace Users\Authorization;

interface IResource extends \Nette\Security\IResource
{
    /**
     * @return int
     */
    public function getOwnerId();
}