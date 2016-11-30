<?php

namespace Users\Authorization;

use Nette\SmartObject;

class AuthorizationAssertionsCollection
{
    use SmartObject;
    
    
    /** @var  IAuthorizationAssertion[] */
    private $definitions = [];


    public function addAssertion(IAuthorizationAssertion $assertion)
    {
        $this->definitions[$assertion->getResourceName()]
                          [(bool)$assertion->isForAllowed()]
                          [$assertion->getPrivilegeName()] = $assertion;
    }


    /**
     * @param $resource
     * @param $privilege
     * @return IAuthorizationAssertion|null
     */
    public function getAssertionsForAllowed($resource, $privilege)
    {
        if (isset($this->definitions[$resource][true][$privilege])) {
            return $this->definitions[$resource][true][$privilege];
        }

        return null;
    }


    /**
     * @param $resource
     * @param $privilege
     * @return IAuthorizationAssertion|null
     */
    public function getAssertionsForDenied($resource, $privilege)
    {
        if (isset($this->definitions[$resource][false][$privilege])) {
            return $this->definitions[$resource][false][$privilege];
        }

        return null;
    }
}