<?php

declare(strict_types=1);

namespace Listings\Pdf;

use Listings\Exceptions\Logic\InvalidArgumentException;
use Listings\Services\InvoiceTime;
use Listings\IListingItem;
use Nette\SmartObject;
use Listings\Listing;

class ListingPdfDTO
{
    use SmartObject;


    /** @var Listing|null */
    private $listing;


    /** @var int */
    private $year;

    /** @var int */
    private $month;

    /** @var string|null */
    private $employeeFullName;

    /** @var null|string */
    private $employerName;

    /** @var int|null */
    private $hourlyRate;

    /** @var IListingItem[] */
    private $listingItems = [];

    /** @var ListingItemPdfDTO[]|null */
    private $itemDTOs;

    /** @var int */
    private $workedDays;

    /** @var InvoiceTime */
    private $workedHours;


    // ----- settings


    /** @var bool */
    private $displayHourlyRate = false;

    /** @var int */
    private $type;


    public function __construct(int $year, int $month, int $type)
    {
        $this->year = $year;
        $this->month = $month;
        $this->workedDays = 0;
        $this->workedHours = new InvoiceTime();

        if (!array_key_exists($type, Listing::getTypes())) {
            throw new InvalidArgumentException;
        }

        $this->type = $type;
    }


    /**
     * @param Listing $listing
     * @param array $listingItems
     */
    public function fillByListing(Listing $listing, array $listingItems = [])
    {
        $this->workedDays = 0;
        $this->workedHours = new InvoiceTime();
        $this->itemDTOs = null;

        $this->listing = $listing;
        $this->year = $listing->getYear();
        $this->month = $listing->getMonth();
        $this->employeeFullName = $listing->getOwnerFullName();
        $this->employerName = $listing->getEmployerName();
        $this->hourlyRate = $listing->getHourlyRate();
        $this->type = $listing->getItemsType();

        foreach ($listingItems as $listingItem) {
            if (!$listingItem instanceof IListingItem or $listingItem->getListingId() !== $this->listing->getId()) {
                throw new InvalidArgumentException;
            }
            $this->listingItems[$listingItem->getDay()] = $listingItem;
            $this->workedHours = $this->workedHours->sum($listingItem->getWorkedHours());
            $this->workedDays++;
        }
    }


    /**
     * @param int $day
     * @return ListingItemPdfDTO|null
     */
    public function getItemByDay(int $day)
    {
        if ($this->itemDTOs === null) {
            for ($d = 1; $d <= $this->getDaysInMonth(); $d++) {
                if (isset($this->listingItems[$d])) {
                    if ($this->type === Listing::ITEM_TYPE_LUNCH_SIMPLE) {
                        $this->itemDTOs[$d] = new ListingItemPdfDTO($this->year, $this->month, $d);
                    } else {
                        $this->itemDTOs[$d] = new RangeLunchListingItemPdfDTO($this->year, $this->month, $d);
                    }
                    $this->itemDTOs[$d]->fillByListingItem($this->listingItems[$d]);
                } else {
                    $this->itemDTOs[$d] = new ListingItemPdfDTO($this->year, $this->month, $d);
                }
            }
        }

        return $this->itemDTOs[$day];
    }


    /**
     * @return bool
     */
    public function isHourlyRateDisplayed(): bool
    {
        return $this->displayHourlyRate;
    }


    public function displayHourlyRate()
    {
        $this->displayHourlyRate = true;
    }


    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }


    /**
     * @return int
     */
    public function getDaysInMonth(): int
    {
        return cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year);
    }


    /**
     * @return int
     */
    public function getWorkedDays(): int
    {
        return $this->workedDays;
    }


    /**
     * @return InvoiceTime
     */
    public function getWorkedHours(): InvoiceTime
    {
        return $this->workedHours;
    }


    /**
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }


    /**
     * @param int $year
     */
    public function setYear(int $year)
    {
        $this->year = $year;
    }


    /**
     * @return int
     */
    public function getMonth(): int
    {
        return $this->month;
    }


    /**
     * @param int $month
     */
    public function setMonth(int $month)
    {
        $this->month = $month;
    }


    /**
     * @return string|null
     */
    public function getEmployeeFullName()
    {
        return $this->employeeFullName;
    }


    /**
     * @param string|null $employeeFullName
     */
    public function setEmployeeFullName(string $employeeFullName = null)
    {
        $this->employeeFullName = $employeeFullName;
    }


    /**
     * @return null|string
     */
    public function getEmployerName()
    {
        return $this->employerName;
    }


    /**
     * @param null|string $employerName
     */
    public function setEmployerName(string $employerName = null)
    {
        $this->employerName = $employerName;
    }


    /**
     * @return int|null
     */
    public function getHourlyRate()
    {
        return $this->hourlyRate;
    }


    /**
     * @param int|null $hourlyRate
     */
    public function setHourlyRate(int $hourlyRate = null)
    {
        $this->hourlyRate = $hourlyRate;
    }


}