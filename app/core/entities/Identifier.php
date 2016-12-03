<?php

declare(strict_types = 1);

namespace App\Entities\Attributes;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;

trait Identifier
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=32, options={"fixed": true})
     * @var string
     */
    private $id;



    /**
     * @return string
     */
    final public function getId(): string
    {
        return $this->id;
    }


    /**
     * @return string
     */
    private function getUuid(): string
    {
        return $this->id = str_replace('-', '', Uuid::uuid4()->toString());
    }
    

    public function __clone()
    {
        $this->id = $this->getUuid();
    }

}
