<?php

namespace Users\Authorization;

use App\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Nette\Utils\Validators;

/**
 * @ORM\Entity
 * @ORM\Table(name="privilege")
 *
 */
class Privilege
{
    use Identifier;

    const CREATE = 'create';
    const EDIT = 'edit';
    const REMOVE = 'remove';
    const VIEW = 'view';


    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=false, unique=true)
     * @var string
     */
    private $name;


    public function __construct(
        $name
    ) {
        $this->id = $this->generateUuid();
        
        $this->setName($name);
    }


    /**
     * @param string $name
     */
    private function setName($name)
    {
        Validators::assert($name, 'unicode:1..255');
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