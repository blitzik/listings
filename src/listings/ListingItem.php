<?php

declare(strict_types=1);

namespace Listings;

use Listings\Exceptions\Runtime\NegativeWorkedTimeException;
use Listings\Exceptions\Runtime\WorkedHoursException;
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
     * @throws WorkedHoursException
     * @throws WorkedHoursRangeException
     * @throws NegativeWorkedTimeException
     */
    public function changeHours($workStart, $workEnd, $lunch)
    {
        $workStart = new InvoiceTime($workStart);
        $workEnd = new InvoiceTime($workEnd);
        if ($workStart->compare('00:00') !== 0 or $workEnd->compare('00:00') !== 0) {
            if ($workStart->compare($workEnd) > 0) {
                throw new WorkedHoursRangeException;
            }

            if ($workEnd->sub($workStart)->compare('00:30') === -1) {
                throw new WorkedHoursException;
            }
        }

        $lunch = new InvoiceTime($lunch);
        $workedHoursWithLunch = $workEnd->sub($workStart);
        if ($workedHoursWithLunch->compare($lunch) < 0) { // must be $workedHoursWithLunch >= $_lunch
            throw new NegativeWorkedTimeException;
        }

        $this->workStart = $workStart;
        $this->workEnd = $workEnd;
        $this->lunch = $lunch;

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