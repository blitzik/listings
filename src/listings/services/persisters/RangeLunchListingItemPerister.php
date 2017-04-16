<?php declare(strict_types=1);

namespace Listings\Services\Persisters;

use Listings\Exceptions\Runtime\NegativeWorkedTimeException;
use Listings\Exceptions\Runtime\WorkedHoursRangeException;
use Listings\LunchRangeListingItem;
use Kdyby\Doctrine\EntityManager;
use Nette\SmartObject;

class RangeLunchListingItemPersister
{
    use SmartObject;


    /** @var EntityManager */
    private $em;


    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }


    /**
     * @param array $values
     * @param LunchRangeListingItem|null $listingItem
     * @return LunchRangeListingItem
     * @throws WorkedHoursRangeException
     * @throws NegativeWorkedTimeException
     */
    public function save(array $values, LunchRangeListingItem $listingItem = null): LunchRangeListingItem
    {
        if ($listingItem !== null) {
            $newListingItem = $this->update($values, $listingItem);
        } else {
            $newListingItem = $this->create($values);
        }

        return $newListingItem;
    }


    /**
     * @param array $values
     * @return LunchRangeListingItem
     * @throws WorkedHoursRangeException
     * @throws NegativeWorkedTimeException
     */
    private function create(array $values): LunchRangeListingItem
    {
        $listingItem = new LunchRangeListingItem(
            $values['listing'],
            $values['day'],
            $values['locality'],
            $values['workStart'],
            $values['workEnd'],
            $values['lunchStart'],
            $values['lunchEnd']
        );
        $this->em->persist($listingItem)->flush();

        return $listingItem;
    }


    /**
     * @param array $values
     * @param LunchRangeListingItem $listingItem
     * @return LunchRangeListingItem
     * @throws WorkedHoursRangeException
     * @throws NegativeWorkedTimeException
     */
    private function update(array $values, LunchRangeListingItem $listingItem): LunchRangeListingItem
    {
        $listingItem->changeLocality($values['locality']);
        $listingItem->changeHours($values['workStart'], $values['workEnd'], $values['lunchStart'], $values['lunchEnd']);

        $this->em->flush();

        return $listingItem;
    }
}