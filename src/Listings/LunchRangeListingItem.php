<?php declare(strict_types=1);

namespace Listings;

use Listings\Exceptions\Runtime\NegativeWorkedTimeException;
use Listings\Exceptions\Runtime\WorkedHoursRangeException;
use Listings\Exceptions\Runtime\LunchHoursRangeException;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Common\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping\JoinColumn;
use Listings\Services\InvoiceTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="lunch_range_listing_item",
 *     uniqueConstraints={
 *         @UniqueConstraint(name="listing_day", columns={"listing", "day"})
 *     }
 * )
 *
 */
class LunchRangeListingItem implements IListingItem
{
    use Identifier;
    use TListingItem;


    /**
     * @ORM\Column(name="lunch_start", type="invoice_time", nullable=false, unique=false)
     * @var InvoiceTime
     */
    private $lunchStart;

    /**
     * @ORM\Column(name="lunch_end", type="invoice_time", nullable=false, unique=false)
     * @var InvoiceTime
     */
    private $lunchEnd;


    /** @var InvoiceTime */
    private $lunch;


    /**
     * @param Listing $listing
     * @param int $day
     * @param string $locality
     * @param \DateTimeInterface|int|InvoiceTime|null|string $workStart
     * @param \DateTimeInterface|int|InvoiceTime|null|string $workEnd
     * @param \DateTimeInterface|int|InvoiceTime|null|string $lunchStart
     * @param \DateTimeInterface|int|InvoiceTime|null|string $lunchEnd
     * @throws WorkedHoursRangeException
     * @throws NegativeWorkedTimeException
     * @throws LunchHoursRangeException
     */
    public function __construct(
        Listing $listing,
        int $day,
        string $locality,
        $workStart,
        $workEnd,
        $lunchStart,
        $lunchEnd
    ) {
        $this->id = $this->generateUuid();

        $this->listing = $listing;
        $this->workedHoursInSeconds = 0;

        $this->setDay($day);
        $this->changeLocality($locality);
        $this->changeHours($workStart, $workEnd, $lunchStart, $lunchEnd);
    }


    /**
     * @param \DateTimeInterface|int|InvoiceTime|null|string $workStart
     * @param \DateTimeInterface|int|InvoiceTime|null|string $workEnd
     * @param \DateTimeInterface|int|InvoiceTime|null|string $lunchStart
     * @param \DateTimeInterface|int|InvoiceTime|null|string $lunchEnd
     * @throws WorkedHoursRangeException
     * @throws NegativeWorkedTimeException
     * @throws LunchHoursRangeException
     */
    public function changeHours($workStart, $workEnd, $lunchStart, $lunchEnd)
    {
        $this->workStart = new InvoiceTime($workStart);
        $this->workEnd = new InvoiceTime($workEnd);
        if ($this->workStart->compare($this->workEnd) === 1) {
            throw new WorkedHoursRangeException;
        }

        $this->lunchStart = new InvoiceTime($lunchStart);
        $this->lunchEnd = new InvoiceTime($lunchEnd);
        if ($this->lunchStart->compare($this->lunchEnd) === 1 or
            $this->workStart->compare($this->lunchStart) === 1 or
            $this->workEnd->compare($this->lunchEnd) === -1
        ) {
            throw new LunchHoursRangeException;
        }

        $workedHoursWithLunch = $this->workEnd->sub($this->workStart);
        if ($workedHoursWithLunch->compare($this->getLunch()) < 0) { // must be $workedHoursWithLunch >= $_lunch
            throw new NegativeWorkedTimeException;
        }

        $this->lunch = null;
        $this->workStart = $workStart;
        $this->workEnd = $workEnd;
        $this->lunchStart = $lunchStart;
        $this->lunchEnd = $lunchEnd;

        $this->workedHoursInSeconds = (int)$this->getWorkedHours()->toSeconds();
    }


    /**
     * @return InvoiceTime
     */
    public function getLunchStart(): InvoiceTime
    {
        return $this->lunchStart;
    }


    /**
     * @return InvoiceTime
     */
    public function getLunchEnd(): InvoiceTime
    {
        return $this->lunchEnd;
    }


    /**
     * @return InvoiceTime
     */
    public function getLunch(): InvoiceTime
    {
        if ($this->lunch === null) {
            $this-> lunch = $this->lunchEnd->sub($this->lunchStart);
        }

        return $this->lunch;
    }


}