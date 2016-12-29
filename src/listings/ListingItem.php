<?php

declare(strict_types=1);

namespace Listings;

use Listings\Exceptions\Runtime\NegativeWorkedTimeException;
use Listings\Exceptions\Runtime\WorkedHoursRangeException;
use Listings\Exceptions\Runtime\WrongDayNumberException;
use Doctrine\ORM\Mapping\UniqueConstraint;
use App\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping\JoinColumn;
use Listings\Services\InvoiceTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Nette\Utils\Validators;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="listing_item",
 *     uniqueConstraints={
 *         @UniqueConstraint(name="listing_day", columns={"listing", "day"})
 *     }
 * )
 */
class ListingItem
{
    use Identifier;


    /**
     * @ORM\ManyToOne(targetEntity="Listing")
     * @ORM\JoinColumn(name="listing", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var Listing
     */
    private $listing;

    /**
     * @ORM\Column(name="`day`", type="smallint", nullable=false, unique=false)
     * @var int
     */
    private $day;
    
    /**
     * @ORM\Column(name="locality", type="string", length=70, nullable=false, unique=false)
     * @var string
     */
    private $locality;

    /**
     * @ORM\Column(name="work_start", type="invoice_time", nullable=false, unique=false)
     * @var InvoiceTime
     */
    private $workStart;

    /**
     * @ORM\Column(name="work_end", type="invoice_time", nullable=false, unique=false)
     * @var InvoiceTime
     */
    private $workEnd;

    /**
     * @ORM\Column(name="lunch", type="invoice_time", nullable=false, unique=false)
     * @var InvoiceTime
     */
    private $lunch;

    /**
     * @ORM\Column(name="worked_hours_in_seconds", type="integer", nullable=false, unique=false)
     * @var int
     */
    private $workedHoursInSeconds;


    /**
     * @param Listing $listing
     * @param int $day
     * @param string $locality
     * @param \DateTimeInterface|int|InvoiceTime|null|string $workStart
     * @param \DateTimeInterface|int|InvoiceTime|null|string $workEnd
     * @param \DateTimeInterface|int|InvoiceTime|null|string $lunch
     * @throws WorkedHoursRangeException
     * @throws NegativeWorkedTimeException
     */
    public function __construct(
        Listing $listing,
        int $day,
        string $locality,
        $workStart,
        $workEnd,
        $lunch
    ) {
        $this->id = $this->generateUuid();

        $this->listing = $listing;

        $this->workStart = new InvoiceTime;
        $this->workEnd = new InvoiceTime;
        $this->lunch = new InvoiceTime;
        $this->workedHoursInSeconds = 0;

        $this->setDay($day);
        $this->changeLocality($locality);
        $this->changeHours($workStart, $workEnd, $lunch);
    }


    /**
     * @param string $locality
     */
    public function changeLocality(string $locality)
    {
        Validators::assert($locality, 'unicode:..70');
        $this->locality = $locality;
    }


    /**
     * @param \DateTimeInterface|int|InvoiceTime|null|string $workStart
     * @param \DateTimeInterface|int|InvoiceTime|null|string $workEnd
     * @param \DateTimeInterface|int|InvoiceTime|null|string $lunch
     * @throws WorkedHoursRangeException
     * @throws NegativeWorkedTimeException
     */
    public function changeHours($workStart, $workEnd, $lunch)
    {
        $this->workStart = new InvoiceTime($workStart);
        $this->workEnd = new InvoiceTime($workEnd);
        if ($this->workStart->compare($this->workEnd) === 1) {
            throw new WorkedHoursRangeException;
        }

        $this->lunch = new InvoiceTime($lunch);
        $workedHoursWithLunch = $this->workEnd->sub($this->workStart);
        if ($workedHoursWithLunch->compare($this->lunch) < 0) { // must be $workedHoursWithLunch >= $_lunch
            throw new NegativeWorkedTimeException;
        }

        $this->workedHoursInSeconds = (int)$this->getWorkedHours()->toSeconds();
    }


    /**
     * @param int $day
     * @throws WrongDayNumberException
     */
    private function setDay(int $day)
    {
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $this->listing->getMonth(), $this->listing->getYear());
        if ($day < 1 or $day > $daysInMonth) {
            throw new WrongDayNumberException;
        }

        $this->day = $day;
    }


    /**
     * @return \DateTimeImmutable
     */
    public function getDate(): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat('!Y-m-d', sprintf('%s-%s-%s', $this->getYear(), $this->getMonth(), $this->getDay()));
    }


    /**
     * @return int
     */
    public function getDay(): int
    {
        return $this->day;
    }


    /**
     * @return string
     */
    public function getLocality(): string
    {
        return $this->locality;
    }


    /**
     * @return InvoiceTime
     */
    public function getWorkStart(): InvoiceTime
    {
        return $this->workStart;
    }


    /**
     * @return InvoiceTime
     */
    public function getWorkEnd(): InvoiceTime
    {
        return $this->workEnd;
    }


    /**
     * @return InvoiceTime
     */
    public function getLunch(): InvoiceTime
    {
        return $this->lunch;
    }


    /**
     * @return InvoiceTime
     */
    public function getWorkedHours(): InvoiceTime
    {
        return $this->workEnd->sub($this->workStart)->sub($this->lunch);
    }


    /**
     * @return InvoiceTime
     */
    public function getWorkedHoursWithLunch(): InvoiceTime
    {
        return $this->workEnd->sub($this->workStart);
    }


    /*
     * ---------------------------
     * ----- LISTING GETTERS -----
     * ---------------------------
     */


    /**
     * @return string
     */
    public function getListingId(): string
    {
        return $this->listing->getId();
    }


    /**
     * @return int
     */
    public function getMonth(): int
    {
        return $this->listing->getMonth();
    }


    /**
     * @return int
     */
    public function getYear(): int
    {
        return $this->listing->getYear();
    }
}