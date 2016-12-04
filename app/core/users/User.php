<?php

namespace Users;

use App\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Nette\Security\IIdentity;
use Users\Authorization\Role;
use Nette\Security\Passwords;
use Nette\Utils\Validators;
use Nette\Security\IRole;
use Nette\Utils\Random;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User implements IIdentity, IRole
{
    use Identifier;


    const LENGTH_FIRSTNAME = 50;
    const LENGTH_LASTNAME = 50;
    const LENGTH_EMAIL = 100;


    /**
     * @ORM\Column(name="first_name", type="string", length=50, nullable=false, unique=false)
     * @var string
     */
    private $firstName;

    /**
     * @ORM\Column(name="last_name", type="string", length=50, nullable=false, unique=false)
     * @var string
     */
    private $lastName;
    
    /**
     * @ORM\Column(name="email", type="string", length=100, nullable=false, unique=true)
     * @var string
     */
    private $email;
    
    /**
     * @ORM\Column(name="password", type="string", length=60, nullable=false, unique=false, options={"fixed": true})
     * @var string
     */
    private $password;

    /**
     * @ORM\Column(name="token", type="string", length=32, nullable=true, unique=false, options={"fixed": true})
     * @var string
     */
    private $token;

    /**
     * @ORM\Column(name="token_validity", type="datetime_immutable", nullable=true, unique=false)
     * @var \DateTimeImmutable
     */
    private $tokenValidity;

    /**
     * @ORM\Column(name="is_closed", type="boolean", nullable=false, unique=false)
     * @var bool
     */
    private $isClosed;

    /**
     * @ORM\ManyToOne(targetEntity="\Users\Authorization\Role")
     * @ORM\JoinColumn(name="role", referencedColumnName="id", nullable=false)
     * @var Authorization\Role
     */
    private $role;


    public function __construct(
        $firstName,
        $lastName,
        $email,
        $plainPassword,
        Role $role
    ) {
        $this->id = $this->getUuid();
        
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setEmail($email);
        $this->setPassword($plainPassword);
        $this->isClosed = false;

        $this->role = $role;
    }


    public function deactivate()
    {
        $this->isClosed = true;
    }


    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        Validators::assert($email, 'email');
        Validators::assert($email, sprintf('unicode:1..%s', self::LENGTH_EMAIL));
        $this->email = $email;
    }


    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }


    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        Validators::assert($firstName, sprintf('unicode:1..%s', self::LENGTH_FIRSTNAME));
        $this->firstName = $firstName;
    }


    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }


    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        Validators::assert($lastName, sprintf('unicode:1..%s', self::LENGTH_LASTNAME));
        $this->lastName = $lastName;
    }


    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }


    /**
     * @param string $plainPassword
     */
    public function setPassword($plainPassword)
    {
        $this->password = Passwords::hash($plainPassword);
    }


    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }


    public function createToken(\DateTime $validity)
    {
        $this->token = Random::generate(32);
        $this->tokenValidity = $validity;
    }


    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
    

    /**
     * @return \DateTimeImmutable
     */
    public function getTokenValidity()
    {
        return $this->tokenValidity;
    }


    /**
     * @return string
     */
    public function getFullName()
    {
        return sprintf('%s %s', $this->firstName, $this->lastName);
    }


    // ----- IIdentity


    /**
     * @return IRole[]
     */
    public function getRoles()
    {
        return [$this->role];
    }


    // ----- IRole


    function getRoleId()
    {
        return $this->role->getName();
    }


}