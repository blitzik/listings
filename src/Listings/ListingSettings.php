<?php declare(strict_types = 1);

namespace Listings;

use Listings\Exceptions\Runtime\WorkedHoursRangeException;
use Listings\Exceptions\Runtime\LunchHoursRangeException;
use Listings\Exceptions\Runtime\WorkedHoursException;
use Listings\Exceptions\Runtime\LunchHoursException;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Listings\Utils\Time\ListingTime;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Users\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="listing_settings")
 *
 */
class ListingSettings
{
    use Identifier;

    /**
     * @ORM\ManyToOne(targetEntity="Users\User")
     * @ORM\JoinColumn(name="user", referencedColumnName="id", nullable=false, unique=true)
     * @var \Users\User
     */
    private $user;

    /**
     * @ORM\Column(name="item_type", type="smallint", nullable=false, unique=false)
     * @var int
     */
    private $itemType;
    
    /**
     * @ORM\Column(name="work_start", type="listing_time", nullable=false, unique=false)
     * @var ListingTime
     */
    private $workStart;

    /**
     * @ORM\Column(name="work_end", type="listing_time", nullable=false, unique=false)
     * @var ListingTime
     */
    private $workEnd;

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
    

    public function __construct(
        User $user,
        int $itemType,
        ListingTime $workStart,
        ListingTime $workEnd,
        ListingTime $lunchStart,
        ListingTime $lunchEnd
    ) {
        $this->user = $user;

        $this->itemType = $itemType;

        $this->workStart = $workStart;
        $this->workEnd = $workEnd;
        $this->lunchStart = $lunchStart;
        $this->lunchEnd = $lunchEnd;
    }


    public function setItemType(int $itemType): void
    {
        $this->itemType = $itemType;
    }


    public function getItemType(): int
    {
        return $this->itemType;
    }


    public function changeHours($workStart, $workEnd, $lunchStart, $lunchEnd)
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

        $this->workStart = $workStart;
        $this->workEnd = $workEnd;
        $this->lunchStart = $lunchStart;
        $this->lunchEnd = $lunchEnd;
    }


    public function getWorkStart(): ListingTime
    {
        return $this->workStart;
    }


    public function getWorkEnd(): ListingTime
    {
        return $this->workEnd;
    }


    public function getLunchStart(): ListingTime
    {
        return $this->lunchStart;
    }


    public function getLunchEnd(): ListingTime
    {
        return $this->lunchEnd;
    }


    public function getWorkedHours(): ListingTime
    {
        return $this->workEnd->sub($this->workStart)->sub($this->getLunchHours());
    }


    public function getLunchHours(): ListingTime
    {
        return $this->lunchEnd->sub($this->lunchStart);
    }

}