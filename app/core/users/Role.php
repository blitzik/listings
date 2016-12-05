<?php

namespace Users\Authorization;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity
 * @ORM\Table(name="role")
 */
class Role
{
    use Identifier;

    const MEMBER = 'member';
    const ADMIN = 'admin';
    const GOD = 'god';


    const LENGTH_NAME = 255;


    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=false, unique=true)
     * @var string
     */
    private $name;

    /**
     * @ORM\OneToOne(targetEntity="Role", cascade={"persist"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true, unique=false, onDelete="SET NULL")
     */
    private $parent;


    public function __construct(
        $name,
        Role $parent = null
    ) {
        $this->setName($name);
        $this->parent = $parent;
    }


    /**
     * @param string $name
     */
    private function setName($name)
    {
        $this->name = $name;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * @return bool
     */
    public function hasParent()
    {
        return $this->parent !== null;
    }


    /*
     * --------------------------
     * ----- PARENT GETTERS -----
     * --------------------------
     */


    /**
     * @return Role
     */
    public function getParent()
    {
        return $this->parent;
    }

}