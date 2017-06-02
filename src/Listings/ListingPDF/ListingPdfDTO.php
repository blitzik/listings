<?php declare(strict_types=1);

namespace Listings\Pdf;

use Listings\Exceptions\Logic\InvalidArgumentException;
use Listings\Utils\Time\ListingTime;
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

    /** @var ListingTime */
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
        $this->workedHours = new ListingTime();

        if (!array_key_exists($type, Listing::getTypes())) {
            throw new InvalidArgumentException;
        }

        $this->type = $type;
    }


    public function fillByListing(Listing $listing, array $listingItems = [])
    {
        $this->workedDays = 0;
        $this->workedHours = new ListingTime();
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


    public function getItemByDay(int $day): ListingItemPdfDTO
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


    public function isHourlyRateDisplayed(): bool
    {
        return $this->displayHourlyRate;
    }


    public function displayHourlyRate(): void
    {
        $this->displayHourlyRate = true;
    }


    public function getType(): int
    {
        return $this->type;
    }


    public function getDaysInMonth(): int
    {
        return cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year);
    }


    public function getWorkedDays(): int
    {
        return $this->workedDays;
    }


    public function getWorkedHours(): ListingTime
    {
        return $this->workedHours;
    }


    public function getYear(): int
    {
        return $this->year;
    }


    public function setYear(int $year): void
    {
        $this->year = $year;
    }


    public function getMonth(): int
    {
        return $this->month;
    }


    public function setMonth(int $month): void
    {
        $this->month = $month;
    }


    public function getEmployeeFullName(): ?string
    {
        return $this->employeeFullName;
    }

    public function setEmployeeFullName(?string $employeeFullName): void
    {
        $this->employeeFullName = $employeeFullName;
    }


    public function getEmployerName(): ?string
    {
        return $this->employerName;
    }

    
    public function setEmployerName(?string $employerName): void
    {
        $this->employerName = $employerName;
    }

    
    public function getHourlyRate(): ?int
    {
        return $this->hourlyRate;
    }

    
    public function setHourlyRate(?int $hourlyRate): void
    {
        $this->hourlyRate = $hourlyRate;
    }


}