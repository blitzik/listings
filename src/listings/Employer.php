<?php

declare(strict_types = 1);

namespace Listings;

use App\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Nette\Utils\Validators;
use Users\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="employer")
 *
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


    public function __construct(
        $name,
        User $user
    ) {
        $this->setName($name);
        $this->user = $user;
    }


    /**
     * @param $name
     */
    private function setName($name)
    {
        Validators::assert($name, sprintf('unicode:1..%s', self::LENGTH_NAME));
        $this->name = $name;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}