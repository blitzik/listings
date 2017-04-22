<?php declare(strict_types=1);

namespace Listings\Pdf;

use Listings\Services\Time;
use Listings\IListingItem;
use Listings\ListingItem;
use Nette\SmartObject;

class ListingItemPdfDTO
{
    use SmartObject;


    /** @var int */
    protected $month;

    /** @var int */
    protected $year;

    /** @var int */
    protected $day;


    /** @var ListingItem|null */
    protected $listingItem;


    /** @var Time */
    protected $workedHoursWithLunch;

    /** @var Time */
    protected $workedHours;

    /** @var Time */
    protected $workStart;

    /** @var string */
    protected $locality;

    /** @var Time */
    protected $workEnd;

    /** @var Time */
    protected $lunch;


    public function __construct(int $year, int $month, int $day)
    {
        $this->month = $month;
        $this->year = $year;
        $this->day = $day;
    }


    /**
     * @param IListingItem $listingItem
     */
    public function fillByListingItem(IListingItem $listingItem)
    {
        $this->listingItem = $listingItem;

        $this->month = $listingItem->getMonth();
        $this->year = $listingItem->getYear();
        $this->day = $listingItem->getDay();

        $this->locality = $listingItem->getLocality();
        $this->workStart = $listingItem->getWorkStart();
        $this->workEnd = $listingItem->getWorkEnd();
        $this->lunch = $listingItem->getLunch();
        $this->workedHours = $listingItem->getWorkedHours();
        $this->workedHoursWithLunch = $listingItem->getWorkedHoursWithLunch();
    }


    /**
     * @return \DateTimeImmutable
     */
    public function getDate(): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat('!Y-m-d', sprintf('%s-%s-%s', $this->year, $this->month, $this->day));
    }


    /**
     * @return bool
     */
    public function isCurrentDay(): bool
    {
        $currentDate = \DateTimeImmutable::createFromFormat('!Y-m-d', date('Y-m-d'));
        return $this->getDate() == $currentDate;
    }


    /**
     * @return bool
     */
    public function isWeekEnd(): bool
    {
        return $this->getDate()->format('N') > 5;
    }


    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->listingItem === null;
    }


    /**
     * @return int
     */
    public function getMonth(): int
    {
        return $this->month;
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
    public function getDay(): int
    {
        return $this->day;
    }


    /**
     * @return Time
     */
    public function getWorkedHoursWithLunch(): Time
    {
        return $this->workedHoursWithLunch;
    }


    /**
     * @return Time
     */
    public function getWorkedHours(): Time
    {
        return $this->workedHours;
    }


    /**
     * @return Time
     */
    public function getWorkStart(): Time
    {
        return $this->workStart;
    }


    /**
     * @return string
     */
    public function getLocality(): string
    {
        return $this->locality;
    }


    /**
     * @return Time
     */
    public function getWorkEnd(): Time
    {
        return $this->workEnd;
    }


    /**
     * @return Time
     */
    public function getLunch(): Time
    {
        return $this->lunch;
    }


}