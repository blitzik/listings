<?php

declare(strict_types = 1);

namespace Listings;

use Listings\Exceptions\Runtime\NegativeWorkedTimeException;
use Listings\Exceptions\Runtime\WorkedHoursRangeException;
use Listings\Exceptions\Runtime\WrongDayNumberException;
use App\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping\JoinColumn;
use Listings\Services\InvoiceTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Nette\Utils\Validators;

/**
 * @ORM\Entity
 * @ORM\Table(name="listing_item")
 *
 */
class ListingItem
{
    use Identifier;


    /**
     * @ORM\ManyToOne(targetEntity="Listing")
     * @ORM\JoinColumn(name="listing", referencedColumnName="id", nullable=false)
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
     * @param Listing $listing
     * @param int $day
     * @param string $locality
     * @param \DateTimeInterface|int|InvoiceTime|null|string $workStart
     * @param \DateTimeInterface|int|InvoiceTime|null|string $workEnd
     * @param \DateTimeInterface|int|InvoiceTime|null|string $lunch
     */
    public function __construct(
        Listing $listing,
        int $day,
        string $locality,
        $workStart,
        $workEnd,
        $lunch
    ) {
        $this->id = $this->getUuid();

        $this->listing = $listing;

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
     */
    public function changeHours($workStart, $workEnd, $lunch)
    {
        $this->workStart = new InvoiceTime($workStart);
        $this->workEnd = new InvoiceTime($workEnd);
        $workedHoursWithLunch = $this->workEnd->sub($this->workStart);
        if ($workedHoursWithLunch->compare($lunch) < 0) { // must be $workedHoursWithLunch >= $_lunch
            throw new NegativeWorkedTimeException;
        }

        $this->lunch = new InvoiceTime($lunch);
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
}