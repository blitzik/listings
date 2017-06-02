<?php declare(strict_types=1);

namespace Users\Authentication;

use Nette\Security\IIdentity;

class FakeIdentity implements IIdentity
{
    /** @var mixed */
    private $id;

    /** @var string */
    private $class;

    
    public function __construct($id, $class)
    {
        $this->id = $id;
        $this->class = $class;
    }

    
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


    public function getClass(): string
    {
        return $this->class;
    }


    public function getRoles(): array
    {
        return array();
    }

}