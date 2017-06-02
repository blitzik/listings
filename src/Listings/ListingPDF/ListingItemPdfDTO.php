<?php declare(strict_types=1);

namespace Listings\Pdf;

use Listings\Utils\Time\ListingTime;
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


    /** @var ListingTime */
    protected $workedHoursWithLunch;

    /** @var ListingTime */
    protected $workedHours;

    /** @var ListingTime */
    protected $workStart;

    /** @var string */
    protected $locality;

    /** @var ListingTime */
    protected $workEnd;

    /** @var ListingTime */
    protected $lunch;


    public function __construct(int $year, int $month, int $day)
    {
        $this->month = $month;
        $this->year = $year;
        $this->day = $day;
    }


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


    public function getDate(): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat('!Y-m-d', sprintf('%s-%s-%s', $this->year, $this->month, $this->day));
    }


    public function isCurrentDay(): bool
    {
        $currentDate = \DateTimeImmutable::createFromFormat('!Y-m-d', date('Y-m-d'));
        return $this->getDate() == $currentDate;
    }


    public function isWeekEnd(): bool
    {
        return $this->getDate()->format('N') > 5;
    }


    public function isEmpty(): bool
    {
        return $this->listingItem === null;
    }


    public function getMonth(): int
    {
        return $this->month;
    }


    public function getYear(): int
    {
        return $this->year;
    }


    public function getDay(): int
    {
        return $this->day;
    }


    public function getWorkedHoursWithLunch(): ListingTime
    {
        return $this->workedHoursWithLunch;
    }


    public function getWorkedHours(): ListingTime
    {
        return $this->workedHours;
    }


    public function getWorkStart(): ListingTime
    {
        return $this->workStart;
    }


    public function getLocality(): string
    {
        return $this->locality;
    }


    public function getWorkEnd(): ListingTime
    {
        return $this->workEnd;
    }


    public function getLunch(): ListingTime
    {
        return $this->lunch;
    }


}