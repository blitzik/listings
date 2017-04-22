<?php declare(strict_types=1);

namespace Listings;

use blitzik\Utils\Time;
use Listings\Exceptions\Runtime\NegativeWorkedDaysException;
use Listings\Exceptions\Runtime\NegativeWorkedTimeException;
use Listings\Exceptions\Runtime\WrongMonthNumberException;
use Listings\Exceptions\Logic\InvalidArgumentException;
use blitzik\Authorization\Authorizator\IResource;
use Common\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping\JoinColumn;
use Listings\Utils\Time\ListingTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Nette\Utils\Validators;
use Users\User;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="listing",
 *     indexes={
 *         @Index(name="owner_year_month_created_at", columns={"owner", "year", "month", "created_at"})
 *     }
 * )
 */
class Listing implements IResource
{
    use Identifier;


    const LENGTH_NAME = 50;

    const ITEM_TYPE_LUNCH_SIMPLE = 1;
    const ITEM_TYPE_LUNCH_RANGE = 2;


    /**
     * @ORM\ManyToOne(targetEntity="\Users\User")
     * @ORM\JoinColumn(name="owner", referencedColumnName="id", nullable=false)
     * @var \Users\User
     */
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity="Employer")
     * @ORM\JoinColumn(name="employer", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * @var Employer
     */
    private $employer;
    
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
     * @ORM\Column(name="created_at", type="datetime_immutable", nullable=false, unique=false)
     * @var \DateTimeImmutable
     */
    private $createdAt;

    /**
     * @ORM\Column(name="`type`", type="smallint", nullable=false, unique=false, options={"comment":"type of items"})
     * @var int
     */
    private $type;
    
    /**
     * @ORM\Column(name="worked_days", type="smallint", nullable=false, unique=false)
     * @var int
     */
    private $workedDays;

    /**
     * @ORM\Column(name="worked_hours", type="integer", nullable=false, unique=false, options={"comment":"in seconds"})
     * @var int
     */
    private $workedHours;


    // -----


    /** @var Time */
    private $currentWorkedHours;


    /**
     * @param User $owner
     * @param int $year
     * @param int $month
     * @param int $itemType
     * @throws WrongMonthNumberException
     */
    public function __construct(
        User $owner,
        int $year,
        int $month,
        int $itemType
    ) {
        $this->id = $this->generateUuid();

        $this->type = self::ITEM_TYPE_LUNCH_RANGE;

        $this->owner = $owner;
        $this->year = $year;

        $this->checkMonth($month);
        $this->month = $month;

        $this->setItemsType($itemType);

        $this->createdAt = new \DateTimeImmutable;
        $this->workedDays = 0;
        $this->workedHours = 0;
    }


    /**
     * @param Time $difference
     * @throws NegativeWorkedTimeException
     */
    public function updateWorkedHours(Time $difference)
    {
        $result = $this->getCurrentWorkedHours()->sum($difference);
        if ($result->getSeconds() < 0) {
            throw new NegativeWorkedTimeException;
        }

        $this->workedHours = (int)$result->getSeconds();
        $this->currentWorkedHours = $result;
    }


    private function getCurrentWorkedHours(): Time
    {
        if ($this->currentWorkedHours === null) {
            $this->currentWorkedHours = new Time($this->workedHours);
        }

        return $this->currentWorkedHours;
    }


    public function getWorkedHours(): ListingTime
    {
        return new ListingTime($this->workedHours);
    }


    public function getWorkedDays(): int
    {
        return $this->workedDays;
    }


    /**
     * @param int $days
     * @throws  NegativeWorkedDaysException
     */
    public function updateWorkedDays(int $days)
    {
        $result = $this->workedDays + $days;
        if ($result < 0) {
            throw new NegativeWorkedDaysException;
        }

        $this->workedDays = $result;
    }


    /**
     * @return array
     */
    public static function getTypes(): array
    {
        return [
            self::ITEM_TYPE_LUNCH_RANGE => 'Pole oběd - rozsah hodin',
            self::ITEM_TYPE_LUNCH_SIMPLE => 'Pole oběd - počet hodin',
        ];
    }


    /**
     * @param int $type
     */
    private function setItemsType(int $type)
    {
        if (!array_key_exists($type, [self::ITEM_TYPE_LUNCH_RANGE => null, self::ITEM_TYPE_LUNCH_SIMPLE => null])) {
            throw new InvalidArgumentException;
        }

        $this->type = $type;
    }


    /**
     * @return int
     */
    public function getItemsType()
    {
        return $this->type;
    }


    /**
     * @param Employer|null $employer
     */
    public function setEmployer(Employer $employer = null)
    {
        $this->employer = $employer;
    }


    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        Validators::assert($name, sprintf('unicode:..%s', self::LENGTH_NAME));
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
     * @return \DateTimeImmutable
     */
    public function getDate(): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat('!Y-m', sprintf('%s-%s', $this->year, $this->month));
    }


    /**
     * @return int|null
     */
    public function getHourlyRate()
    {
        return $this->hourlyRate;
    }


    /**
     * @return int
     */
    public function getNumberOfDaysInMonth(): int
    {
        return cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year);
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


    /*
     * ----------------------------
     * ----- EMPLOYER GETTERS -----
     * ----------------------------
     */


    /**
     * @return bool
     */
    public function hasSetEmployer(): bool
    {
        return $this->employer !== null;
    }


    /**
     * @return string|null
     */
    public function getEmployerId()
    {
        if ($this->hasSetEmployer()) {
            return $this->employer->getId();
        }

        return null;
    }


    /**
     * @return string|null
     */
    public function getEmployerName()
    {
        if ($this->hasSetEmployer()) {
            return $this->employer->getName();
        }

        return null;
    }


    /*
     * -------------------------
     * ----- OWNER GETTERS -----
     * -------------------------
     */


    /**
     * @return string
     */
    public function getOwnerFullName(): string
    {
        return sprintf('%s %s', $this->owner->getFirstName(), $this->owner->getLastName());
    }



    // ----- IResource


    function getResourceId(): string
    {
        return self::class;
    }


    /**
     * @param bool $convertToHex
     * @return string
     */
    public function getOwnerId(bool $convertToHex = false): string
    {
        return $this->owner->getId($convertToHex);
    }


}