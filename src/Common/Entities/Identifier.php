<?php

declare(strict_types=1);

namespace Common\Entities\Attributes;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;

trait Identifier
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid_binary")
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
     * @return UuidInterface
     */
    public function getUuid(): UuidInterface
    {
        return Uuid::fromString($this->id);
    }


    /**
     * @return string
     */
    private function generateUuid(): string
    {
        return str_replace('-', '', Uuid::uuid4()->toString());
    }
    

    public function __clone()
    {
        $this->id = $this->generateUuid();
    }

}
