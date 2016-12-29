<?php

namespace Users\Authorization;

use Doctrine\ORM\Mapping\UniqueConstraint;
use App\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="access_definition",
 *     uniqueConstraints={@UniqueConstraint(name="resource_privilege", columns={"resource", "privilege"})}
 * )
 */
class AccessDefinition
{
    use Identifier;

    /**
     * @ORM\ManyToOne(targetEntity="Resource", cascade={"persist"})
     * @ORM\JoinColumn(name="resource", referencedColumnName="id", nullable=false)
     * @var Resource
     */
    private $resource;

    /**
     * @ORM\ManyToOne(targetEntity="Privilege", cascade={"persist"})
     * @ORM\JoinColumn(name="privilege", referencedColumnName="id", nullable=false)
     * @var Privilege
     */
    private $privilege;


    public function __construct(
        \Users\Authorization\Resource $resource,
        Privilege $privilege
    ) {
        $this->id = $this->generateUuid();

        $this->resource = $resource;
        $this->privilege = $privilege;
    }


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