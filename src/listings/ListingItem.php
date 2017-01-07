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
class ListingItem implements IListingItem
{
    use Identifier;
    use TListingItem;


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
        $this->workedHoursInSeconds = 0;

        $this->setDay($day);
        $this->changeLocality($locality);
        $this->changeHours($workStart, $workEnd, $lunch);
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
     * @return InvoiceTime
     */
    public function getLunch(): InvoiceTime
    {
        return $this->lunch;
    }


}