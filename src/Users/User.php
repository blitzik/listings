<?php declare(strict_types=1);

namespace Users;

use Common\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use blitzik\Authorization\Role;
use Nette\Security\IIdentity;
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
    const LENGHT_TOKEN = 32;


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
     * @ORM\ManyToOne(targetEntity="blitzik\Authorization\Role")
     * @ORM\JoinColumn(name="role", referencedColumnName="id", nullable=false)
     * @var Role
     */
    private $role;


    public function __construct(
        $firstName,
        $lastName,
        $email,
        $plainPassword,
        Role $role
    ) {
        $this->id = $this->generateUuid();
        
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setEmail($email);
        $this->changePassword($plainPassword);
        $this->isClosed = false;

        $this->role = $role;
    }


    public function deactivate(): void
    {
        $this->isClosed = true;
    }


    public function setEmail(string $email): void
    {
        Validators::assert($email, 'email');
        Validators::assert($email, sprintf('unicode:1..%s', self::LENGTH_EMAIL));
        $this->email = $email;
    }


    public function getEmail(): string
    {
        return $this->email;
    }


    public function setFirstName(string $firstName): void
    {
        Validators::assert($firstName, sprintf('unicode:1..%s', self::LENGTH_FIRSTNAME));
        $this->firstName = $firstName;
    }


    public function getFirstName(): string
    {
        return $this->firstName;
    }


    public function setLastName(string $lastName): void
    {
        Validators::assert($lastName, sprintf('unicode:1..%s', self::LENGTH_LASTNAME));
        $this->lastName = $lastName;
    }


    public function getLastName(): string
    {
        return $this->lastName;
    }


    public function changePassword(string $plainPassword): void
    {
        $this->password = Passwords::hash($plainPassword);
        $this->token = null;
        $this->tokenValidity = null;
    }


    public function getPassword(): string
    {
        return $this->password;
    }


    public function createToken(\DateTime $validity): string
    {
        $this->token = Random::generate(self::LENGHT_TOKEN);
        $this->tokenValidity = $validity;

        return $this->token;
    }


    public function getToken(): string
    {
        return $this->token;
    }
    

    public function getTokenValidity(): \DateTimeImmutable
    {
        return $this->tokenValidity;
    }

    public function getFullName(): string
    {
        return sprintf('%s %s', $this->firstName, $this->lastName);
    }


    // ----- IIdentity


    /**
     * @return IRole[]
     */
    public function getRoles(): array
    {
        return [$this->role];
    }


    // ----- IRole


    function getRoleId(): string
    {
        return $this->role->getName();
    }


}