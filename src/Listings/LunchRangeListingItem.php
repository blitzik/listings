<?php declare(strict_types=1);

namespace Listings;

use Listings\Exceptions\Runtime\NegativeWorkedTimeException;
use Listings\Exceptions\Runtime\WorkedHoursRangeException;
use Listings\Exceptions\Runtime\LunchHoursRangeException;
use Listings\Exceptions\Logic\InvalidArgumentException;
use Listings\Exceptions\Runtime\WorkedHoursException;
use Listings\Exceptions\Runtime\LunchHoursException;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Doctrine\ORM\Mapping\JoinColumn;
use Listings\Utils\Time\ListingTime;
use Listings\Utils\TimeWithComma;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use blitzik\Utils\Time;

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
     * @ORM\Column(name="lunch_start", type="listing_time", nullable=false, unique=false)
     * @var ListingTime
     */
    private $lunchStart;

    /**
     * @ORM\Column(name="lunch_end", type="listing_time", nullable=false, unique=false)
     * @var ListingTime
     */
    private $lunchEnd;


    /** @var ListingTime */
    private $lunch;


    /**
     * @param Listing $listing
     * @param int $day
     * @param string $locality
     * @param \DateTimeInterface|int|ListingTime|TimeWithComma|null|string $workStart
     * @param \DateTimeInterface|int|ListingTime|TimeWithComma|null|string $workEnd
     * @param \DateTimeInterface|int|ListingTime|TimeWithComma|null|string $lunchStart
     * @param \DateTimeInterface|int|ListingTime|TimeWithComma|null|string $lunchEnd
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
        $this->listing = $listing;
        if ($listing->getItemsType() !== Listing::ITEM_TYPE_LUNCH_RANGE) {
            throw new InvalidArgumentException();
        }
        $this->listing->updateWorkedDays(1);

        $this->workStart = new ListingTime();
        $this->workEnd = new ListingTime();
        $this->lunchStart = new ListingTime();
        $this->lunchEnd = new ListingTime();
        $this->lunch = new ListingTime();

        $this->setDay($day);
        $this->changeLocality($locality);
        $this->changeHours($workStart, $workEnd, $lunchStart, $lunchEnd);
    }


    /**
     * @param \DateTimeInterface|int|ListingTime|TimeWithComma|null|string $workStart
     * @param \DateTimeInterface|int|ListingTime|TimeWithComma|null|string $workEnd
     * @param \DateTimeInterface|int|ListingTime|TimeWithComma|null|string $lunchStart
     * @param \DateTimeInterface|int|ListingTime|TimeWithComma|null|string $lunchEnd
     * @throws WorkedHoursRangeException
     * @throws WorkedHoursException
     * @throws LunchHoursRangeException
     * @throws LunchHoursException
     */
    public function changeHours($workStart, $workEnd, $lunchStart, $lunchEnd): void
    {
        $workStart = new ListingTime($workStart);
        $workEnd = new ListingTime($workEnd);
        $lunchStart = new ListingTime($lunchStart);
        $lunchEnd = new ListingTime($lunchEnd);

        if (!($workStart->isEqualTo('00:00') and $workEnd->isEqualTo('00:00'))) {
            if ($workStart->isBiggerThan($workEnd)) {
                throw new WorkedHoursRangeException;
            }
        }

        if (!($lunchStart->isEqualTo('00:00') and $lunchEnd->isEqualTo('00:00'))) {
            if ($lunchStart->isBiggerThan($lunchEnd)) {
                throw new LunchHoursRangeException;
            }

            if ($workStart->isBiggerThan($lunchStart) or $workEnd->isLowerThan($lunchEnd)) {
                throw new LunchHoursException;
            }

            $workedHours = $workEnd->sub($workStart)->sub($lunchEnd->sub($lunchStart));
            if ($workedHours->isLowerThan('00:30')) {
                throw new WorkedHoursException;
            }
        }

        $originalWorkedHours = new Time($this->getWorkedHours()->getSeconds());

        $this->lunch = $lunchEnd->sub($lunchStart);
        $this->workStart = $workStart;
        $this->workEnd = $workEnd;
        $this->lunchStart = $lunchStart;
        $this->lunchEnd = $lunchEnd;

        $newWorkedHours = new Time($this->getWorkedHours()->getSeconds());
        $diff = $newWorkedHours->sub($originalWorkedHours);

        $this->listing->updateWorkedHours($diff);
    }


    public function getWorkedHours(): ListingTime
    {
        return $this->workEnd->sub($this->workStart)->sub($this->lunchEnd->sub($this->lunchStart));
    }


    public function getLunchStart(): ListingTime
    {
        return $this->lunchStart;
    }


    public function getLunchEnd(): ListingTime
    {
        return $this->lunchEnd;
    }


    public function getLunch(): ListingTime
    {
        if ($this->lunch === null) {
            $this-> lunch = $this->lunchEnd->sub($this->lunchStart);
        }

        return $this->lunch;
    }


}