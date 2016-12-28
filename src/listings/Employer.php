<?php

declare(strict_types=1);

namespace Listings;

use App\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Nette\Utils\Validators;
use Users\User;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="employer",
 *     indexes={
 *         @Index(name="user_created_at", columns={"user", "created_at"})
 *     }
 * )
 */
class Employer
{
    use Identifier;


    const LENGTH_NAME = 255;


    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=false, unique=false)
     * @var string
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="\Users\User")
     * @ORM\JoinColumn(name="user", referencedColumnName="id", nullable=false)
     * @var User
     */
    private $user;

    /**
     * @ORM\Column(name="created_at", type="datetime_immutable", nullable=false, unique=false)
     * @var \DateTimeImmutable
     */
    private $createdAt;


    public function __construct(
        string $name,
        User $user
    ) {
        $this->id = $this->getUuid();

        $this->setName($name);
        $this->user = $user;
        $this->createdAt = new \DateTimeImmutable;
    }


    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        Validators::assert($name, sprintf('unicode:1..%s', self::LENGTH_NAME));
        $this->name = $name;
    }


    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }


    /**
     * @return \DateTimeImmutable
     */
    public function getCreationTime(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}