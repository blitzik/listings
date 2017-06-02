<?php declare(strict_types=1);

namespace Listings;

use Listings\Exceptions\Runtime\WrongDayNumberException;
use Listings\Utils\Time\ListingTime;
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
     * @ORM\Column(name="weekday", type="smallint", nullable=false, unique=false)
     * @var int
     */
    private $weekday;

    /**
     * @ORM\Column(name="locality", type="string", length=70, nullable=false, unique=false)
     * @var string
     */
    private $locality;

    /**
     * @ORM\Column(name="work_start", type="listing_time", nullable=false, unique=false)
     * @var ListingTime
     */
    private $workStart;

    /**
     * @ORM\Column(name="work_end", type="listing_time", nullable=false, unique=false)
     * @var ListingTime
     */
    private $workEnd;


    public function getListingItemsType(): int
    {
        return $this->listing->getItemsType();
    }


    public function changeLocality(string $locality): void
    {
        Validators::assert($locality, sprintf('unicode:..%s', IListingItem::LENGTH_LOCALITY));
        $this->locality = $locality;
    }


    /**
     * @param int $day
     * @throws WrongDayNumberException
     */
    private function setDay(int $day): void
    {
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $this->listing->getMonth(), $this->listing->getYear());
        if ($day < 1 or $day > $daysInMonth) {
            throw new WrongDayNumberException;
        }

        $this->day = $day;
        $this->weekday = (int)(new \DateTime(sprintf('%s-%s-%s', $this->listing->getYear(), $this->listing->getMonth(), $this->day)))->format('w');
    }


    public function getDate(): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat('!Y-m-d', sprintf('%s-%s-%s', $this->getYear(), $this->getMonth(), $this->getDay()));
    }


    public function getDay(): int
    {
        return $this->day;
    }


    /**
     * @return int 0 - sunday; 1 - monday, etc.
     */
    public function getWeekDay(): int
    {
        return $this->weekday;
    }


    public function getLocality(): string
    {
        return $this->locality;
    }


    public function getWorkStart(): ListingTime
    {
        return $this->workStart;
    }


    public function getWorkEnd(): ListingTime
    {
        return $this->workEnd;
    }


    public function getWorkedHours(): ListingTime
    {
        return $this->workEnd->sub($this->workStart)->sub($this->getLunch());
    }


    public function getWorkedHoursWithLunch(): ListingTime
    {
        return $this->workEnd->sub($this->workStart);
    }


    /*
     * ---------------------------
     * ----- LISTING GETTERS -----
     * ---------------------------
     */


    public function getListingId(): string
    {
        return $this->listing->getId();
    }


    public function getMonth(): int
    {
        return $this->listing->getMonth();
    }


    public function getYear(): int
    {
        return $this->listing->getYear();
    }


}