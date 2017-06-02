<?php declare(strict_types=1);

namespace Listings;

use Listings\Exceptions\Runtime\NegativeWorkedTimeException;
use Listings\Exceptions\Runtime\WorkedHoursRangeException;
use Listings\Exceptions\Runtime\WorkedHoursException;
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
     * @ORM\Column(name="lunch", type="listing_time", nullable=false, unique=false)
     * @var ListingTime
     */
    private $lunch;


    /**
     * @param Listing $listing
     * @param int $day
     * @param string $locality
     * @param \DateTimeInterface|int|ListingTime|null|string $workStart
     * @param \DateTimeInterface|int|ListingTime|null|string $workEnd
     * @param \DateTimeInterface|int|ListingTime|null|string $lunch
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
        $this->listing->updateWorkedDays(1);

        $this->workStart = new ListingTime();
        $this->workEnd = new ListingTime();
        $this->lunch = new ListingTime();

        $this->setDay($day);
        $this->changeLocality($locality);
        $this->changeHours($workStart, $workEnd, $lunch);
    }


    /**
     * @param \DateTimeInterface|int|ListingTime|null|string $workStart
     * @param \DateTimeInterface|int|ListingTime|null|string $workEnd
     * @param \DateTimeInterface|int|ListingTime|null|string $lunch
     * @throws WorkedHoursException
     * @throws WorkedHoursRangeException
     * @throws NegativeWorkedTimeException
     */
    public function changeHours($workStart, $workEnd, $lunch): void
    {
        $workStart = new ListingTime($workStart);
        $workEnd = new ListingTime($workEnd);
        if ($workStart->compare('00:00') !== 0 or $workEnd->compare('00:00') !== 0) {
            if ($workStart->compare($workEnd) > 0) {
                throw new WorkedHoursRangeException;
            }

            if ($workEnd->sub($workStart)->compare('00:30') === -1) {
                throw new WorkedHoursException;
            }
        }

        $lunch = new ListingTime($lunch);
        $workedHoursWithLunch = $workEnd->sub($workStart);
        if ($workedHoursWithLunch->compare($lunch) < 0) { // must be $workedHoursWithLunch >= $_lunch
            throw new NegativeWorkedTimeException;
        }

        $originalWorkedHours = new Time($this->getWorkedHours()->getSeconds());

        $this->workStart = $workStart;
        $this->workEnd = $workEnd;
        $this->lunch = $lunch;

        $newWorkedHours = new Time($this->getWorkedHours()->getSeconds());
        $diff = $newWorkedHours->sub($originalWorkedHours);

        $this->listing->updateWorkedHours($diff);
    }


    public function getLunch(): ListingTime
    {
        return $this->lunch;
    }


}