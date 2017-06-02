<?php declare(strict_types=1);

namespace Listings;

use Listings\Exceptions\Runtime\NegativeWorkedTimeException;
use Listings\Exceptions\Runtime\WorkedHoursRangeException;
use Listings\Exceptions\Runtime\LunchHoursRangeException;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Common\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping\JoinColumn;
use Listings\Utils\Time\ListingTime;
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
     * @param \DateTimeInterface|int|ListingTime|null|string $workStart
     * @param \DateTimeInterface|int|ListingTime|null|string $workEnd
     * @param \DateTimeInterface|int|ListingTime|null|string $lunchStart
     * @param \DateTimeInterface|int|ListingTime|null|string $lunchEnd
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

        $this->workStart = new ListingTime();
        $this->workEnd = new ListingTime();
        $this->lunch = new ListingTime();

        $this->setDay($day);
        $this->changeLocality($locality);
        $this->changeHours($workStart, $workEnd, $lunchStart, $lunchEnd);
    }


    /**
     * @param \DateTimeInterface|int|ListingTime|null|string $workStart
     * @param \DateTimeInterface|int|ListingTime|null|string $workEnd
     * @param \DateTimeInterface|int|ListingTime|null|string $lunchStart
     * @param \DateTimeInterface|int|ListingTime|null|string $lunchEnd
     * @throws WorkedHoursRangeException
     * @throws NegativeWorkedTimeException
     * @throws LunchHoursRangeException
     */
    public function changeHours($workStart, $workEnd, $lunchStart, $lunchEnd): void // todo
    {
        $this->workStart = new ListingTime($workStart);
        $this->workEnd = new ListingTime($workEnd);
        if ($this->workStart->compare($this->workEnd) === 1) {
            throw new WorkedHoursRangeException;
        }

        $this->lunchStart = new ListingTime($lunchStart);
        $this->lunchEnd = new ListingTime($lunchEnd);
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

        $originalWorkedHours = $this->getWorkedHours();

        $this->lunch = null;
        $this->workStart = $workStart;
        $this->workEnd = $workEnd;
        $this->lunchStart = $lunchStart;
        $this->lunchEnd = $lunchEnd;

        $newWorkedHours = $this->getWorkedHours();
        $diff = $newWorkedHours->sub($originalWorkedHours);

        $this->listing->updateWorkedHours(new Time($diff->getTime()));
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