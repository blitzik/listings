<?php

namespace App\Entities\Attributes;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;

trait Identifier
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=32, options={"fixed": true})
     * @var UuidInterface
     */
    private $id;



    /**
     * @return string
     */
    final public function getId()
    {
        return $this->id;
    }


    /**
     * @return string
     */
    private function getUuid()
    {
        return $this->id = str_replace('-', '', Uuid::uuid4()->toString());
    }
    

    public function __clone()
    {
        $this->id = $this->getUuid();
    }

}
