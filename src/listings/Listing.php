<?php

declare(strict_types = 1);

namespace Listings;

use Listings\Exceptions\Runtime\WrongMonthNumberException;
use App\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Nette\Utils\Validators;
use Users\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="listing")
 *
 */
class Listing
{
    use Identifier;


    /**
     * @ORM\ManyToOne(targetEntity="\Users\User")
     * @ORM\JoinColumn(name="owner", referencedColumnName="id", nullable=false)
     * @var \Users\User
     */
    private $owner;
    
    /**
     * @ORM\Column(name="name", type="string", length=50, nullable=true, unique=false)
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(name="year", type="smallint", nullable=false, unique=false)
     * @var int
     */
    private $year;

    /**
     * @ORM\Column(name="month", type="smallint", nullable=false, unique=false)
     * @var int
     */
    private $month;
    
    /**
     * @ORM\Column(name="hourly_rate", type="smallint", nullable=true, unique=false)
     * @var int
     */
    private $hourlyRate;


    /**
     * @param User $owner
     * @param int $year
     * @param int $month
     * @throws WrongMonthNumberException
     */
    public function __construct(
        User $owner,
        int $year,
        int $month
    ) {
        $this->id = $this->getUuid();

        $this->owner = $owner;
        $this->year = $year;

        $this->checkMonth($month);
        $this->month = $month;
    }


    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        Validators::assert($name, 'unicode:..50');
        $this->name = $name;
    }


    public function removeName()
    {
        $this->name = null;
    }


    /**
     * @param int|null $hourlyRate
     */
    public function setHourlyRate(int $hourlyRate = null)
    {
        Validators::assert($hourlyRate, 'null|numericint:0..');
        $this->hourlyRate = $hourlyRate;
    }


    public function removeHourlyRate()
    {
        $this->hourlyRate = null;
    }


    /*
    * --------------------
    * ----- GETTERS ------
    * --------------------
    */


    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }


    /**
     * @return int
     */
    public function getMonth(): int
    {
        return $this->month;
    }


    /**
     * @return int|null
     */
    public function getHourlyRate()
    {
        return $this->hourlyRate;
    }


    /*
    * --------------------------
    * ----- OWNER GETTERS ------
    * --------------------------
    */


    /**
     * @return string
     */
    public function getOwnerId(): string
    {
        return $this->owner->getId();
    }


    /**
     * @param int $month
     * @throws WrongMonthNumberException
     */
    private function checkMonth($month)
    {
        if ($month < 1 or $month > 12) {
            throw new WrongMonthNumberException;
        }
    }
}