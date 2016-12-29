<?php

namespace Users\Authorization;

use App\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity
 * @ORM\Table(name="permission")
 *
 */
class Permission
{
    use Identifier;

    
    /**
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumn(name="role", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var Role
     */
    private $role;

    /**
     * @ORM\ManyToOne(targetEntity="Resource")
     * @ORM\JoinColumn(name="resource", referencedColumnName="id", nullable=false)
     * @var Resource
     */
    private $resource;

    /**
     * @ORM\ManyToOne(targetEntity="Privilege")
     * @ORM\JoinColumn(name="privilege", referencedColumnName="id", nullable=false)
     * @var Privilege
     */
    private $privilege;

    /**
     * @ORM\Column(name="is_allowed", type="boolean", nullable=false, unique=false, options={"default":true})
     * @var bool
     */
    private $isAllowed;


    public function __construct(
        Role $role,
        \Users\Authorization\Resource $resource,
        Privilege $privilege,
        $isAllowed = true
    ) {
        $this->id = $this->generateUuid();
        
        $this->role = $role;
        $this->resource = $resource;
        $this->privilege = $privilege;
        $this->isAllowed = (bool)$isAllowed;
    }


    /**
     * @return bool
     */
    public function isAllowed()
    {
        return $this->isAllowed;
    }


    /*
     * ------------------------
     * ----- ROLE GETTERS -----
     * ------------------------
     */

    
    /**
     * @return string
     */
    public function getRoleName()
    {
        return $this->role->getName();
    }
    

    /*
     * ----------------------------
     * ----- RESOURCE GETTERS -----
     * ----------------------------
     */


    /**
     * @return string
     */
    public function getResourceId()
    {
        return $this->resource->getId();
    }


    /**
     * @return string
     */
    public function getResourceName()
    {
        return $this->resource->getName();
    }


    /*
     * -----------------------------
     * ----- PRIVILEGE GETTERS -----
     * -----------------------------
     */


    /**
     * @return string
     */
    public function getPrivilegeId()
    {
        return $this->privilege->getId();
    }


    /**
     * @return string
     */
    public function getPrivilegeName()
    {
        return $this->privilege->getName();
    }

}