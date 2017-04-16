<?php declare(strict_types=1);

namespace Listings;

use Listings\Exceptions\Runtime\WrongDayNumberException;
use Listings\Services\InvoiceTime;
use Nette\Utils\Validators;

trait TListingItem
{
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
     * @ORM\Column(name="worked_hours_in_seconds", type="integer", nullable=false, unique=false)
     * @var int
     */
    private $workedHoursInSeconds;


    /**
     * @return int
     */
    public function getListingItemsType(): int
    {
        return $this->listing->getItemsType();
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
    public function getWorkedHours(): InvoiceTime
    {
        return $this->workEnd->sub($this->workStart)->sub($this->getLunch());
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