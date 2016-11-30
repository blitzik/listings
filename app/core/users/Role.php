<?php

namespace Users\Authorization;

use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Nette\Utils\Validators;

/**
 * @ORM\Entity
 * @ORM\Table(name="role")
 */
class Role implements IRole
{
    const GOD = 'god';
    const LENGTH_NAME = 255;



    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $id;

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

    /** @var int|null */
    private $ownerId;


    public function __construct(
        $identifier,
        $name,
        Role $parent = null
    ) {
        Validators::assert($identifier, 'numericint:1..');
        $this->id = $identifier;
        
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


    /*
     * ----------------------------
     * ----- I_ROLE INTERFACE -----
     * ----------------------------
     */


    /**
     * @return string
     */
    public function getRoleId()
    {
        return $this->getName();
    }


    /**
     * @param int $ownerId
     */
    public function setOwnerId($ownerId)
    {
        Validators::assert($ownerId, 'numericint');
        $this->ownerId = $ownerId;
    }


    /**
     * Role owner ID
     *
     * @return int|null
     */
    public function getOwnerId()
    {
        return $this->ownerId;
    }





    /**
     * @return int
     */
    final public function getId()
    {
        return $this->id;
    }



    public function __clone()
    {
        $this->id = NULL; // todo
    }

}